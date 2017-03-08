<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class exam_sheet extends table_model
{
    //

    function column(){

        $this->item->col("es_code")->type("string")->name("委托单编号")->input("exec");
        $this->item->col("es_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("es_wj_type")->type("string")->name("焊口类型")->input("exec");
        $this->item->col("es_ild_sys")->type("string")->name("系统")->input("exec");
        $this->item->col("es_demand_date")->type("date")->name("要求完成日期")->input("exec");
        $this->item->col("es_ep_ids")->type("string")->name("检验计划")->input("exec");
        $this->item->col("es_code_specify")->type("integer")->name("编号指定")->def("0")->input("exec");

        $this->item->unique("es_code");

    }



    function qp_del(){
        $this->table_data(array("id","version","qp_ild","qp_sys","qp_code","qp_name","name","created_at"),"user");
        $this->data->add_del();
        //$data->where(function($query) use ($para){
            //$query->where("setting_type",$para);
        //});
        return $this->data->render();
    }

    function qp_update(){
        $this->table_data(array("id","version","CONCAT(qp_ild,qp_sys)","qp_code","qp_name","name","created_at"),"user");
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->add_version_update();
        //$data->where(function($query) use ($para){
            //$query->where("setting_type",$para);
        //});
        return $this->data->render();
    }

    function ep_list(){
        $this->table_data(array("id","ep_code","ep_method","ep_wj_type","ep_sys","ep_pp","ep_wps","name","created_at"),"user");
        return $this->data->render();
    }
}
