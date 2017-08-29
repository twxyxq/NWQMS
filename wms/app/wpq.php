<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class wpq extends table_model
{
    //

    function column(){

    	$this->item->col("wpq_code")->type("string")->name("编码");
    	$this->item->col("wpq_standard")->type("string")->name("标准");
        $this->item->col("wpq_base_metal")->type("string")->name("母材牌号");
        $this->item->col("wpq_base_metal_type")->type("string")->name("母材类型")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basetype");
        })->multiple(2);
        $this->item->col("wpq_method")->type("string")->name("焊接方法");
        $this->item->col("wpq_position")->type("string")->name("焊接位置");
        $this->item->col("wpq_base_type")->type("string")->name("母材规格");
        $this->item->col("wpq_jtype")->type("string")->name("接头型式");
        $this->item->col("wpq_gtype")->type("string")->name("坡口型式");
        $this->item->col("wpq_rod")->type("string")->name("焊条");
        $this->item->col("wpq_wire")->type("string")->name("焊丝");
        $this->item->col("wpq_wire_type")->type("string")->name("焊丝类型");
        $this->item->col("wpq_rod_type")->type("string")->name("焊材类型");
        $this->item->col("wpq_backing")->type("string")->name("背面保护");
        $this->item->col("wpq_swing")->type("string")->name("摆动");
        $this->item->col("wpq_ph_type")->type("string")->name("预热方式");
        $this->item->col("wpq_ph_temperature")->type("string")->name("预热温度");
        $this->item->col("wpq_ht_temperature")->type("string")->name("热处理温度");
        $this->item->col("wpq_layer_temperature")->type("string")->name("层间温度");
        $this->item->col("wpq_front_protective_gas")->type("string")->name("正面保护气体");
        $this->item->col("wpq_back_protective_gas")->type("string")->name("背面保护气体");

        $this->item->unique("wpq_code");

        $this->status_control();

        $this->version_control();

    }



    function wpq_del(){
        $this->table_data($this->items(array("id","version"),array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_model();
        $this->data->without("avail");
        //$data->where(function($query) use ($para){
            //$query->where("setting_type",$para);
        //});
        return $this->data->render();
    }

    function wpq_proc(){
        $this->table_data($this->items(7,array("id","version"),array("name","created_at")),"user");
        //$this->data->add_edit();
        $this->data->add_status_proc();
        $this->data->without("avail");
        $this->data->without("current_version");
        $this->data->where("status","<>",$this->status_avail);
        return $this->data->render();
    }

   

    function wpq_list(){
        $this->table_data($this->items(7,array("id","version"),array("name","created_at")),"user");
        $this->data->add_version_update();
        return $this->data->render();
    }
}
