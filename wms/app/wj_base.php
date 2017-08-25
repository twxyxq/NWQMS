<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;


require_once "table_model.php";

class wj_base extends table_model
{
    //

    function column(){

        $this->item->col("title")->type("string")->input("exec");
        $this->item->col("check_procedure")->type("integer")->def("0")->input("exec");
        $this->item->col("check_p")->type("integer")->def("0")->input("exec");
        $this->item->col("wj_id")->type("integer")->def("0")->input("exec");

        $this->item->col("valid")->type("string")->def("未验证")->input("exec");
        $this->item->col("notice")->type("string")->def("")->input("exec");
        $this->item->col("unvalided_cols")->type("string")->def("")->input("exec");

        $this->item->col("wj_type")->type("string")->def("管道");

        $this->item->col("project")->type("string");
        $this->item->col("drawing")->type("string");
        $this->item->col("ild")->type("string");
        $this->item->col("sys")->type("string");
        $this->item->col("pipeline")->type("string");
        $this->item->col("vnum")->type("string");
        $this->item->col("vcode")->type("string");
        $this->item->col("jtype")->type("string");
        $this->item->col("at")->type("string");
        $this->item->col("ath")->type("string");
        $this->item->col("upstream")->type("string");
        $this->item->col("ac")->type("string");
        $this->item->col("bt")->type("string");
        $this->item->col("bth")->type("string");
        $this->item->col("downstream")->type("string");
        $this->item->col("bc")->type("string");
        $this->item->col("temperature")->type("string");
        $this->item->col("pressure")->type("string");
        $this->item->col("ft")->type("string");
        
        $this->item->col("qid")->type("string");
        
        
        $this->item->col("medium")->type("string");

        $this->item->col("pressure_test")->type("string");

        $this->item->cal(array("ild","sys","pipeline","vnum"),"vcode",function($ild,$sys,$pipeline,$vnum){
            return $ild.$sys."-".$pipeline."-".$vnum;
        });

        $wj = new \App\wj();

        foreach ($this->items_init() as $item) {
            if (isset($wj->item->$item)) {
                $this->item->$item->name($wj->item->$item->name)->size($wj->item->$item->size)->restrict($this->item->$item->restrict);
                $this->item->$item->bind = $wj->item->$item->bind;
                $this->item->$item->bind_addition = $wj->item->$item->bind_addition;
            }
        }

    }

    //清单列表
    function wj_base_list(){
        $this->table_data(array("id","title","COUNT('id') as amount","IF(check_p=0,CONCAT('<button class=\"btn btn-success btn-small\" onclick=\"table_flavr(\\'/wj/wj_base_check?group=',title,'\\')\">审核</button>'),CONCAT('<button class=\"btn btn-default btn-small\" onclick=\"table_flavr(\\'/wj/wj_base_check?group=',title,'\\')\">查看</button>')) as check_status"));
        $this->data->groupby("title");
        $this->data->orderby("id","desc");
        return $this->data->render();
    }

    //清单审核
    function wj_base_item($para){
        $this->table_data($this->items_init(array("valid","notice"),array("unvalided_cols","check_p")));
        $this->data->where("title",$para);
        $const_value = "";
        $this->data->index(function($data,$model) use (&$const_value){
            if ($data["check_p"] > 0) {
                return "已导入";
            } else {
                if ($const_value == "") {
                    $const_value = array();
                }
                $valid = "验证通过";
                if ($data["valid"] == "未验证" || ($data["valid"] != "验证通过" && strlen($data["unvalided_cols"]) == 0)) {
                    $cols = $this->items_init();
                } else if (strlen($data["unvalided_cols"]) > 0){
                    $cols = multiple_to_array($data["unvalided_cols"]);
                } else {
                    //验证通过时执行
                    $cols = array();
                    if ($data["notice"] == "重复") {
                        $this->data->special["ild_".$data["id"]] = "title='重复录入'";
                        $this->data->special["sys_".$data["id"]] = "title='重复录入'";
                        $this->data->special["pipeline_".$data["id"]] = "title='重复录入'";
                        $this->data->special["vnum_".$data["id"]] = "title='重复录入'";
                        $this->data->special["notice_".$data["id"]] = "style='color:red'";
                    }
                    if ($data["notice"] == "已存在") {
                        $this->data->special["ild_".$data["id"]] = "title='焊口已存在'";
                        $this->data->special["sys_".$data["id"]] = "title='焊口已存在'";
                        $this->data->special["pipeline_".$data["id"]] = "title='焊口已存在'";
                        $this->data->special["vnum_".$data["id"]] = "title='焊口已存在'";
                        $this->data->special["notice_".$data["id"]] = "style='color:red'";
                    }
                }
                $unvalided_cols = "";
                if (sizeof($cols) > 0) {
                    foreach ($cols as $item) {
                        //先判断是否在已验证清单内
                        if (!isset($const_value[$item])) {
                            $const_value[$item] = array();
                        }
                        if (!in_array($data[$item],$const_value[$item])){
                            if(!$this->item->valid_value($item,$data[$item])){
                                $valid = "未通过";
                                $this->data->special[$item."_".$data["id"]] = "title='".$this->item->msg()."'";
                                $unvalided_cols .= "{".$item."}";
                            } else {
                                $const_value[$item][] = $data[$item];
                                if ($item == "ac") {
                                    $const_value["bc"][] = $data[$item];
                                }
                                if ($item == "bc") {
                                    $const_value["ac"][] = $data[$item];
                                }
                            }
                        }
                    }
                }
                //$unique_array = array("ild","sys","pipeline","vnum");
                /*
                if ($valid == "验证通过") {
                    $wj_unique = \App\wj::withoutGlobalScopes(array("avail"))->where("ild",$data["ild"])->where("sys",$data["sys"])->where("pipeline",$data["pipeline"])->where("vnum",$data["vnum"])->get();
                    if (sizeof($wj_unique) > 0) {
                        $valid = "未通过";
                        $this->data->special["ild_".$data["id"]] = "title='焊口已存在'";
                        $this->data->special["sys_".$data["id"]] = "title='焊口已存在'";
                        $this->data->special["pipeline_".$data["id"]] = "title='焊口已存在'";
                        $this->data->special["vnum_".$data["id"]] = "title='焊口已存在'";
                    }
                }*/
                if ($valid != $data["valid"]) {
                    DB::table("wj_base")->where("id",$data["id"])->update(["valid" => $valid,"unvalided_cols" => $unvalided_cols]);
                }
                if ($valid != "验证通过") {
                    return "<span style=\"display:inline-block\" title>".$valid."<a href=\"###\" onclick=\"dt_delete('wj_base',".$data["id"].")\">[删除]</a></span>";
                } else {
                    return $valid."<a href=\"###\" onclick=\"dt_delete('wj_base',".$data["id"].")\">[删除]</a>";
                }
            }           
        });
        $this->data->special_all = function($data){
            if ($data["check_p"] == 0){
                return "onclick='table_flavr(\"/console/dt_edit?model=wj_base&id=".$data["id"]."\")'";
            }
            return "";
        };
        return $this->data->render();
    }
}
