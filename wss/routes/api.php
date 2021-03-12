<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Front'], function () {
    //测试
    Route::post('index', 'UserController@index');
    //token刷新
    Route::post('refreshToken', 'UserController@refreshToken')->middleware("refresh.jwt");
    //验证码URL
    Route::get('captcha', 'UserController@captcha');
    //登陆
    Route::post('login', 'UserController@login');
    //注册
    Route::post('register', 'UserController@register');
    //发送邮箱验证码
    Route::post('sendEmailCode', 'UserController@sendEmailCode');

    Route::group(["middleware" => "check.jwt"], function () {
        //退出当前用户
        Route::post('logout', 'UserController@logout');
        //获取用户信息
        Route::post('userInfo', 'UserController@userInfo');
        //上传头像
        Route::post('uploadAvatar', 'UserController@uploadAvatar');
        //获取好友
        Route::post('friends', 'UserController@friends');
        //搜索账号
        Route::post('searchEmail', 'UserController@searchEmail');
        //添加好友
        Route::post('applyFriend', 'UserController@applyFriend');
        //申请列表
        Route::post('getApplyFriend', 'UserController@getApplyFriend');
        //申请通过
        Route::post('agreeApplyFriend', 'UserController@agreeApplyFriend');

    });

});
