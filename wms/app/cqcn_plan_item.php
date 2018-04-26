<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;



class cqcn_plan_item extends table_model
{
    public $parent_lock = true;

    public $parent_name = "cqcn_plan";

    public $child_col = "cqcn_plan_item_plan_id";

    function column(){
        $this->item->col("cqcn_plan_item_type")->type("string")->name("证书类型")->restrict("民用核安全设备","特种设备");
        $this->item->col("cqcn_plan_item_method")->type("string")->name("方法")->restrict("RT","UT","PT","MT","ET","LT","VT");
        $this->item->col("cqcn_plan_item_level")->type("string")->name("等级")->restrict("Ⅰ","Ⅱ","Ⅲ");

        $this->item->col("cqcn_plan_item_plan_id")->type("integer")->name("考证计划");
        $this->item->col("cqcn_plan_item_comment")->type("string")->name("考证说明");
        $this->item->col("cqcn_plan_item_cqcn_id")->type("integer")->name("复证选项")->bind("cqcn","id","CONCAT(cqcn_method,cqcn_level,' ',cqcn_expire_date)",function($query){
            $query->where("created_by",Auth::user()->id);
        })->bind_addition(array("非复证" => "0"));

    	$this->item->unique("cqcn_plan_item_method","cqcn_plan_item_level");
    }


    function cqcn_plan_item_del($id){
        $this->parent($id);
        $this->table_data(array("id","cqcn_plan_item_type","CONCAT(cqcn_plan_item_method,' ',cqcn_plan_item_level)","cqcn_plan_item_comment","IF(cqcn_plan_item_plan_id=0,'否','是')","name"),"user");
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->orderby("cqcn_plan_item.created_by","desc");
        //$this->data->add_button("查看","new_flavr",function($data){
            //return $data["qf_src"];
        //});
        return $this->data->render();
    }

}
