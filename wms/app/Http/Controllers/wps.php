<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class wps extends Controller
{
   

    function wps_add(){
        $model = new \App\wps();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table",["width" => "1800px"],"wps@wps_del");
        $sview->title($model->titles(array("操作","版本"),array("创建者","时间")));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function wps_proc(){
        $model = new \App\wps();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table",["width" => "1800px"],"wps@wps_proc");
        $sview->title($model->titles(array("操作","版本"),array("创建者","时间")));
        $sview->info("panel_body","工艺卡生效流程");
        return $sview;
    }

    function wps_list(){
        $model = new \App\wps();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table",["width" => "1800px"],"wps@wps_list");
        $sview->title($model->titles(array("操作","版本"),array("创建者","时间")));
        $sview->info("panel_body","工艺卡清单");
        return $sview;
    }

    //(POST)工艺卡匹配
    function wj_get_wps(){
        if (isset($_POST["wj_id"])) {

            $wj = \App\wj::find($_POST["wj_id"]);
            if (!isset($wj->id)) {
                die("没找到焊口");
            }
            //接头型式匹配
            $wps = \App\wps::where("wps_jtype",$wj->jtype);
            //管径厚度匹配
            if ($wj->wj_type != "管道") {
                //厚度取min，即厚度不相同只满足最小厚度
                $wps->where("wps_thickness_lower_limit","<=",min($wj->ath,$wj->bth));
                $wps->where(function($query) use ($wj){
                    $query->orWhere("wps_thickness_upper_limit",0);
                    $query->orWhere("wps_thickness_upper_limit",">=",min($wj->ath,$wj->bth));
                });
            } else {
                if ($wj->jtype == "对接") {
                    $wps->where("wps_diameter_lower_limit","<=",floatval(min($wj->at,$wj->bt)));
                    $wps->where(function($query) use ($wj){
                        $query->orWhere("wps_diameter_upper_limit",0);
                        $query->orWhere("wps_diameter_upper_limit",">=",floatval(max($wj->at,$wj->bt)));
                    });
                    //厚度取min，即厚度不相同只满足最小厚度
                    $wps->where("wps_thickness_lower_limit","<=",floatval(min($wj->ath,$wj->bth)));
                    $wps->where(function($query) use ($wj){
                        $query->orWhere("wps_thickness_upper_limit",0);
                        $query->orWhere("wps_thickness_upper_limit",">=",floatval(min($wj->ath,$wj->bth)));
                    });
                } else {
                    if ($wj->at > $wj->bt) {
                        $max = "at";
                        $min = "bt";
                        $max_h = "ath";
                        $min_h = "bth";
                    } else {
                        $max = "bt";
                        $min = "at";
                        $max_h = "bth";
                        $min_h = "ath";
                    }
                    $wps->where("wps_diameter_lower_limit","<=",floatval($wj->$min));
                    $wps->where(function($query) use ($wj,$min,$min_h){
                        $query->orWhere("wps_diameter_upper_limit",0);
                        $query->orWhere("wps_diameter_upper_limit",">=",floatval($wj->$min));
                    });
                    $wps->where("wps_thickness_lower_limit","<=",floatval($wj->$min_h));
                    $wps->where(function($query) use ($wj,$min,$min_h){
                        $query->orWhere("wps_thickness_upper_limit",0);
                        $query->orWhere("wps_thickness_upper_limit",">=",floatval($wj->$min_h));
                    });
                }
            }
            //材质匹配，特殊要求匹配
            $a_base = \App\setting::where("setting_type","basemetal")->where("setting_name",$wj->ac)->get()[0];
            $grade_a = $a_base->setting_r0;
            $special_a = $a_base->setting_r1;
            if ($wj->ac == $wj->bc) {
                $grade_b = $grade_a;
                $special_b = $special_a;
            } else {
                $b_base = \App\setting::where("setting_type","basemetal")->where("setting_name",$wj->bc)->get()[0];
                $grade_b = $b_base->setting_r0;
                $special_b = $b_base->setting_r1;
            }
            $wps->where(function($query) use ($grade_a,$grade_b){
                $query->orWhere(function($query) use ($grade_a,$grade_b){
                    $query->where("wps_base_metal_type_A","like","%{".$grade_a."}%");
                    $query->where("wps_base_metal_type_B","like","%{".$grade_b."}%");
                });
                $query->orWhere(function($query) use ($grade_a,$grade_b){
                    $query->where("wps_base_metal_type_A","like","%{".$grade_b."}%");
                    $query->where("wps_base_metal_type_B","like","%{".$grade_a."}%");
                });
            });
            if ($special_a != "无" && $special_a != null && strlen($special_a) > 0) {
                $wps->where("wps_limit",$special_a);
            }
            if ($special_b != "无" && $special_b != null && strlen($special_b) > 0 && $special_b != $special_a) {
                $wps->where("wps_limit",$special_b);
            }
            $wps_data = $wps->get()->toArray();

            if (sizeof($wps_data) > 0) {
                $r = array(
                        "suc" => 1,
                        "msg" => "操作成功",
                        "wps" => $wps_data
                    );
                die(json_encode($r));
            } else {
                $r = array(
                        "suc" => -1,
                        "msg" => "没有找到合适的工艺卡"
                    );
                die(json_encode($r));
            }


        }
    }

    


}
