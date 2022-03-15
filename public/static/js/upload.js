(function(factory) {
  if (typeof exports === 'object') {
    // Node/CommonJS
    module.exports = factory();
  } else if (typeof define === 'function' && define.amd) {
    // AMD
    define(factory);
  } else {
      // Browser globals (with support for web workers)
    var glob;

    try {
        glob = window;
    } catch (e) {
        glob = self;
    }

    glob.Upload = factory();
  }
})(function() {
  "use strict";
  var Request = function(method, url, body = {}, header = {}) {
    this.state = 0;
    // xhr 对象
    this.xhr = new XMLHttpRequest();
    // 设置请求方法和地址
    this.xhr.open(method, url);
    // 添加请求头
    for(let key in header) {
      if (method.toUpperCase() == 'POST' && key.toLowerCase() == 'content-type' && header[key] == 'multipart/form-data') {
        continue;
      }
      this.xhr.setRequestHeader(key, header[key]);
    }
    // 响应回调
    this.onResponse = function (callback) {
      if (typeof callback !== 'function') {
        throw new Error('The first argument should be a function');
      }
      this.xhr.onreadystatechange = () => {
        this.state = this.xhr.readyState;
        if (this.xhr.readyState == 4) {
          callback(this.xhr.responseText, this.xhr.status);
        }
      }
      return this;
    }
    // 进度回调
    this.onProgress = function(callback) {
      if (typeof callback !== 'function') {
        throw new Error('The first argument should be a function');
      }
      this.xhr.upload.onprogress = function(e) {
        callback(e.loaded, e.total);
      }
      return this;
    }
    // 发送请求
    this.send = function() {
      if (typeof body == 'object' && method.toUpperCase() == 'POST' && (header['content-type'] == 'multipart/form-data' || header['content-type'] == undefined)) {
        let formData = new FormData();
        for(let key in body) {
          formData.append(key, body[key]);
        }
        this.xhr.send(formData);
      } else if (body) {
        this.xhr.send(body);
      } else {
        this.xhr.send();
      }
    }
    // 取消请求回调
    var abortCallback;
    this.onAbort = function(callback) {
      if (typeof callback !== 'function') {
        throw new Error('callback is not a funcation');
      }
      abortCallback = callback;
      return this;
    }
    // 取消请求
    this.abort = function () {
      this.xhr.abort();
      if (abortCallback) {
        abortCallback();
      }
    }
  }
  var Upload = function (host, bindDomain = false) {
    // 版本号
    this.version = '1.0.0';
    // 服务端地址
    this.host = host;
    // 服务端是否绑定域名
    this.bindDomain = bindDomain;
    // 计算MD5进度回调
    var md5ProgressCallback;
    // 开始上传回调
    var beginCallback;
    // 上传进度回调
    var progressCallback;
    // 上传结束回调
    var successCallback;
    // 上传失败回调
    var failCallback;
    // 最大任务数量
    this.maxTask = 8;
    // 任务列表
    var tasks = [];
    // 接口列表
    var apis = {
      info: ['POST', '/common', '/upload/info'],
      partInfo: ['POST', '/common', '/upload/partInfo'],
      partOptions: ['POST', '/common', '/upload/partOptions'],
      partComplate: ['POST', '/common', '/upload/partComplate'],
    };
    var blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice;
    // 设置计算MD5进度回调
    this.onMd5Progress = function (callback) {
      if (typeof callback !== 'function') {
        throw new Error('callback is not a funcation');
      }
      md5ProgressCallback = callback;
      return this;
    }
    // 设置开始上传回调
    this.onBegin = function(callback) {
      if (typeof callback !== 'function') {
        throw new Error('callback is not a funcation');
      }
      beginCallback = callback;
      return this;
    }
    // 设置上传进度回调
    this.onProgress = function (callback) {
      if (typeof callback !== 'function') {
        throw new Error('callback is not a funcation');
      }
      progressCallback = callback;
      return this;
    }
    // 设置上传成功回调
    this.onSuccess = function(callback) {
      if (typeof callback !== 'function') {
        throw new Error('callback is not a funcation');
      }
      successCallback = callback;
      return this;
    }
    // 设置失败回调
    this.onFail = function(callback) {
      if (typeof callback !== 'function') {
        throw new Error('callback is not a funcation');
      }
      failCallback = callback;
      return this;
    }
    // 获取接口
    var getApi = name => {
      return this.host + (this.bindDomain ? apis[name][1] : apis[name][1] + apis[name][2]);
    }
    // 调用回调函数
    var callback = function(callback, args = []) {
      if (callback) {
        return callback(...args);
      }
    }
    // 计算文件MD5值
    var calculateFileMd5 = function(file) {
      // 块大小 2M
      let chunkSize = 1024 * 1024 * 5;
      // 块数量
      let chunkNum = Math.ceil(file.size / chunkSize);
      // 当前块
      let currentChunk = 1;
      
      let spark = new SparkMD5.ArrayBuffer(),
        fileReader = new FileReader();
      
      return new Promise(function(resolve, reject) {
        let nextFunc = function() {
          let start = (currentChunk - 1) * chunkSize;
          let end = ((start + chunkSize) >= file.size) ? file.size : start + chunkSize;
          fileReader.readAsArrayBuffer(blobSlice.call(file, start, end));
        }
        fileReader.onload = function (e) {
          spark.append(e.target.result);
          callback(md5ProgressCallback, [currentChunk >= chunkNum ? file.size : currentChunk * chunkSize, file.size, file]);
          if (currentChunk >= chunkNum) {
            let md5 = spark.end();
            resolve(md5);
            return;
          }
          currentChunk ++;
          nextFunc();
        }
        fileReader.onerror = function() {
          reject();
        }
        nextFunc();
      });
    }
    // 上传文件
    this.save = function(file) {
      if (file instanceof File === false) {
        throw new Error('The first argument should be a File');
      }
      calculateFileMd5(file).then(md5 => {
        (new Request(apis.info[0], getApi('info'), {
          fileName: file.name,
          fileMd5: md5
        })).onResponse((responseText, status) => {
          if (status == 200) {
            let response = JSON.parse(responseText);
            if (response.code != 0) {
              callback(failCallback, [{code: response.code, message: response.message}]);
              return;
            }
            let options = response.data.map.options;
            if (!options) {
              if (endCallback) {
                callback(endCallback, [response.data.map.url, file]);
                return;
              }
            } else {
              let header = {}, body = {};
              options.header.forEach(item => {
                header[item.key] = item.value;
              });
              options.body.forEach(item => {
                body[item.key] = item.value;
              });
              body[options.file_key] = file
              callback(beginCallback, [file]);
              let request = new Request(options.method, options.server, body, header);
              request.onProgress((loaded, total) => {
                callback(progressCallback, [loaded, total]);
              }).onResponse((responseText, status) => {
                if (status == 200) {
                  callback(successCallback, [response.data.map.url]);
                } else {
                  callback(failCallback);
                }
              }).send();
              tasks.push(request);
            }
          } else {
            callback(failCallback);
          }
        }).send();
      }).catch(() => {
        callback(failCallback);
      })
    }
    // 切片上传
    this.partSave = function(file, async = true) {
      if (file instanceof File === false) {
        throw new Error('The first argument should be a File');
      }
      calculateFileMd5(file).then(md5 => {
        (new Request(apis.partInfo[0], getApi('partInfo'), {
          fileName: file.name,
          fileMd5: md5,
        })).onResponse((responseText, status) => {
          if (status == 200) {
            let response = JSON.parse(responseText);
            if (response.code !== 0) {
              callback(failCallback, [{code: response.code, message: response.message}]);
              return;
            }
            if (response.data.map.options == null) {
              callback(successCallback, [{url: response.data.map.url}]);
              return;
            }
            //! 切片上传
            let accessUrl = response.data.map.url;
            let partSize = Math.floor(response.data.map.options.part_size) * 1024 * 1024;
            let uploadId = response.data.map.options.upload_id;
            let partNum = Math.ceil(file.size / partSize);
             // 已申请参数切片数量
            let applyNum = 0;
            // 切片参数列表
            let partOptions = [];
            // 已完成切片信息
            let complatePart = {};
            // 正在进行上传的任务数
            let taskNum = 0;
            // 是否发生异常 
            let errorStatus = false;
            // 获取参数任务数量
            let getOptionsTaskNum = 0
            callback(beginCallback, [file]);
            let nextFunc = () => {
              if (errorStatus) return;
              if (taskNum >= this.maxTask) return;
              if (partOptions.length <= 4 && applyNum + 1 < partNum && getOptionsTaskNum == 0) {
                let startNumber = applyNum + 1;
                let endNumber = startNumber + 10 >= partNum ? partNum + 1 : startNumber + 10;
                (new Request(apis.partOptions[0], getApi('partOptions'), {
                  fileName: file.name,
                  fileMd5: md5,
                  uploadId: uploadId,
                  partNumbers: (Array.from({length: endNumber - startNumber}, (v,k) => startNumber + k)),
                })).onResponse((responseText, status) => {
                  if (status == 200) {
                    let response = JSON.parse(responseText);
                    if (response.code != 0) {
                      callback(failCallback, [{code: response.code, message: response.message}]);
                      return;
                    }
                    response.data.list.forEach(item => {
                      partOptions.push(item);
                      applyNum ++;
                    });
                    getOptionsTaskNum --;
                    nextFunc();
                  } else {
                    callback(failCallback, [{}]);
                  }
                }).send();
                getOptionsTaskNum ++;
              }
              if (!async && taskNum >= 1) return;
              if (partOptions.length > 0) {
                let options = partOptions.shift();
                let header = {};
                options.header.forEach(item => {
                  header[item.key] = item.value;
                });
                let start = (options.part_number - 1) *partSize;
                let end = start + partSize >= file.size ? file.size : start + partSize;
                let request = new Request(options.method, options.server, blobSlice.call(file, start, end), header);
                request.onProgress((loaded, total) => {
                  complatePart[options.part_number] = {complateSize: loaded, etag: null};
                  callback(progressCallback, [eval(Object.values(complatePart).map(item => item.complateSize).join('+')), file.size]);
                }).onResponse((responseText, status) => {
                  if (errorStatus || status != 200) {
                    errorStatus = true;
                    return;
                  }
                  taskNum --;
                  complatePart[options.part_number].etag = JSON.parse(responseText).etag || request.xhr.getResponseHeader('ETag');
                  if (taskNum == 0 && partOptions.length == 0 && applyNum == partNum) {
                    let parts = [];
                    for(let item in complatePart) {
                      parts.push({partNumber: parseInt(item), etag: complatePart[item].etag});
                    }
                    (new Request(apis.partComplate[0], getApi('partComplate'), JSON.stringify({
                      fileName: file.name,
                      fileMd5: md5,
                      uploadId: uploadId,
                      parts: parts,
                    }), {
                      'content-type': 'applaction/json'
                    })).onResponse((responseText, status) => {
                      if (status != 200) {
                        callback(failCallback, [{}]);
                        return;
                      }
                      let response = JSON.parse(responseText);
                      if (response.code != 0) {
                        callback(failCallback,[{code: response.code, message: response.message}])
                        return;
                      }
                      callback(successCallback, [accessUrl]);
                    }).send();
                  }
                  nextFunc();
                }).send();
                tasks.push(request);
                taskNum ++;
                nextFunc();
              }
            }
            nextFunc();
          } else {
            callback(failCallback);
          }
        }).send();
      }).catch(() => {
        callback(failCallback);
      })
    }
    // 中断上传
    this.abort = function() {
      tasks.forEach(item => {
        if (item.state != 4) {
          item.abort();
        }
      });
    }
  }
  return Upload;
});


