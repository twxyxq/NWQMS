<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class qualification extends table_model
{
    //

    function column(){
        $this->item->col("qf_type")->type("string")->name("证书类型");
        $this->item->col("qf_code")->type("string")->name("证书编号");
        $this->item->col("qf_src")->type("string")->name("数据源")->def("N/A");
        $this->item->col("qf_qcode")->type("string")->name("二维码")->def("N/A");
        $this->item->col("qf_pic")->type("string")->name("个人照片")->def("N/A");
        $this->item->col("qf_pidcard")->type("string")->name("个人证件");
        $this->item->col("qf_name")->type("string")->name("姓名");
        $this->item->col("qf_info")->type("string")->name("证书信息");
        $this->item->col("qf_institution")->type("string")->name("颁发机构");
        $this->item->col("qf_company")->type("string")->name("聘用单位");
    	$this->item->col("qf_date")->type("date")->name("颁发日期")->def("null");
    	$this->item->col("qf_expiration_date")->type("date")->name("截止日期")->def("null");
        $this->item->col("qf_range")->type("string")->name("有效期")->def("null")->input("exec");
        $this->item->col("qf_standard")->type("string")->name("标准");

    	$this->item->unique("qf_code","qf_src");
    }


    function haf(){
        $this->parent("核级焊工证","qf_type");
    }


    function qualification_del($para){
        $this->$para();
        $this->table_data(array("id","qf_code","qf_name","qf_company","qf_info","qf_expiration_date"));
        $this->data->add_del();
        return $this->data->render();
    }

    function qualification_no_valid($para){
        $this->$para();
        $this->table_data(array("id","qf_code","qf_name","qf_company","qf_info","qf_expiration_date"));
        $this->data->add_button("选择","wj_choose",function($data){return $data["id"];});
        return $this->data->render();
    }

}
