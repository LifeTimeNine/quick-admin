<?php

namespace middleware;

use think\Response;

class Cors
{
    public function handle($request, \Closure $next)
    {
        $origin = $request->header('Origin');
        // 跨域请求设置
        header("Access-Control-Allow-Methods: GET,POST,PUT");
        header("Access-Control-Allow-Credentials:true");
        header("Access-Control-Allow-Origin:{$origin}");
        header("Access-Control-Allow-Headers:Content-Type,Lang,lang,Authorization,authorization");
        header("Access-Control-Expose-Headers:Etag,etag,Refresh-Token,refresh-token");
        if ($request->isOptions()) {
            return Response::create();
        } else {
            return $next($request);
        }
    }
}
