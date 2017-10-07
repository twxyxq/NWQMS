<?php

namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use datatables;
use view;

class wechat extends Controller
{
    public $options = array(
            'token'=>'tokenaccesskey', //填写应用接口的Token
            'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
            'appid'=>'ww87531fd7a21b0b82', //填写高级调用功能的app id
            'appsecret'=>'mjbYfBlZk4jQg2GBMLIcIL_m0Jhc2e3cCNwW_pJIER8', //填写高级调用功能的密钥
            'agentid'=>'1', //应用的id
            'debug'=>false, //调试开关
            'logcallback'=>'logg', //调试输出方法，需要有一个string类型的参数
        );

    function index(){
        $state = $_GET["state"];
        if (Auth::check()) {
            header("location:".$state);
        } else {
            $app = new \JSSDK($this->options["appid"],$this->options["appsecret"]);
            $access_token = $app->getAccessToken();
            if (isset($_GET["code"])) {
                $info = json_decode(file_get_contents("https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=".$access_token."&code=".$_GET["code"]));
            } else {
                die("获取code失败");
            }

            $exit = 0;

            if (isset($info->UserId) && isset($info->user_ticket)) {
                $user = \App\User::where("code",$info->UserId)->get();
                if (sizeof($user) == 1) {
                    Auth::loginUsingId($user[0]->id,true);
                    if(Auth::check()){
                        header("location:".$state);
                    } else {
                        die("登录失败");
                    }
                    
                } else {
                    $detail = json_decode($app->httpPost("https://qyapi.weixin.qq.com/cgi-bin/user/getuserdetail?access_token=".$access_token,'{"user_ticket":"'.$info->user_ticket.'"}'));
                    if (isset($detail->userid) && isset($detail->name) && isset($detail->mobile) && isset($detail->avatar) && isset($detail->email)) {
                        $password = substr(md5(time()),0,6);
                        $new_user = \App\User::create([
                            'code' => $detail->userid,
                            'name' => $detail->name,
                            'email' => $detail->email,
                            'password' => bcrypt($password),
                            'auth' => '{wechat}',
                            'default_key' => $password,
                            'mobile' => $detail->mobile,
                            'avatar' => $detail->avatar
                        ]);
                        Auth::loginUsingId($new_user->id, true);
                        header("location:".$state);
                    }
                }
            } else {
                die("获取user_ticket失败");
            }
        }
        
    }

    function photo(){
        
    }

    

}
