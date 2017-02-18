<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class setting extends table_model
{
    public $child_col = "setting_type";


    function column(){
        $this->item->col("setting_type")->type("string");
    	$this->item->col("setting_name")->type("string");
    	$this->item->col("setting_r0")->type("string")->def("N/A");
    	$this->item->col("setting_r1")->type("string")->def("N/A");
    	$this->item->col("setting_comment")->type("string")->name("备注")->def("null");
    	$this->item->unique("setting_name","setting_type");
    }

    function supplier(){
        $this->parent("supplier");
        $this->item->setting_name->name("供应商");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }

    function wmtype(){
        $this->parent("wmtype");
        $this->item->setting_name->name("焊材型号");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }

    function wmtrademark(){
        $this->parent("wmtype");
        $this->item->setting_name->name("焊材牌号");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }

    function medium(){
        $this->parent("medium");
        $this->item->setting_name->name("介质");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }

    function basetype(){
        $this->parent("basetype");
        $this->item->setting_name->name("母材类型");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }

    function jtype(){
        $this->parent("jtype");
        $this->item->setting_name->name("接头型式");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }

    function gtype(){
        $this->parent("gtype");
        $this->item->setting_name->name("坡口型式");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }

    function wmethod(){
        $this->parent("wmethod");
        $this->item->setting_name->name("焊接方法");
        $this->item->setting_r0->input("null");
        $this->item->setting_r1->input("null");
    }


    function basemetal(){
        $this->parent("basemetal");
        $this->item->setting_name->name("母材材质");
        $this->item->setting_r0->name("母材类型")->def(false)->bind("setting","setting_name",function($query){
            $query->where("setting_type","basetype");
        });
        //print_r($this->item->setting_r0);
        $this->item->setting_r1->input("null");
    }

    /*
    function single_view($para,$type = "data"){
        $this->$para();
        $this->table_data(array("id","setting_name","setting_comment","name","created_at"),"user");
        $this->data->add_del();
        $this->data->add_edit($para);
        return $this->data->render();
    }

    function double_view($para,$type = "data"){
        $this->$para();
        $this->table_data(array("id","setting_name","setting_r0","setting_comment","name","created_at"),"user");
        $this->data->add_del();
        $this->data->add_edit($para);
        return $this->data->render();
    }
    */

    function view($para){
        $this->$para();
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_edit($para);
        return $this->data->render();
    }

}
