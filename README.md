简易版聊天室
 ===
基于vue、laravel、mysql、redis、workman实现的聊天室，使用Email+Redis实现邮箱验证注册，已实现的基础功能有：

1、账号注册

2、搜索用户

3、添加好友

4、一对一聊天

5、群聊

功能不多，但基础的已实现，自己可以根据需求再做修改。

### 一、目录结构

```css
├─front//前台
│  ├─public
│  └─src
│      ├─assets
│      │  ├─api//接口
│      │  ├─img
│      │  ├─js
│      │  ├─scss
│      │  └─vuex
│      ├─components
│      │  └─template
│      └─router
├─sql//数据库
└─wss//api
    ├─.idea
    ├─app
    ├─bootstrap
    ├─config
    ├─database
    ├─public
    ├─resources
    ├─routes
    ├─storage
    ├─vendor
    └─.env//记得修改配置（数据库&邮箱&Redis）
```

### 二、前台

1、下载项目到本地后，初始化项目

```
npm install
```

2、修改request.js、main.js、home.vue的请求接口以及socket接口

### 三、后台

1、把sql目录下的chatroom.sql添加到数据库中  

2、修改.env.example为.env，并根据里面的注释修改配置  

3、开启socket服务，window系统直接点击目录下的start_for_win.bat文件，出现界面显示连接成功即可    

如果是linux系统下测试，修改app\Console\Commands\WorkermanCommand.php文件，把里面的注释取消掉，并把相对的注释并执行下面代码  

```
php artisan workman start --d
```