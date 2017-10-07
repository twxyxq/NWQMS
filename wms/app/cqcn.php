<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;


require_once "table_model.php";

class cqcn extends table_model
{
    //

    function column(){
        $this->item->col("cqcn_type")->type("string")->name("证书类型")->restrict("民用核安全设备","特种设备");
        $this->item->col("cqcn_code")->type("string")->name("证书编号");
        $this->item->col("cqcn_method")->type("string")->name("方法")->restrict("RT","UT","PT","MT","ET","LT","VT");
        $this->item->col("cqcn_level")->type("string")->name("等级")->restrict("Ⅰ","Ⅱ","Ⅲ");
        $this->item->col("cqcn_expire_date")->type("date")->name("有效期");

    	$this->item->unique("cqcn_code");
    }


    function cqcn_del(){
        $this->table_data(array("id","cqcn_type","cqcn_code","CONCAT(cqcn_method,' ',cqcn_level)","cqcn_expire_date"));
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->where("created_by",Auth::user()->id);
        $this->data->orderby("cqcn_expire_date","asc");
        //$this->data->add_button("查看","new_flavr",function($data){
            //return $data["qf_src"];
        //});
        return $this->data->render();
    }

    function cqcn_list(){
        $this->table_data(array("id","cqcn_type","cqcn_code","CONCAT(cqcn_method,' ',cqcn_level)","cqcn_expire_date"));
        $this->data->where("created_by",Auth::user()->id);
        $this->data->orderby("cqcn_expire_date","asc");
        //$this->data->add_button("查看","new_flavr",function($data){
            //return $data["qf_src"];
        //});
        return $this->data->render();
    }

    function cqcn_list_all(){
        $this->table_data(array("id","cqcn_type","CONCAT(cqcn_method,' ',cqcn_level)","name","cqcn_expire_date"),"user");
        //$this->data->where("created_by",Auth::user()->id);
        $this->data->orderby("cqcn_expire_date","asc");
        //$this->data->add_button("查看","new_flavr",function($data){
            //return $data["qf_src"];
        //});
        return $this->data->render();
    }

}