(function (factory) {
  if (typeof exports === 'object') {
      // Node/CommonJS
      module.exports = factory();
  } else if (typeof define === 'function' && define.amd) {
      // AMD
      define(factory);
  } else {
      // Browser globals (with support for web workers)
      var glob;

      try {
          glob = window;
      } catch (e) {
          glob = self;
      }

      glob.SparkMD5 = factory();
  }
}(function (undefined) {

  'use strict';

  /*
   * Fastest md5 implementation around (JKM md5).
   * Credits: Joseph Myers
   *
   * @see http://www.myersdaily.org/joseph/javascript/md5-text.html
   * @see http://jsperf.com/md5-shootout/7
   */

  /* this function is much faster,
    so if possible we use it. Some IEs
    are the only ones I know of that
    need the idiotic second function,
    generated by an if clause.  */
  var add32 = function (a, b) {
      return (a + b) & 0xFFFFFFFF;
  },
      hex_chr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];


  function cmn(q, a, b, x, s, t) {
      a = add32(add32(a, q), add32(x, t));
      return add32((a << s) | (a >>> (32 - s)), b);
  }

  function md5cycle(x, k) {
      var a = x[0],
          b = x[1],
          c = x[2],
          d = x[3];

      a += (b & c | ~b & d) + k[0] - 680876936 | 0;
      a  = (a << 7 | a >>> 25) + b | 0;
      d += (a & b | ~a & c) + k[1] - 389564586 | 0;
      d  = (d << 12 | d >>> 20) + a | 0;
      c += (d & a | ~d & b) + k[2] + 606105819 | 0;
      c  = (c << 17 | c >>> 15) + d | 0;
      b += (c & d | ~c & a) + k[3] - 1044525330 | 0;
      b  = (b << 22 | b >>> 10) + c | 0;
      a += (b & c | ~b & d) + k[4] - 176418897 | 0;
      a  = (a << 7 | a >>> 25) + b | 0;
      d += (a & b | ~a & c) + k[5] + 1200080426 | 0;
      d  = (d << 12 | d >>> 20) + a | 0;
      c += (d & a | ~d & b) + k[6] - 1473231341 | 0;
      c  = (c << 17 | c >>> 15) + d | 0;
      b += (c & d | ~c & a) + k[7] - 45705983 | 0;
      b  = (b << 22 | b >>> 10) + c | 0;
      a += (b & c | ~b & d) + k[8] + 1770035416 | 0;
      a  = (a << 7 | a >>> 25) + b | 0;
      d += (a & b | ~a & c) + k[9] - 1958414417 | 0;
      d  = (d << 12 | d >>> 20) + a | 0;
      c += (d & a | ~d & b) + k[10] - 42063 | 0;
      c  = (c << 17 | c >>> 15) + d | 0;
      b += (c & d | ~c & a) + k[11] - 1990404162 | 0;
      b  = (b << 22 | b >>> 10) + c | 0;
      a += (b & c | ~b & d) + k[12] + 1804603682 | 0;
      a  = (a << 7 | a >>> 25) + b | 0;
      d += (a & b | ~a & c) + k[13] - 40341101 | 0;
      d  = (d << 12 | d >>> 20) + a | 0;
      c += (d & a | ~d & b) + k[14] - 1502002290 | 0;
      c  = (c << 17 | c >>> 15) + d | 0;
      b += (c & d | ~c & a) + k[15] + 1236535329 | 0;
      b  = (b << 22 | b >>> 10) + c | 0;

      a += (b & d | c & ~d) + k[1] - 165796510 | 0;
      a  = (a << 5 | a >>> 27) + b | 0;
      d += (a & c | b & ~c) + k[6] - 1069501632 | 0;
      d  = (d << 9 | d >>> 23) + a | 0;
      c += (d & b | a & ~b) + k[11] + 643717713 | 0;
      c  = (c << 14 | c >>> 18) + d | 0;
      b += (c & a | d & ~a) + k[0] - 373897302 | 0;
      b  = (b << 20 | b >>> 12) + c | 0;
      a += (b & d | c & ~d) + k[5] - 701558691 | 0;
      a  = (a << 5 | a >>> 27) + b | 0;
      d += (a & c | b & ~c) + k[10] + 38016083 | 0;
      d  = (d << 9 | d >>> 23) + a | 0;
      c += (d & b | a & ~b) + k[15] - 660478335 | 0;
      c  = (c << 14 | c >>> 18) + d | 0;
      b += (c & a | d & ~a) + k[4] - 405537848 | 0;
      b  = (b << 20 | b >>> 12) + c | 0;
      a += (b & d | c & ~d) + k[9] + 568446438 | 0;
      a  = (a << 5 | a >>> 27) + b | 0;
      d += (a & c | b & ~c) + k[14] - 1019803690 | 0;
      d  = (d << 9 | d >>> 23) + a | 0;
      c += (d & b | a & ~b) + k[3] - 187363961 | 0;
      c  = (c << 14 | c >>> 18) + d | 0;
      b += (c & a | d & ~a) + k[8] + 1163531501 | 0;
      b  = (b << 20 | b >>> 12) + c | 0;
      a += (b & d | c & ~d) + k[13] - 1444681467 | 0;
      a  = (a << 5 | a >>> 27) + b | 0;
      d += (a & c | b & ~c) + k[2] - 51403784 | 0;
      d  = (d << 9 | d >>> 23) + a | 0;
      c += (d & b | a & ~b) + k[7] + 1735328473 | 0;
      c  = (c << 14 | c >>> 18) + d | 0;
      b += (c & a | d & ~a) + k[12] - 1926607734 | 0;
      b  = (b << 20 | b >>> 12) + c | 0;

      a += (b ^ c ^ d) + k[5] - 378558 | 0;
      a  = (a << 4 | a >>> 28) + b | 0;
      d += (a ^ b ^ c) + k[8] - 2022574463 | 0;
      d  = (d << 11 | d >>> 21) + a | 0;
      c += (d ^ a ^ b) + k[11] + 1839030562 | 0;
      c  = (c << 16 | c >>> 16) + d | 0;
      b += (c ^ d ^ a) + k[14] - 35309556 | 0;
      b  = (b << 23 | b >>> 9) + c | 0;
      a += (b ^ c ^ d) + k[1] - 1530992060 | 0;
      a  = (a << 4 | a >>> 28) + b | 0;
      d += (a ^ b ^ c) + k[4] + 1272893353 | 0;
      d  = (d << 11 | d >>> 21) + a | 0;
      c += (d ^ a ^ b) + k[7] - 155497632 | 0;
      c  = (c << 16 | c >>> 16) + d | 0;
      b += (c ^ d ^ a) + k[10] - 1094730640 | 0;
      b  = (b << 23 | b >>> 9) + c | 0;
      a += (b ^ c ^ d) + k[13] + 681279174 | 0;
      a  = (a << 4 | a >>> 28) + b | 0;
      d += (a ^ b ^ c) + k[0] - 358537222 | 0;
      d  = (d << 11 | d >>> 21) + a | 0;
      c += (d ^ a ^ b) + k[3] - 722521979 | 0;
      c  = (c << 16 | c >>> 16) + d | 0;
      b += (c ^ d ^ a) + k[6] + 76029189 | 0;
      b  = (b << 23 | b >>> 9) + c | 0;
      a += (b ^ c ^ d) + k[9] - 640364487 | 0;
      a  = (a << 4 | a >>> 28) + b | 0;
      d += (a ^ b ^ c) + k[12] - 421815835 | 0;
      d  = (d << 11 | d >>> 21) + a | 0;
      c += (d ^ a ^ b) + k[15] + 530742520 | 0;
      c  = (c << 16 | c >>> 16) + d | 0;
      b += (c ^ d ^ a) + k[2] - 995338651 | 0;
      b  = (b << 23 | b >>> 9) + c | 0;

      a += (c ^ (b | ~d)) + k[0] - 198630844 | 0;
      a  = (a << 6 | a >>> 26) + b | 0;
      d += (b ^ (a | ~c)) + k[7] + 1126891415 | 0;
      d  = (d << 10 | d >>> 22) + a | 0;
      c += (a ^ (d | ~b)) + k[14] - 1416354905 | 0;
      c  = (c << 15 | c >>> 17) + d | 0;
      b += (d ^ (c | ~a)) + k[5] - 57434055 | 0;
      b  = (b << 21 |b >>> 11) + c | 0;
      a += (c ^ (b | ~d)) + k[12] + 1700485571 | 0;
      a  = (a << 6 | a >>> 26) + b | 0;
      d += (b ^ (a | ~c)) + k[3] - 1894986606 | 0;
      d  = (d << 10 | d >>> 22) + a | 0;
      c += (a ^ (d | ~b)) + k[10] - 1051523 | 0;
      c  = (c << 15 | c >>> 17) + d | 0;
      b += (d ^ (c | ~a)) + k[1] - 2054922799 | 0;
      b  = (b << 21 |b >>> 11) + c | 0;
      a += (c ^ (b | ~d)) + k[8] + 1873313359 | 0;
      a  = (a << 6 | a >>> 26) + b | 0;
      d += (b ^ (a | ~c)) + k[15] - 30611744 | 0;
      d  = (d << 10 | d >>> 22) + a | 0;
      c += (a ^ (d | ~b)) + k[6] - 1560198380 | 0;
      c  = (c << 15 | c >>> 17) + d | 0;
      b += (d ^ (c | ~a)) + k[13] + 1309151649 | 0;
      b  = (b << 21 |b >>> 11) + c | 0;
      a += (c ^ (b | ~d)) + k[4] - 145523070 | 0;
      a  = (a << 6 | a >>> 26) + b | 0;
      d += (b ^ (a | ~c)) + k[11] - 1120210379 | 0;
      d  = (d << 10 | d >>> 22) + a | 0;
      c += (a ^ (d | ~b)) + k[2] + 718787259 | 0;
      c  = (c << 15 | c >>> 17) + d | 0;
      b += (d ^ (c | ~a)) + k[9] - 343485551 | 0;
      b  = (b << 21 | b >>> 11) + c | 0;

      x[0] = a + x[0] | 0;
      x[1] = b + x[1] | 0;
      x[2] = c + x[2] | 0;
      x[3] = d + x[3] | 0;
  }

  function md5blk(s) {
      var md5blks = [],
          i; /* Andy King said do it this way. */

      for (i = 0; i < 64; i += 4) {
          md5blks[i >> 2] = s.charCodeAt(i) + (s.charCodeAt(i + 1) << 8) + (s.charCodeAt(i + 2) << 16) + (s.charCodeAt(i + 3) << 24);
      }
      return md5blks;
  }

  function md5blk_array(a) {
      var md5blks = [],
          i; /* Andy King said do it this way. */

      for (i = 0; i < 64; i += 4) {
          md5blks[i >> 2] = a[i] + (a[i + 1] << 8) + (a[i + 2] << 16) + (a[i + 3] << 24);
      }
      return md5blks;
  }

  function md51(s) {
      var n = s.length,
          state = [1732584193, -271733879, -1732584194, 271733878],
          i,
          length,
          tail,
          tmp,
          lo,
          hi;

      for (i = 64; i <= n; i += 64) {
          md5cycle(state, md5blk(s.substring(i - 64, i)));
      }
      s = s.substring(i - 64);
      length = s.length;
      tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
      for (i = 0; i < length; i += 1) {
          tail[i >> 2] |= s.charCodeAt(i) << ((i % 4) << 3);
      }
      tail[i >> 2] |= 0x80 << ((i % 4) << 3);
      if (i > 55) {
          md5cycle(state, tail);
          for (i = 0; i < 16; i += 1) {
              tail[i] = 0;
          }
      }

      // Beware that the final length might not fit in 32 bits so we take care of that
      tmp = n * 8;
      tmp = tmp.toString(16).match(/(.*?)(.{0,8})$/);
      lo = parseInt(tmp[2], 16);
      hi = parseInt(tmp[1], 16) || 0;

      tail[14] = lo;
      tail[15] = hi;

      md5cycle(state, tail);
      return state;
  }

  function md51_array(a) {
      var n = a.length,
          state = [1732584193, -271733879, -1732584194, 271733878],
          i,
          length,
          tail,
          tmp,
          lo,
          hi;

      for (i = 64; i <= n; i += 64) {
          md5cycle(state, md5blk_array(a.subarray(i - 64, i)));
      }

      // Not sure if it is a bug, however IE10 will always produce a sub array of length 1
      // containing the last element of the parent array if the sub array specified starts
      // beyond the length of the parent array - weird.
      // https://connect.microsoft.com/IE/feedback/details/771452/typed-array-subarray-issue
      a = (i - 64) < n ? a.subarray(i - 64) : new Uint8Array(0);

      length = a.length;
      tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
      for (i = 0; i < length; i += 1) {
          tail[i >> 2] |= a[i] << ((i % 4) << 3);
      }

      tail[i >> 2] |= 0x80 << ((i % 4) << 3);
      if (i > 55) {
          md5cycle(state, tail);
          for (i = 0; i < 16; i += 1) {
              tail[i] = 0;
          }
      }

      // Beware that the final length might not fit in 32 bits so we take care of that
      tmp = n * 8;
      tmp = tmp.toString(16).match(/(.*?)(.{0,8})$/);
      lo = parseInt(tmp[2], 16);
      hi = parseInt(tmp[1], 16) || 0;

      tail[14] = lo;
      tail[15] = hi;

      md5cycle(state, tail);

      return state;
  }

  function rhex(n) {
      var s = '',
          j;
      for (j = 0; j < 4; j += 1) {
          s += hex_chr[(n >> (j * 8 + 4)) & 0x0F] + hex_chr[(n >> (j * 8)) & 0x0F];
      }
      return s;
  }

  function hex(x) {
      var i;
      for (i = 0; i < x.length; i += 1) {
          x[i] = rhex(x[i]);
      }
      return x.join('');
  }

  // In some cases the fast add32 function cannot be used..
  if (hex(md51('hello')) !== '5d41402abc4b2a76b9719d911017c592') {
      add32 = function (x, y) {
          var lsw = (x & 0xFFFF) + (y & 0xFFFF),
              msw = (x >> 16) + (y >> 16) + (lsw >> 16);
          return (msw << 16) | (lsw & 0xFFFF);
      };
  }

  // ---------------------------------------------------

  /**
   * ArrayBuffer slice polyfill.
   *
   * @see https://github.com/ttaubert/node-arraybuffer-slice
   */

  if (typeof ArrayBuffer !== 'undefined' && !ArrayBuffer.prototype.slice) {
      (function () {
          function clamp(val, length) {
              val = (val | 0) || 0;

              if (val < 0) {
                  return Math.max(val + length, 0);
              }

              return Math.min(val, length);
          }

          ArrayBuffer.prototype.slice = function (from, to) {
              var length = this.byteLength,
                  begin = clamp(from, length),
                  end = length,
                  num,
                  target,
                  targetArray,
                  sourceArray;

              if (to !== undefined) {
                  end = clamp(to, length);
              }

              if (begin > end) {
                  return new ArrayBuffer(0);
              }

              num = end - begin;
              target = new ArrayBuffer(num);
              targetArray = new Uint8Array(target);

              sourceArray = new Uint8Array(this, begin, num);
              targetArray.set(sourceArray);

              return target;
          };
      })();
  }

  // ---------------------------------------------------

  /**
   * Helpers.
   */

  function toUtf8(str) {
      if (/[\u0080-\uFFFF]/.test(str)) {
          str = unescape(encodeURIComponent(str));
      }

      return str;
  }

  function utf8Str2ArrayBuffer(str, returnUInt8Array) {
      var length = str.length,
         buff = new ArrayBuffer(length),
         arr = new Uint8Array(buff),
         i;

      for (i = 0; i < length; i += 1) {
          arr[i] = str.charCodeAt(i);
      }

      return returnUInt8Array ? arr : buff;
  }

  function arrayBuffer2Utf8Str(buff) {
      return String.fromCharCode.apply(null, new Uint8Array(buff));
  }

  function concatenateArrayBuffers(first, second, returnUInt8Array) {
      var result = new Uint8Array(first.byteLength + second.byteLength);

      result.set(new Uint8Array(first));
      result.set(new Uint8Array(second), first.byteLength);

      return returnUInt8Array ? result : result.buffer;
  }

  function hexToBinaryString(hex) {
      var bytes = [],
          length = hex.length,
          x;

      for (x = 0; x < length - 1; x += 2) {
          bytes.push(parseInt(hex.substr(x, 2), 16));
      }

      return String.fromCharCode.apply(String, bytes);
  }

  // ---------------------------------------------------

  /**
   * SparkMD5 OOP implementation.
   *
   * Use this class to perform an incremental md5, otherwise use the
   * static methods instead.
   */

  function SparkMD5() {
      // call reset to init the instance
      this.reset();
  }

  /**
   * Appends a string.
   * A conversion will be applied if an utf8 string is detected.
   *
   * @param {String} str The string to be appended
   *
   * @return {SparkMD5} The instance itself
   */
  SparkMD5.prototype.append = function (str) {
      // Converts the string to utf8 bytes if necessary
      // Then append as binary
      this.appendBinary(toUtf8(str));

      return this;
  };

  /**
   * Appends a binary string.
   *
   * @param {String} contents The binary string to be appended
   *
   * @return {SparkMD5} The instance itself
   */
  SparkMD5.prototype.appendBinary = function (contents) {
      this._buff += contents;
      this._length += contents.length;

      var length = this._buff.length,
          i;

      for (i = 64; i <= length; i += 64) {
          md5cycle(this._hash, md5blk(this._buff.substring(i - 64, i)));
      }

      this._buff = this._buff.substring(i - 64);

      return this;
  };

  /**
   * Finishes the incremental computation, reseting the internal state and
   * returning the result.
   *
   * @param {Boolean} raw True to get the raw string, false to get the hex string
   *
   * @return {String} The result
   */
  SparkMD5.prototype.end = function (raw) {
      var buff = this._buff,
          length = buff.length,
          i,
          tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
          ret;

      for (i = 0; i < length; i += 1) {
          tail[i >> 2] |= buff.charCodeAt(i) << ((i % 4) << 3);
      }

      this._finish(tail, length);
      ret = hex(this._hash);

      if (raw) {
          ret = hexToBinaryString(ret);
      }

      this.reset();

      return ret;
  };

  /**
   * Resets the internal state of the computation.
   *
   * @return {SparkMD5} The instance itself
   */
  SparkMD5.prototype.reset = function () {
      this._buff = '';
      this._length = 0;
      this._hash = [1732584193, -271733879, -1732584194, 271733878];

      return this;
  };

  /**
   * Gets the internal state of the computation.
   *
   * @return {Object} The state
   */
  SparkMD5.prototype.getState = function () {
      return {
          buff: this._buff,
          length: this._length,
          hash: this._hash.slice()
      };
  };

  /**
   * Gets the internal state of the computation.
   *
   * @param {Object} state The state
   *
   * @return {SparkMD5} The instance itself
   */
  SparkMD5.prototype.setState = function (state) {
      this._buff = state.buff;
      this._length = state.length;
      this._hash = state.hash;

      return this;
  };

  /**
   * Releases memory used by the incremental buffer and other additional
   * resources. If you plan to use the instance again, use reset instead.
   */
  SparkMD5.prototype.destroy = function () {
      delete this._hash;
      delete this._buff;
      delete this._length;
  };

  /**
   * Finish the final calculation based on the tail.
   *
   * @param {Array}  tail   The tail (will be modified)
   * @param {Number} length The length of the remaining buffer
   */
  SparkMD5.prototype._finish = function (tail, length) {
      var i = length,
          tmp,
          lo,
          hi;

      tail[i >> 2] |= 0x80 << ((i % 4) << 3);
      if (i > 55) {
          md5cycle(this._hash, tail);
          for (i = 0; i < 16; i += 1) {
              tail[i] = 0;
          }
      }

      // Do the final computation based on the tail and length
      // Beware that the final length may not fit in 32 bits so we take care of that
      tmp = this._length * 8;
      tmp = tmp.toString(16).match(/(.*?)(.{0,8})$/);
      lo = parseInt(tmp[2], 16);
      hi = parseInt(tmp[1], 16) || 0;

      tail[14] = lo;
      tail[15] = hi;
      md5cycle(this._hash, tail);
  };

  /**
   * Performs the md5 hash on a string.
   * A conversion will be applied if utf8 string is detected.
   *
   * @param {String}  str The string
   * @param {Boolean} [raw] True to get the raw string, false to get the hex string
   *
   * @return {String} The result
   */
  SparkMD5.hash = function (str, raw) {
      // Converts the string to utf8 bytes if necessary
      // Then compute it using the binary function
      return SparkMD5.hashBinary(toUtf8(str), raw);
  };

  /**
   * Performs the md5 hash on a binary string.
   *
   * @param {String}  content The binary string
   * @param {Boolean} [raw]     True to get the raw string, false to get the hex string
   *
   * @return {String} The result
   */
  SparkMD5.hashBinary = function (content, raw) {
      var hash = md51(content),
          ret = hex(hash);

      return raw ? hexToBinaryString(ret) : ret;
  };

  // ---------------------------------------------------

  /**
   * SparkMD5 OOP implementation for array buffers.
   *
   * Use this class to perform an incremental md5 ONLY for array buffers.
   */
  SparkMD5.ArrayBuffer = function () {
      // call reset to init the instance
      this.reset();
  };

  /**
   * Appends an array buffer.
   *
   * @param {ArrayBuffer} arr The array to be appended
   *
   * @return {SparkMD5.ArrayBuffer} The instance itself
   */
  SparkMD5.ArrayBuffer.prototype.append = function (arr) {
      var buff = concatenateArrayBuffers(this._buff.buffer, arr, true),
          length = buff.length,
          i;

      this._length += arr.byteLength;

      for (i = 64; i <= length; i += 64) {
          md5cycle(this._hash, md5blk_array(buff.subarray(i - 64, i)));
      }

      this._buff = (i - 64) < length ? new Uint8Array(buff.buffer.slice(i - 64)) : new Uint8Array(0);

      return this;
  };

  /**
   * Finishes the incremental computation, reseting the internal state and
   * returning the result.
   *
   * @param {Boolean} raw True to get the raw string, false to get the hex string
   *
   * @return {String} The result
   */
  SparkMD5.ArrayBuffer.prototype.end = function (raw) {
      var buff = this._buff,
          length = buff.length,
          tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
          i,
          ret;

      for (i = 0; i < length; i += 1) {
          tail[i >> 2] |= buff[i] << ((i % 4) << 3);
      }

      this._finish(tail, length);
      ret = hex(this._hash);

      if (raw) {
          ret = hexToBinaryString(ret);
      }

      this.reset();

      return ret;
  };

  /**
   * Resets the internal state of the computation.
   *
   * @return {SparkMD5.ArrayBuffer} The instance itself
   */
  SparkMD5.ArrayBuffer.prototype.reset = function () {
      this._buff = new Uint8Array(0);
      this._length = 0;
      this._hash = [1732584193, -271733879, -1732584194, 271733878];

      return this;
  };

  /**
   * Gets the internal state of the computation.
   *
   * @return {Object} The state
   */
  SparkMD5.ArrayBuffer.prototype.getState = function () {
      var state = SparkMD5.prototype.getState.call(this);

      // Convert buffer to a string
      state.buff = arrayBuffer2Utf8Str(state.buff);

      return state;
  };

  /**
   * Gets the internal state of the computation.
   *
   * @param {Object} state The state
   *
   * @return {SparkMD5.ArrayBuffer} The instance itself
   */
  SparkMD5.ArrayBuffer.prototype.setState = function (state) {
      // Convert string to buffer
      state.buff = utf8Str2ArrayBuffer(state.buff, true);

      return SparkMD5.prototype.setState.call(this, state);
  };

  SparkMD5.ArrayBuffer.prototype.destroy = SparkMD5.prototype.destroy;

  SparkMD5.ArrayBuffer.prototype._finish = SparkMD5.prototype._finish;

  /**
   * Performs the md5 hash on an array buffer.
   *
   * @param {ArrayBuffer} arr The array buffer
   * @param {Boolean}     [raw] True to get the raw string, false to get the hex one
   *
   * @return {String} The result
   */
  SparkMD5.ArrayBuffer.hash = function (arr, raw) {
      var hash = md51_array(new Uint8Array(arr)),
          ret = hex(hash);

      return raw ? hexToBinaryString(ret) : ret;
  };

  return SparkMD5;
}));