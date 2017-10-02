<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class pp extends Controller
{
    
    function get_val(){
       /* 
        if (isset($_POST["val"])) {
            $match = array();
            $vv = preg_match("/(?<=t|T)[0-9.]+/", $_POST["val"], $match);
            dd($vv);
        }
        */
        $collection = \App\qualification::where("id",">=",180)->where("id","<",240)->get();
        foreach ($collection as $c) {

            $qlf = \App\qualification::find($c->id);

            $match = array();
            /*
            $vv = preg_match("/(?<=D)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_D = $vv?$match[0]:-1;
            
            $vv = preg_match("/(?<=t|T)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_t = $vv?$match[0]:-1;
            
            $vv = preg_match("/(?<=h)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_h = $vv?$match[0]:-1;
            
            $vv = preg_match("/(?<=α)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_α = $vv?$match[0]:-1;

            $vv = preg_match("/(?<=Z)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_Z = $vv?$match[0]:-1;
            */

            $vv = preg_match("/(?<=D)[0-9.]+\/[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_Dm = $vv?explode("/",$match[0])[1]:-1;

            $qlf->save();
        }
        echo "suc";
    }

    function pp_add(){
        $model = new \App\pp();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","pp@pp_del");
        $sview->title(array("操作","钢印号","姓名","性别","生日","进场","离场","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function pp_qrcode(){
        $sview = new datatables("pp/pp_qrcode","pp@pp_qrcode");
        $sview->title(array("操作","钢印号","姓名","性别","生日","进场","离场","时间"));
        return $sview;
    }

    function pp_qrcode_detail(){
        $sview = new datatables("pp/pp_qrcode_detail","pp@pp_qrcode");
        $sview->title(array("操作","钢印号","姓名","性别","生日","进场","离场","时间"));
        return $sview;
    }

    function pp_scan(){
        $qf = new \App\qualification();
        $qf->haf();
        $sview = new datatables("pp/scan_add",["url" => "/pp/scan_add"],"qualification@qualification_del","haf");
        $sview->title(array("操作","证书编号","姓名","聘用单位","证书信息","有效期"));
        return $sview;
    }

    function qf_range(){
        $qf = new \App\qualification();
        $qf->haf();
        $sview = new datatables("qualification/qualification","qualification@qualification_range","haf");
        $sview->title(array("操作","证书编号","姓名","聘用单位","证书信息","有效期","D","T","h","α","Z"));
        return $sview;
    }

    function qf_range_list(){
        $model = new \App\qf_range();
        $sview = new datatables("layouts/panel_table","qf_range@qf_range_list");
        $sview->title($model->titles_init("操作"));
        return $sview;
    }

    function qf_validation_plan(){
        $sview = new datatables("validation/validation_plan","qualification@qualification_no_valid","haf");
        $sview->title(array("操作","证书编号","姓名","聘用单位","证书信息","有效期"));
        $sview->option("info: false");
        $sview->option("length: 5");
        //$sview->option("lengthChange: false");
        $sview->option("lengthMenu: [ 5, 10, 20 ]");
        return $sview;
    }


    function cqcn_del(){
        $model = new \App\cqcn();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","cqcn@cqcn_del");
        $sview->title(array("操作","类型","证书编号","方法等级","过期时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function scan_add(){
        if (isset($_POST["code_input"])) {
            //$html = file_get_contents($_POST["str"]);
            //$img_pos = strpos($html,"img_tx");
            //$img_path = substr($html,0,$img_pos-6);
            //$img_start = strrpos($img_path,"src");
            //$img_path = substr($img_path,$img_start+5);
            $obj = new \App\htmlfetch\html_info($_POST["code_input"]);
            $obj->add_tag("img_tx")->tag_type("img");
            $obj->add_tag("img_erm")->tag_type("img");
            $obj->add_tag("lb_xm");
            $obj->add_tag("lb_sfz");
            $obj->add_tag("lb_pydw");
            $obj->add_tag("lb_zsbh");
            $obj->add_tag("lb_zsyxq");
            $obj->add_tag("lb_hgxmkshgxmdh");
            $value = $obj->get_value();

            $qlf = new \App\qualification();
            $qlf->qf_type = "核级焊工证";
            $qlf->qf_code = $value["lb_zsbh"];
            $qlf->qf_src = $_POST["code_input"];
            $qlf->qf_qcode = $value["img_erm"];
            $qlf->qf_pic = $value["img_tx"];
            $qlf->qf_name = $value["lb_xm"];
            $qlf->qf_pidcard = $value["lb_sfz"];
            $qlf->qf_company = $value["lb_pydw"];
            $qlf->qf_expiration_date = str_replace("年", "-", str_replace("月", "-", str_replace("日", "", $value["lb_zsyxq"])));
            $qlf->qf_info = $value["lb_hgxmkshgxmdh"];
           
            $qlf->qf_institution = "国家核安全局";
            $qlf->qf_standard = "HAF603";

            //获取D，t，h，α
            $match = array();
            
            $vv = preg_match("/(?<=D)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_D = $vv?$match[0]:-1;

            $vv = preg_match("/(?<=D)[0-9.]+\/[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_Dm = $vv?explode("/",$match[0])[1]:-1;
            
            $vv = preg_match("/(?<=t|T)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_t = $vv?$match[0]:-1;

            $vv = preg_match("/(?<=t|T)[0-9.]+\([0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_t1 = $vv?explode("(",$match[0])[1]:-1;

            $vv = preg_match("/(?<=t|T)[0-9.]+\([0-9.]+\/[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_t2 = $vv?explode("/",$match[0])[1]:-1;
            
            $vv = preg_match("/(?<=h)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_h = $vv?$match[0]:-1;
            
            $vv = preg_match("/(?<=α)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_α = $vv?$match[0]:-1;

            $vv = preg_match("/(?<=Z)[0-9.]+/", $qlf->qf_info, $match);
            $qlf->qf_Z = $vv?$match[0]:-1;
            

            if ($qlf->save()){
                $output = array(
                    "suc" => 1,
                    "content" => $value,
                    "msg" => "添加成功！"
                );
            } else {
                $output = array(
                    "suc" => -1,
                    "msg" => "添加失败！".$qlf->msg
                );
            }

            
        } else {
            $output = array(
                "suc" => -1,
                "content" => "",
                "msg" => "没有对象！"
            );
        }
        die(json_encode($output));
    }

    function range_save(){
        if (isset($_POST["name"])) {
            $qf_range = new \App\qf_range();
            $postdata = $_POST;
            unset($postdata["_token"]);
            unset($postdata["_method"]);
            foreach ($postdata as $key => $value) {
                $key_text = "qf_range_".$key;
                $qf_range->$key_text = $value;
            }
            if ($qf_range->save()) {
                $output = array(
                    "suc" => 1,
                    "msg" => "保存成功！"
                );
            } else {
                $output = array(
                    "suc" => -1,
                    "msg" => "保存失败！".$qf_range->msg
                );
            }
            die(json_encode($output));
        } else {
            die("数据错误");
        }
    }


}
