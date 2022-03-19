<?php

namespace app\admin\controller;

use service\Storage;
use think\response\Json;
use service\Code;
use think\Response;

class Upload extends Basic
{
    /**
     * 获取上传参数
     */
    public function info()
    {
        $storage = Storage::instance();
        $res = $storage->info($this->request->post('fileName'), $this->request->post('fileMd5'));
        if ($res === false) {
            $this->error(Code::PARAM_ERROR, $storage->getError());
        }
        $this->returnMap($res);
    }

    /**
     * 上传文件
     */
    public function file()
    {
        $res = Storage::instance()->storage('local')->saveFile();
        if ($res !== true) {
            return Json::create(['message' => $res], 'json')->code(400);
        } else {
            return Response::create();
        }
    }

    /**
     * 获取切片上传参数
     */
    public function partInfo()
    {
        $storage = Storage::instance();
        $res = $storage->partInfo($this->request->post('fileName'), $this->request->post('fileMd5'));
        if ($res === false) {
            $this->error(Code::PARAM_ERROR, $storage->getError());
        }
        $this->returnMap($res);
    }

    /**
     * 获取单个切片参数
     */
    public function partOptions()
    {
        $storage = Storage::instance();
        $options = [];
        foreach (explode(',', $this->request->post('partNumbers')) as $partNumber) {
            $res = $storage->partOptions($this->request->post('fileName'), $this->request->post('fileMd5'), $this->request->post('uploadId'), (int)$partNumber);
            if ($res === false) {
                $this->error(Code::PARAM_ERROR, $storage->getError());
            }
            $options[] = $res;
        }
        $this->returnList($options);
    }

    /**
     * 切片上传
     */
    public function part()
    {
        $storage = Storage::instance();
        $res = $storage->storage('local')->part();
        if (!is_array($res)) {
            return Response::create($storage->getError(), 'html', 400);
        }
        return Response::create()->code(204)->eTag($res['etag']);
    }

    /**
     * 切片上传完成
     */
    public function partComplete()
    {
        $fileName = $this->request->post('fileName');
        $fileMd5 = $this->request->post('fileMd5');
        $uploadId = $this->request->post('uploadId');
        $parts = $this->request->post('parts');
        array_multisort(array_column($parts, 'partNumber'), SORT_ASC, $parts);
        $storage = Storage::instance();
        $res = $storage->partComplete($fileName, $fileMd5, $uploadId, $parts);
        if (!is_array($res)) {
            $this->error(Code::PARAM_ERROR, $storage->getError());
        } else {
            $this->returnMap($res);
        }
    }
}