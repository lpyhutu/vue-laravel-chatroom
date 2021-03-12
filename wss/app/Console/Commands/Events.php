<?php

namespace App\Workerman;

use \GatewayWorker\Lib\Gateway;

class Events
{

    public static function onWorkerStart($businessWorker)
    {
    }

    public static function onConnect($client_id)
    {
    }

    public static function onWebSocketConnect($client_id, $data)
    {
    }

    //接收用户端发信息
    public static function onMessage($client_id, $message)
    {
        $res = json_decode($message);
        switch ($res->type) {
            case "login":
                Gateway::bindUid($client_id, $res->user_id);
                Gateway::joinGroup($client_id, $res->group);
                break;
            case "send_message":
                if (!Gateway::isUidOnline($res->friend_id)) {
                    Gateway::sendToUid($res->id, json_encode(
                        ["type" => "notOnline", "msg" => "该用户不在线！"]));
                } else {
                    Gateway::sendToUid($res->friend_id, $message);
                }
                break;
            case "send_group_message":
                Gateway::sendToGroup($res->group, $message);
                break;
            case "count_group_online":
                Gateway::sendToGroup($res->group, json_encode(
                    ["type" => "count_group_online", "data" => [
                        "online" => Gateway::getUidCountByGroup($res->group)
                    ]]));
                break;
        }
    }

    public static function onClose($client_id)
    {

    }
}
