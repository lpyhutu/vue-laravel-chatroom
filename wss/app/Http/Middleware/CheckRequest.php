<?php

namespace App\Http\Middleware;

use Closure;

class CheckRequest
{

    const BLACK_LIST = "blog:front:request:black:list"; //频繁请求名单
    const LOCK_LIST = "blog:front:request:lock:list"; //防刷名单
    const USER_INCREMENT = "blog:front:request:ip:"; //监听请求次数

    public function handle($request, Closure $next)
    {
        $max_res = 15; //频繁请求最大数，超过即加入黑名单
        $time = 5; //5秒内请求数超20次触发防刷
        $limit = 20;
        $lock_time = 60; //倒计时；
        //获取频繁请求名单中该ip触发防刷的次数
        $redis = app('redis.connection');
        $ip = $request->ip();
        $res_num = $redis->zscore(self::BLACK_LIST, $ip);
        if ($res_num > $max_res) {
            return response(["code" => 40000]);
        } else {
            $start_time = $redis->zscore(self::LOCK_LIST, $ip); //获取触发防刷时的时间
            if (time() - $start_time < $lock_time) {
                //返回剩余时间
                return response(["code" => 400001, "time" => 60 - (time() - $lock_time)]);
            } else {
                $ip_value = $redis->get(self::USER_INCREMENT . $ip); //获取锁
                if ($ip_value) {
                    $redis->incr(self::USER_INCREMENT . $ip); //自增
                    if ($ip_value > $limit) {
                        //超过请求次数触发防刷
                        $redis->zadd(self::LOCK_LIST, time(), $ip);
                        $redis->sadd(self::LOCK_LIST, $ip);
                        $redis->expire(self::LOCK_LIST, 60 * 5);
                        //频繁请求名单次数自增
                        $back_num = $redis->zscore(self::BLACK_LIST, $ip);
                        $redis->zadd(self::BLACK_LIST, $back_num + 1, $ip);
                        $redis->sadd(self::BLACK_LIST, $ip);
                    }
                    return $next($request);
                } else {
                    $redis->setex(self::USER_INCREMENT . $ip, $time, 1); //设置锁
                    return $next($request);
                }
            }
        }

    }
}
