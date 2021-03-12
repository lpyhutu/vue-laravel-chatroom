<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class RefreshToken extends BaseMiddleware
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
            //刷新token
            usleep(1000*100);
            $token = $this->auth->refresh();
            $res = [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $this->auth->factory()->getTTL() * 60
            ];
            return response()->json(["code" => 1, "msg" => "刷新成功！", "data" => $res]);
        } catch (JWTException $e) {
            try {
                //刷新token
                usleep(1000*100);
                $token = $this->auth->refresh();
                $res = [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => $this->auth->factory()->getTTL() * 60
                ];
                return response()->json(["code" => 1, "msg" => "刷新成功！", "data" => $res]);
            } catch (JWTException $e) {
                //刷新过期，重新登陆
                return response()->json(["code" => 204, "msg" => "身份过期，请重新登陆！"]);
            }
        }
    }
}
