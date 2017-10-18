<?php

namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use datatables;
use view;

use TencentYoutuyun\Youtu;
use TencentYoutuyun\Conf;

class wechat extends Controller
{
    public $options = array(
            'token'=> array(
                    '1000002' => 'EuzhA2oYY8xhqtUm5FQkxPD',
                    '1000007' => 'mMFozxVlljEFTcaN',
                ), //填写应用接口的Token
            'encodingAesKey'=> array(
                    '1000002' => 'eEqtApJ8UCNuEDeeBmask2EPbVMb7M1fxxaVspdwjhK',
                    '1000007' => 'hzCHQcLJ7319CWnVcAe3gvOy5YKqNGtNWJn5sWdfwld',
                ), //填写加密用的EncodingAESKey
            'appid'=>'ww87531fd7a21b0b82', //填写高级调用功能的app id
            'appsecret'=> array(
                    '1000002' => 'mjbYfBlZk4jQg2GBMLIcIL_m0Jhc2e3cCNwW_pJIER8',
                    '1000007' => '3ulZc40JLgPc1-Tg_SnrKkDeo7my3VvXFz7oKvmzWFA',
                ), //填写高级调用功能的密钥
            'agent'=> array(
                    '1000002' => 'cqcn',
                    '1000007' => 'radiation_gps',
                ), //应用的id
            'debug'=>false, //调试开关
            'logcallback'=>'logg' //调试输出方法，需要有一个string类型的参数
        );

    public $app = false;
    public $TimeStamp = false;
    public $Nonce = false;
    public $wxcpt = false;

    function load_app($AgentID){
        $this->app = new \JSSDK($this->options["appid"],$this->options["appsecret"][$AgentID],$AgentID);
    }

