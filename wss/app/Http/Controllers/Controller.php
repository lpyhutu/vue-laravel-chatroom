<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $CODE = [
        "TIP" => 1,
        "SUCCESS" => 200,
        "WARNING" => 201,
        "ERROR" => 202,
    ];

    protected $REGISTER = "CHATROOM:REGISTER:";

    /**
     * 请求成功
     * @param int $code 状态码
     * @param string $msg 提示
     * @param array $data 值
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($code = 200, $msg = "success", $data = [])
    {
        return $this->response($code, $msg, $data);
    }

    /**
     * 请求失败
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function fail($code = 202, $msg = "error", $data = [])
    {
        return $this->response($code, $msg, $data);
    }

    /**
     * 返回JSON数据
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($code = 200, $msg = "success", $data = [])
    {
        $res = [
            "code" => $code,
            "msg" => $msg,
            "data" => $data
        ];
        return response()->json($res);
    }

}
