<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use \Illuminate\Mail\Message;
use App\Http\Controllers\Controller;
use Mews\Captcha\Captcha;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Common\UploadPicture;

class UserController extends Controller
{
    public function index()
    {
        return \App\Models\Friends::where("user_id", 16)
            ->with(["friendInfo" => function ($query) {
                return $query->select('*');
            }])
            ->get();
    }

    /**
     * 获取用户信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo()
    {
        try {
            $userInfo = auth()->user();
            return $this->ok($this->CODE["TIP"], "获取成功！", $userInfo);
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 用户登陆
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $arr = $request->all();
            if (empty($arr["captcha"])) {
                return $this->fail($this->CODE["WARNING"], "验证码不能为空！");
            }
            if (!captcha_api_check($arr["captcha"], $arr["captchaKey"])) {
                return $this->fail($this->CODE["WARNING"], "验证码错误！");
            }
            $credentials = ["email" => $arr["email"], "password" => $arr["password"]];
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->ok($this->CODE["ERROR"], "账号或密码错误！");
            }
            $res = [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ];
            return $this->ok($this->CODE["SUCCESS"], "登陆成功！", $res);
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 退出登陆
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            auth()->logout();
            return $this->ok($this->CODE["SUCCESS"], "退出登陆！");
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * token刷新
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        return $this->ok($this->CODE["SUCCESS"], "刷新成功！");
    }

    /**
     * 注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $arr = $request->all();
            $redis = Redis::connection("hutu");
            //查询验证码是否过期
            if (!$redis->exists($this->REGISTER . $arr["email"])) {
                return $this->fail($this->CODE["WARNING"], "验证码已过期！");
            }
            //匹对验证码
            if ($arr["code"] != $redis->get($this->REGISTER . $arr["email"])) {
                return $this->fail($this->CODE["WARNING"], "验证码错误！");
            }
            //查询该邮箱是否被注册
            $data = \App\Models\User::where("email", $arr["email"])->first();
            if ($data) {
                return $this->fail($this->CODE["WARNING"], "该账号已注册！");
            }
            //加密
            $arr["password"] = Hash::make($arr["password"]);
            $ob = \App\Models\User::create($arr);
            if ($ob) {
                return $this->ok($this->CODE["SUCCESS"], "注册成功！");
            }
            return $this->fail($this->CODE["ERROR"], "注册失败！");
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 发送邮箱和验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailCode(Request $request)
    {
        try {
            $arr = $request->all();
            $redis = Redis::connection("hutu");
            $code = rand(100000, 999999);
            $redis->setex($this->REGISTER . $arr["email"], 60 * 3, $code);
            //发送邮箱
            Mail::send("mail.SendEmailCode", ["code" => $code, "email" => $arr["email"]], function (Message $message) use ($arr) {
                $message->to($arr["email"]);
                $message->subject("糊涂聊天室——用户注册");
            });
            if (Mail::failures()) {
                return $this->fail($this->CODE["WARNING"], "发送失败！");
            }
            return $this->ok($this->CODE["SUCCESS"], "发送成功，请登陆邮箱接收验证码！");
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 验证码URL
     * @param Captcha $captcha
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function captcha(Captcha $captcha)
    {
        return response()->json([
            $captcha->create('flat', true)
        ]);
    }

    /**
     * 修改头像
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $file = $request->file("file");
            $uid = $request->input("uid");
            //查看用户是否存在
            $ob = \App\Models\User::where("id", $uid)->first();
            if (!$ob) {
                return $this->fail($this->CODE["WARNING"], "用户不存在！");
            }
            //获取头像路径
            $up = new UploadPicture();
            $fileName = $up->uploadFile($file, "img/avatar");
            $delFileName = $ob->avatar;
            $ob->avatar = $fileName;
            $ob->save();
            if (!$ob) {
                return $this->fail($this->CODE["ERROR"], "上传失败！");
            }
            //删除原头像
            $path = public_path($delFileName);
            unlink($path);
            return $this->ok($this->CODE["SUCCESS"], "修改成功！");
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 获取好友
     * @return \Illuminate\Http\JsonResponse
     */
    public function friends(Request $request)
    {
        try {
            $user_id = $request->get("user_id");
            $ob = \App\Models\Friends::where(["user_id" => $user_id, "state" => 1])
                ->with(["friendInfo" => function ($query) {
                    return $query->select('*');
                }])
                ->get();
            if (!$ob) {
                return $this->fail($this->CODE["WARNING"], "空空如也");
            }
            return $this->ok($this->CODE["TIP"], "获取成功！", $ob);
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 搜索账号
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchEmail(Request $request)
    {
        try {
            $email = $request->get("email");
            $ob = \App\Models\User::where("email", $email)->get();
            if (!$ob) {
                return $this->fail($this->CODE["WARNING"], "未搜索到该用户！");
            }
            return $this->ok($this->CODE["TIP"], "获取成功！", $ob);
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 添加好友
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyFriend(Request $request)
    {
        try {
            $arr = $request->all();
            //确认是否添加
            $isFriend = \App\Models\Friends::where(
                ["user_id" => $arr["user_id"], "friend_id" => $arr["friend_id"]]
            )->first();
            if ($isFriend) {
                //确认是否通过
                if ($isFriend->state === 2) {
                    return $this->fail($this->CODE["WARNING"], "已申请添加该用户，等待对方通过！");
                }
                return $this->fail($this->CODE["WARNING"], "已添加该用户为好友！");
            }
            //添加到申请
            $ob = \App\Models\Friends::create(["user_id" => $arr["user_id"], "friend_id" => $arr["friend_id"]]);
            if ($ob) {
                return $this->ok($this->CODE["SUCCESS"], "添加成功，等待对方通过！");
            }
            return $this->fail($this->CODE["ERROR"], "添加失败！");
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    /**
     * 申请列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplyFriend(Request $request)
    {
        try {
            $friend_id = $request->get("friend_id");
            $ob = \App\Models\ApplyFriends::where(["friend_id" => $friend_id, "state" => 2])
                ->with(["friendInfo" => function ($query) {
                    return $query->select('*');
                }])
                ->get();
            if ($ob->isEmpty()) {
                return $this->fail($this->CODE["TIP"], "空空如也！");
            }
            return $this->ok($this->CODE["TIP"], "获取成功！", $ob);
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

    public function agreeApplyFriend(Request $request)
    {
        try {
            $arr = $request->all();
            $isApply = \App\Models\Friends::where(
                ["user_id" => $arr["user_id"], "friend_id" => $arr["friend_id"], "state" => 2])->first();
            if (!$isApply) {
                return $this->fail($this->CODE["WARNING"], "未申请该用户为好友！");
            }
            $isApply->state = 1;
            $isApply->save();
            $ob = \App\Models\Friends::create(
                ["user_id" => $arr["friend_id"], "friend_id" => $arr["user_id"], "state" => 1]);
            if ($ob) {
                return $this->fail($this->CODE["SUCCESS"], "申请通过，添加成功！");
            }
            return $this->fail($this->CODE["ERROR"], "添加失败！");
        } catch (\Exception $e) {
            return $this->fail($this->CODE["ERROR"], "系统繁忙！");
        }
    }

}
