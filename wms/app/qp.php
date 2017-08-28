<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class qp extends table_model
{
    //

    function column(){

    	$this->item->col("qp_project")->type("string")->name("项目")->restrict(string_to_array(PROJECT));
        $this->item->col("qp_pipe_type")->type("string")->name("物项类型")->restrict("管道","电仪","油系统","结构")->def("管道");
        $this->item->col("qp_ild")->type("string")->name("机组")->restrict(string_to_array(ILD));
    	$this->item->col("qp_sys")->type("string")->name("系统");
    	$this->item->col("qp_code")->type("string")->name("编号");
    	$this->item->col("qp_name")->type("string")->name("名称");
    	$this->item->col("qp_open")->type("date")->name("开启")->def("null")->input("exec");
    	$this->item->col("qp_close")->type("date")->name("关闭")->def("null")->input("exec");
        $this->item->col("version")->type("default")->name("版本")->restrict(array("A","B","C","D","E","F"));
        $this->item->col("qp_proc_model")->type("string")->name("模板")->bind("qp_proc_model","id","qpm_name")->multiple();

        $this->item->unique("qp_code");


        //$this->item->lock(model_restrict::create(array("wj","qid")));

        $this->version_control();


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
        $this->table_data(array("id","qp.id as qp_id","version","CONCAT(qp_ild,qp_sys)","qp_code","qp_name","name","created_at"),"user");
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->add_version_update();
        $this->data->add_button("查看","new_flavr",function($data){
            return "/console/dt_edit?id=".$data["id"];
        });
        //$data->where(function($query) use ($para){
            //$query->where("setting_type",$para);
        //});
        return $this->data->render();
    }

    function qp_list(){
        $this->table_data(array("id","qp_ild","qp_sys","qp_code","qp_name","name","created_at"),"user");
        return $this->data->render();
    }
}
