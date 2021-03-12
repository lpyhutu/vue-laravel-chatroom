<?php
/**
 * Created by IntelliJ IDEA.
 * User: hutu
 * Date: 2020/9/20
 * Time: 18:32
 */

namespace App\Common;


class UploadPicture
{
    /**
     * 图片上传
     * @param $base base64
     * @param $path 上传路径
     * @return string
     */
    public function uploadBase($base, $path)
    {
        for ($i = 0; $i < count($base); $i++) {
            $substr = substr(strstr($base[$i], ','), 1);
            $base64 = base64_decode($substr);
            $fileName = $path . date('YmdHis', time()) . rand(1000000, 9999999) . '.png';
            file_put_contents($fileName, $base64, FILE_APPEND);
        }
        return $fileName;
    }

    /**
     * 图片上传
     * @param $file 图片
     * @param $path 路径
     * @return string
     */
    public function uploadFile($file, $path)
    {
        $fileName = time() . '_' . rand(1, 100000) . '.' . strtolower($file->getClientOriginalExtension()) ?: 'png';
        $folderName = '/' . $path . "/" . date("Ym/d", time()) . "/";
        $file->move(public_path() . $folderName, $fileName);
        return $folderName . $fileName;
    }
}
