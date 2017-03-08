<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class exam extends table_model
{
    //

    function column(){

    	$this->item->col("exam_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("exam_wj_id")->type("integer")->name("焊口ID")->input("exec");
        $this->item->col("exam_date")->type("date")->name("检验日期")->input("exec")->def("null");
        $this->item->col("exam_eps_id")->type("integer")->name("工艺卡")->input("exec")->def("0");
        $this->item->col("exam_plan_id")->type("integer")->name("检验计划")->input("exec")->def("0");
        $this->item->col("exam_sheet_id")->type("integer")->name("委托单")->input("exec")->def("0");
        $this->item->col("exam_report_id")->type("integer")->name("报告")->input("exec")->def("0");
        $this->item->col("exam_total")->type("integer")->name("总数")->input("exec")->def("0");
        $this->item->col("exam_unaccept")->type("integer")->name("不合格")->input("exec")->def("0");
        $this->item->col("exam_conclusion")->type("string")->name("结论")->input("exec")->def("null");
        $this->item->col("exam_r_id")->type("integer")->name("返修ID")->input("exec")->def("0");
        $this->item->col("exam_input_time")->type("datetime")->name("录入时间")->input("exec")->def("null");

        $this->item->col("exam_info_model")->type("string")->name("信息模板")->def("null");
        $this->item->col("exam_info_0")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_1")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_2")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_3")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_4")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_5")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_6")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_7")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_8")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_9")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_10")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_11")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_12")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_13")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_14")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_15")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_16")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_17")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_18")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_19")->type("string")->name("信息")->def("null");




    }

    function wj($builder){
        $builder->leftJoin('wj','wj.id',$this->get_table().".exam_wj_id");
        return $builder;
    }

    function exam_plan($builder){
        $builder->leftJoin('exam_plan','exam_plan.id',$this->get_table().".exam_plan_id");
        return $builder;
    }


    function no_sheet_list(){
        $this->table_data(array("id",SQL_VCODE,"exam_method","ep_code","ep_ild_sys","ep_pp","name","created_at"),array("user","wj","exam_plan"));
        $this->data->where("exam_sheet_id",0);
        $this->data->index(function($data,$model){
            return "<input type='checkbox' class='wj_no_sheet' value='".$data["id"]."'>";
        });
        return $this->data->render();
    }
}
