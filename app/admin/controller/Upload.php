<?php

namespace app\admin\controller;

use attribute\Action;
use attribute\Controller;
use response\Code;
use service\Storage;
use think\response\Json;
use think\Response;

#[Controller('上传文件')]
class Upload extends Basic
{
    #[Action('获取上传参数')]
    public function info()
    {
        $storage = Storage::instance();
        $res = $storage->info($this->request->post('fileName'), $this->request->post('fileMd5'));
        if ($res === false) {
            $this->error(Code::PARAM_ERROR, $storage->getError());
        }
        $this->returnMap($res);
    }

    #[Action('上传文件')]
    public function file()
    {
        /** @var \driver\storage\Local */
        $driver = Storage::instance()->storage('local');
        $res = $driver->saveFile();
        if ($res !== true) {
            return Json::create(['message' => $res], 'json')->code(400);
        } else {
            return Response::create();
        }
    }

    #[Action('获取切片上传参数')]
    public function partInfo()
    {
        $storage = Storage::instance();
        $res = $storage->partInfo($this->request->post('fileName'), $this->request->post('fileMd5'));
        if ($res === false) {
            $this->error(Code::PARAM_ERROR, $storage->getError());
        }
        $this->returnMap($res);
    }

    #[Action('获取切片参数')]
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

    #[Action('切片上传')]
    public function part()
    {
        $storage = Storage::instance();
        /** @var \driver\storage\Local */
        $driver = Storage::instance()->storage('local');
        $res = $storage->part();
        if (!is_array($res)) {
            return Response::create($storage->getError(), 'html', 400);
        }
        return Response::create()->code(204)->eTag($res['etag']);
    }

    #[Action('切片上传完成')]
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