<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class CheckAuth extends BaseMiddleware
{
    /**
     * token检测、刷新
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        //检测请求头是否带token
        if (!$this->auth->parser()->setRequest($request)->hasToken()) {
            return response()->json(["code" => 204, "msg" => "请携带TOKEN验证！"]);
        }
        try {
            //判断是否登陆
            if (!$this->auth->parseToken()->authenticate()) {
                return response()->json(["code" => 204, "msg" => "未登陆！"]);
            }
            return $next($request);
        } catch (JWTException $e) {
            return response()->json(["code" => 204, "msg" => "身份过期，请重新登陆！"]);
        }
    }
}
