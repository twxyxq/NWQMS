<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use OSS\OssClient;
use OSS\Core\OssException;

use datatables;
use view;

class interior_management extends Controller
{
    private $accessKeyId = "LTAIF1bKqaqonEIs";
    private $accessKeySecret = "5WK2L2KnNd0dwX4QpeIbbNbpAEDojH";
    private $endpoint = "oss-cn-shenzhen-internal.aliyuncs.com";
    private $bucket = "cme-csd";
   

    function account_book_list(){
    	$book = \App\account_book::all();
        $sview = new view("interior_management/account_book_list",["book" => $book]);
        return $sview;
    }

    function overtime_personal(){
        $sview = new datatables("interior_management/overtime_personal","overtime@overtime_personal");
        $sview->title(array("操作","日期","时长","审批状态"));
        return $sview;
    }

    function overtime_examine_and_approve(){
    	$book = \App\overtime::all();
        $sview = new view("interior_management/overtime",["book" => $book]);
        return $sview;
    }

    function overtime_statistic(){
    	$book = \App\overtime::all();
        $sview = new view("interior_management/overtime",["book" => $book]);
        return $sview;
    }

    function my_report(){
        $sview = new datatables("interior_management/my_report","work_report@my_report");
        $sview->title(array("操作","分类","标题","内容","重要性"));
        if(strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false){
            $sview->option("searching: false");
            $sview->option("lengthChange: false");
        }
        return $sview;
    }

    function current_report(){
        $report = \App\work_report::orderByRaw('FIELD(wr_type,"总体","核岛土建","核岛安装","常规岛")')->orderByRaw('FIELD(wr_level,"极高","高","中","普通","-")')->get();
        $sview = new view("interior_management/current_report",["report" => $report]);
        return $sview;
    }

    function all_photo(){
        $wechat_app = new \JSSDK(1000010);
        $info = json_decode(file_get_contents("https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=".$wechat_app->getAccessToken()."&userid=".Auth::user()->code));
        $department = end($info->department);
        $options = array(
            'delimiter' => "/",
            'prefix' => $department."/",
            'max-keys' => "",
            'marker' => 0,
        );
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $listObjectInfo = $ossClient->listObjects($this->bucket,$options);
            $objectList = $listObjectInfo->getObjectList();
            if (!empty($objectList)) {
                $sview = new view("interior_management/all_photo",["photos" => $objectList]);
                return $sview;
            } else {
                return "没有照片";
            }
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }

    function photo_manager(){
        $wechat_app = new \JSSDK(1000010);
        $info = json_decode(file_get_contents("https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=".$wechat_app->getAccessToken()."&userid=".Auth::user()->code));
        $department = end($info->department);
        $options = array(
            'delimiter' => "/",
            'prefix' => $department."/".Auth::user()->code,
            'max-keys' => "",
            'marker' => 0,
        );
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $listObjectInfo = $ossClient->listObjects($this->bucket,$options);
            $objectList = $listObjectInfo->getObjectList();
            if (!empty($objectList)) {
                $sview = new view("interior_management/photo_manager",["photos" => $objectList]);
                return $sview;
            } else {
                return "没有照片";
            }
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }

    function delete_photo(){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        try{
            $ossClient->deleteObject($this->bucket, $_POST["object"]);
            echo json_encode(array(
                    "suc" => 1,
                    "msg" => "删除成功"
                ));
        } catch(OssException $e) {
            die("删除失败");
        }
    }

    

}
