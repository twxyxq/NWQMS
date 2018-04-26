<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;


class cqcn_plan extends table_model
{
    //

    function column(){
        $this->item->col("cqcn_plan_title")->type("string")->name("计划名称");
        $this->item->col("cqcn_plan_comment")->type("string")->name("计划说明");
        $this->item->col("cqcn_plan_expire_date")->type("date")->name("截止日期");

    	$this->item->unique("cqcn_plan_title");
    }



    function cqcn_plan_del(){
        $this->table_data(array("id","cqcn_plan_title","cqcn_plan_expire_date","cqcn_plan_expire_date"));
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->orderby("cqcn_plan_expire_date","asc");
        //$this->data->add_button("查看","new_flavr",function($data){
            //return $data["qf_src"];
        //});
        return $this->data->render();
    }

    function cqcn_plan_list(){
        $this->table_data(array("id","cqcn_plan_title","cqcn_plan_expire_date","cqcn_plan_expire_date"));
        $this->data->orderby("cqcn_plan_expire_date","asc");
        $this->data->add_button("填报","new_flavr",function($data){
            if (\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($data["cqcn_plan_expire_date"]))) {
                return "";
            } else {
                return "/pp/cqcn_plan_item?plan_id=".$data["id"];
            }
        });
        //$this->data->add_button("查看","new_flavr",function($data){
            //return $data["qf_src"];
        //});
        return $this->data->render();
    }

}
