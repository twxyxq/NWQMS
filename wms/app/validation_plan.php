<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class validation_plan extends table_model
{
    //

    function column(){
        $this->item->col("vp_title")->type("string")->name("标题");
        $this->item->col("vp_date")->type("string")->name("日期");
        $this->item->col("vp_examiner")->type("sting")->name("考官")->def("N/A");
        //$this->item->col("vp_qp")->type("integer")->name("质量计划")->def("0");
        $this->item->col("vp_proc")->type("string")->name("程序")->def("null");
        $this->item->col("vp_finish_date")->type("string")->name("完成日期")->input("exec");
        $this->item->col("vp_comment")->type("string")->name("说明")->def("N/A")->input("exec");

    }


    function haf_qf(){
        $this->parent("valid_col","qf_code");
    }


    function vp_del($para){
        $this->$para();
        $this->table_data(array("id","qf_code","qf_name","qf_company","qf_info","qf_expiration_date"));
        $this->data->add_del();
        return $this->data->render();
    }
}