    function index(){
        if (isset($_GET["msg_signature"]) && isset($_GET["timestamp"]) && isset($_GET["nonce"]) && isset($_GET["echostr"])) {
            $this->validation();
        } else {
            try {
                $state = explode("?", $_GET["state"]);
                $redirect_url = $state[0];
                $exec_id = $state[1];
                if (Auth::check()) {
                    header("location:".$redirect_url);
                } else {
                    $app = new \JSSDK($this->options["appid"],$this->options["appsecret"][$exec_id],$exec_id);
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
                                header("location:".$redirect_url);
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
                                header("location:".$redirect_url);
                            }
                        }
                    } else {
                        die("获取user_ticket失败");
                    }
                }
            } catch (\Exception $e) {
                $error = new \App\error();
                $error->error = $e->getMessage();
                $error->created_by = 0;
                $error->save();
            }
        }
        
        
    }

    function show($page){
        if (method_exists($this, $page)) {
            $this->$page();
        }
    }

    function validation(){

        require_once('../common/wechat/WXBizMsgCrypt.php');

        foreach ($this->options["token"] as $key => $value) {
            $encodingAesKey = $this->options["encodingAesKey"][$key];
            $token = $this->options["token"][$key];

            // $sVerifyMsgSig = HttpUtils.ParseUrl("msg_signature");
            $sVerifyMsgSig = $_GET["msg_signature"];
            // $sVerifyTimeStamp = HttpUtils.ParseUrl("timestamp");
            $sVerifyTimeStamp = $_GET["timestamp"];
            // $sVerifyNonce = HttpUtils.ParseUrl("nonce");
            $sVerifyNonce = $_GET["nonce"];
            // $sVerifyEchoStr = HttpUtils.ParseUrl("echostr");
            $sVerifyEchoStr = $_GET["echostr"];

            // 需要返回的明文
            $sEchoStr = "";

            $wxcpt = new \WXBizMsgCrypt($token, $encodingAesKey, $this->options["appid"]);

            $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
            if ($errCode == 0) {
                echo $sEchoStr;
                break;
            }
        }
        
    }



    function store(){

        $sReqData = file_get_contents("php://input"); 

        $xml = new \DOMDocument();
        $xml->loadXML($sReqData);
        $AgentID = $xml->getElementsByTagName('AgentID')->item(0)->nodeValue;

        require_once('../common/wechat/WXBizMsgCrypt.php');

        $encodingAesKey = $this->options["encodingAesKey"][$AgentID];
        $token = $this->options["token"][$AgentID];
        $corpId = $this->options["appid"];
        
        $sVerifyMsgSig = $_GET["msg_signature"];
        $this->TimeStamp = $_GET["timestamp"];
        $this->Nonce = $_GET["nonce"];


        $this->wxcpt = new \WXBizMsgCrypt($token, $encodingAesKey, $corpId);

        
        $sMsg = "";  // 解析之后的明文
        $errCode = $this->wxcpt->DecryptMsg($sVerifyMsgSig, $this->TimeStamp, $this->Nonce, $sReqData, $sMsg);

        if ($errCode == 0) {


            //$this->app = new \JSSDK($this->options["appid"],$this->options["appsecret"][$AgentID],$AgentID);
            $this->load_app($AgentID);

            $this->{$this->options["agent"][$AgentID]}($sReqData, $sMsg);

        } else {
            print("ERR: " . $errCode . "\n\n");
        }
    }

    function cqcn($sReqData, $sMsg){
    
        $xml = new \DOMDocument();
        $xml->loadXML($sMsg);
        $owner = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;
        $type = $xml->getElementsByTagName('MsgType')->item(0)->nodeValue;
        

        $sRespData = "";

        /*
        if ($type == "event") {

            $event = $xml->getElementsByTagName('Event')->item(0)->nodeValue;

            if ($event == "enter_agent") {
                $msg_str = "欢迎使用！您可以执行以下操作：";
                $msg_str .= "\n1：选择菜单进入相应的功能";
                $msg_str .= "\n2：直接将无损检测证书拍照上传，可自动录入（测试功能）";

                $sRespData = "<xml>";
                $sRespData .= "<ToUserName><![CDATA[toUser]]></ToUserName>";
                $sRespData .= "<FromUserName><![CDATA[fromUser]]></FromUserName>";
                $sRespData .= "<CreateTime>".time()."</CreateTime>";
                $sRespData .= "<MsgType><![CDATA[text]]></MsgType>";
                $sRespData .= "<Content><![CDATA[".$msg_str."]]></Content>";
                $sRespData .= "</xml>";
            }

        } else 
        */

        if ($type == "text"){

            $content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;

           

            $msg_str = "您可以执行以下操作：";
            $msg_str .= "\n1：进入菜单录入证书";
            $msg_str .= "\n2：直接将无损检测证书拍照上传，可自动录入（测试功能）";

            $sRespData = "<xml>";
            $sRespData .= "<ToUserName><![CDATA[toUser]]></ToUserName>";
            $sRespData .= "<FromUserName><![CDATA[fromUser]]></FromUserName>";
            $sRespData .= "<CreateTime>".time()."</CreateTime>";
            $sRespData .= "<MsgType><![CDATA[text]]></MsgType>";
            $sRespData .= "<Content><![CDATA[".$msg_str."]]></Content>";
            $sRespData .= "</xml>";

        } else if ($type == "image"){


            try{


                $mediaid = $xml->getElementsByTagName('MediaId')->item(0)->nodeValue;
                $picurl = $xml->getElementsByTagName('PicUrl')->item(0)->nodeValue;
                $img_url = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=".$this->app->getAccessToken()."&media_id=".$mediaid;

                //$image_file = file_get_contents($img_url);
                //throw new \Exception(public_path("uploads/cqcn")."/".date("y-m-d-H-i-s")."-".$owner.".jpg");
                
                //file_put_contents(public_path("uploads/cqcn")."/".date("y-m-d-H-i-s")."-".$owner.".jpg",$image_file);
                //throw new \Exception(url("/wechat/put_file_from_url_content"));
                
                

                require('../common/TencentYoutuyun/Auth.php');
                require('../common/TencentYoutuyun/Conf.php');
                require('../common/TencentYoutuyun/Http.php');
                require('../common/TencentYoutuyun/Youtu.php');


                // 设置APP 鉴权信息 请在http://open.youtu.qq.com 创建应用

                $appid='10102129';
                $secretId='AKID8RYlmoXIk150veegz62lgQxHPLGzsp9m';
                $secretKey='fK0CE4hDXrlZBTkrlZR9Xnsl8q381jy2';
                $userid='1';


                Conf::setAppInfo($appid, $secretId, $secretKey, $userid,conf::API_YOUTU_END_POINT);

                //$uploadRet = YouTu::generalocr('test.jpg');

                $uploadRet = YouTu::generalocrurl($img_url);

                if ($uploadRet["errorcode"] != 0) {
                    $sRespData = "<xml>";
                    $sRespData .= "<ToUserName><![CDATA[toUser]]></ToUserName>";
                    $sRespData .= "<FromUserName><![CDATA[fromUser]]></FromUserName>";
                    $sRespData .= "<CreateTime>".time()."</CreateTime>";
                    $sRespData .= "<MsgType><![CDATA[text]]></MsgType>";
                    $sRespData .= "<Content><![CDATA[自动录入失败，请手工录入]]></Content>";
                    $sRespData .= "</xml>";
                } else {


               

                    $cqcn = new \App\cqcn();
                    

                    $cqcn->cqcn_type = "民用核安全设备";
                    $cqcn->cqcn_code = "N/A";
                    
                    $name = false;
                    
                    foreach ($uploadRet["items"] as $item) {
                        if (mb_substr($item["itemstring"],0,2) == "姓名") {
                            $name = trim(mb_substr($item["itemstring"],3));
                        } else if (mb_substr($item["itemstring"],0,4) == "证书编号") {
                            $cqcn->cqcn_code = trim(mb_substr($item["itemstring"],5));
                        } else if (mb_substr($item["itemstring"],0,4) == "有效期至") {
                            $cqcn->cqcn_expire_date = \Carbon\Carbon::parse(trim(str_replace("年","-",str_replace("月","-",str_replace("日","",mb_substr($item["itemstring"],5))))));
                        } else if (mb_substr($item["itemstring"],0,4) == "检验方法") {
                            if (strpos($item["itemstring"],"RT") !== false) {
                                $cqcn->cqcn_method = "RT";
                            } else if (strpos($item["itemstring"],"UT") !== false) {
                                $cqcn->cqcn_method = "UT";
                            } else if (strpos($item["itemstring"],"PT") !== false) {
                                $cqcn->cqcn_method = "PT";
                            } else if (strpos($item["itemstring"],"MT") !== false) {
                                $cqcn->cqcn_method = "MT";
                            } else if (strpos($item["itemstring"],"LT") !== false) {
                                $cqcn->cqcn_method = "LT";
                            } else if (strpos($item["itemstring"],"ET") !== false) {
                                $cqcn->cqcn_method = "ET";
                            } else if (strpos($item["itemstring"],"VT") !== false) {
                                $cqcn->cqcn_method = "VT";
                            } 
                        } else if (mb_substr($item["itemstring"],0,2) == "等级") {
                            if (strpos($item["itemstring"],"I") !== false || strpos($item["itemstring"],"Ⅰ") !== false) {
                                $cqcn->cqcn_level = "Ⅰ";
                            } else if (strpos($item["itemstring"],"II") !== false || strpos($item["itemstring"],"Ⅱ") !== false) {
                                $cqcn->cqcn_level = "Ⅱ";
                            } else if (strpos($item["itemstring"],"III") !== false || strpos($item["itemstring"],"Ⅲ") !== false) {
                                $cqcn->cqcn_level = "Ⅲ";
                            }
                        }
                    }

                    if (!Auth::check()) {
                        $user = \App\User::where("code",$owner)->get();
                        if (sizeof($user) == 0) {
                            throw new \Exception("无此用户，请先进入证书列表页面授权");
                        } else if ($name === false || $cqcn->cqcn_code == "N/A" || !isset($cqcn->cqcn_expire_date) || !isset($cqcn->cqcn_method) || !isset($cqcn->cqcn_level)) {
                            throw new \Exception("获取证书信息失败");
                        } else if ($name != $user[0]->name) {
                            throw new \Exception("证书所有者（".$name."）与当前用户（".$user[0]->name."）不一致");
                        }
                        Auth::loginUsingId($user[0]->id,true);
                    }
                    
                    if ($cqcn->save()) {
                        $uu = ($cqcn->cqcn_type??"")." ".($cqcn->cqcn_code??"")." ".($cqcn->cqcn_method??"")." ".($cqcn->cqcn_level??"")." ".($cqcn->cqcn_expire_date??"")." ".$name;
                        $sRespData = "
                        <xml>
                           <ToUserName><![CDATA[toUser]]></ToUserName>
                           <FromUserName><![CDATA[fromUser]]></FromUserName>
                           <CreateTime>".time()."</CreateTime>
                           <MsgType><![CDATA[news]]></MsgType>
                           <ArticleCount>1</ArticleCount>
                           <Articles>
                               <item>
                                   <Title><![CDATA[成功录入]]></Title> 
                                   <Description><![CDATA[证书信息：\n".$uu."]]></Description>
                                   <PicUrl><![CDATA[".$img_url."]]></PicUrl>
                               </item>
                           </Articles>
                        </xml>
                        ";

                        _sock(url("/wechat/put_file_from_url_content")."?url=".urlencode($img_url)."&path=cqcn&file_name=".$cqcn->id);

                    } else {
                        $sRespData = "<xml>";
                        $sRespData .= "<ToUserName><![CDATA[toUser]]></ToUserName>";
                        $sRespData .= "<FromUserName><![CDATA[fromUser]]></FromUserName>";
                        $sRespData .= "<CreateTime>".time()."</CreateTime>";
                        $sRespData .= "<MsgType><![CDATA[text]]></MsgType>";
                        $sRespData .= "<Content><![CDATA[自动录入失败：".$cqcn->msg."]]></Content>";
                        $sRespData .= "</xml>";

                        _sock(url("/wechat/put_file_from_url_content")."?url=".urlencode($img_url)."&path=cqcn&owner=".$owner);
                    }
                
                }

            } catch (\Exception $e) {
                $uu = $e->getMessage();
                $sRespData = "
                <xml>
                   <ToUserName><![CDATA[toUser]]></ToUserName>
                   <FromUserName><![CDATA[fromUser]]></FromUserName>
                   <CreateTime>".time()."</CreateTime>
                   <MsgType><![CDATA[news]]></MsgType>
                   <ArticleCount>1</ArticleCount>
                   <Articles>
                       <item>
                           <Title><![CDATA[录入失败]]></Title> 
                           <Description><![CDATA[错误信息：\n".$uu."]]></Description>
                           <PicUrl><![CDATA[".$img_url."]]></PicUrl>
                       </item>
                   </Articles>
                </xml>
                ";
            }
            
        }

        $sEncryptMsg = ""; //xml格式的密文
        $errCode = $this->wxcpt->EncryptMsg($sRespData, $this->TimeStamp, $this->Nonce, $sEncryptMsg);

        if ($errCode == 0) {
            // TODO:
            // 加密成功，企业需要将加密之后的sEncryptMsg返回
            // HttpUtils.SetResponce($sEncryptMsg);  //回复加密之后的密文
            
            echo $sEncryptMsg;
        } else {
            print("ERR: " . $errCode . "\n\n");
            // exit(-1);
        }
    }


    function put_file_from_url_content($url = false) {
        try{
            // 设置运行时间为无限制
            set_time_limit(0);
            if (isset($_GET["url"])) {
                $url = $_GET["url"];
            }
            $curl = curl_init();
            // 设置你需要抓取的URL
            curl_setopt($curl, CURLOPT_URL, $url);
            // 设置header
            curl_setopt($curl, CURLOPT_HEADER, 0);
            // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // 运行cURL，请求网页
            $file = curl_exec($curl);
            // 关闭URL请求
            curl_close($curl);
            // 将文件写入获得的数据
            if (isset($_GET["file_name"])) {
                $pure_name = $_GET["file_name"];
            } else if (isset($_GET["owner"])){
                $pure_name = date("y-m-d-H-i-s")."-".$_GET["owner"];
            } else {
                $pure_name = date("y-m-d-H-i-s");
            }
            
            $filename = public_path("uploads/".$_GET["path"])."/".$pure_name.".jpg";
            $write = fopen($filename, "w");
            if ($write == false) {
                return false;
                echo "false";
            }
            if (fwrite($write, $file) == false) {
                return false;
                echo "false";
            }
            if (fclose($write) == false) {
                return false;
                echo "false";
            }
            echo "success";
        } catch (\Exception $e) {
            $error = new \App\error();
            $error->error = $e->getMessage();
            $error->created_by = 0;
            $error->save();
            echo $e->getMessage();
        }
    }

    function download_wechat_img(){

        if (!isset($_GET["mediaid"]) || !isset($_GET["AgentID"]) || !isset($_GET["path"]) || !isset($_GET["file_name"])) {
            die("数据错误");
        } else {
            $this->load_app($_GET["AgentID"]);
            $img_url = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=".$this->app->getAccessToken()."&media_id=".$_GET["mediaid"];
            $this->put_file_from_url_content($img_url);
        }
    }



}
