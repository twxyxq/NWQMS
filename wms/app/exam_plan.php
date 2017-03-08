<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class exam_plan extends table_model
{
    //

    function column(){

        $this->item->col("ep_code")->type("string")->name("分组名称")->input("exec");
        $this->item->col("ep_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("ep_wj_type")->type("string")->name("焊口类型")->input("exec");
        $this->item->col("ep_ild_sys")->type("string")->name("系统")->input("exec");
        $this->item->col("ep_pp")->type("string")->name("焊工")->input("exec");
        $this->item->col("ep_wps")->type("string")->name("工艺")->input("exec");

        $this->item->col("ep_wj_ids")->type("string")->name("焊口")->input("exec");
        $this->item->col("ep_wj_samples")->type("string")->name("抽样焊口")->input("exec");
        $this->item->col("ep_wj_addition_samples")->type("string")->name("加倍抽样焊口")->input("exec")->def("null");
        $this->item->col("ep_wj_another_samples")->type("string")->name("再次抽样焊口")->input("exec")->def("null");
        $this->item->col("ep_wj_count")->type("integer")->name("焊口数量")->input("exec")->def("0");
        $this->item->col("ep_wj_samples_count")->type("integer")->name("抽样数")->input("exec")->def("0");
        $this->item->col("ep_wj_all_samples_count")->type("integer")->name("全部抽样数")->input("exec")->def("0");
        $this->item->col("ep_weight")->type("decimal")->name("权重")->input("exec")->def("0");



    }



   

    function ep_list(){
        $this->table_data(array("id","ep_code","ep_method","ep_wj_type","ep_ild_sys","ep_pp","ep_wps","name","created_at"),"user");
        return $this->data->render();
    }
}
