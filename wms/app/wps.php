<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class wps extends table_model
{
    //

    function column(){

    	$this->item->col("wps_code")->type("string")->name("编码");
    	$this->item->col("wps_wpq")->type("integer")->name("工艺评定号")->bind("wpq","id","wpq_code");
        $this->item->col("wps_jtype")->type("string")->name("接头型式");
        $this->item->col("wps_method")->type("string")->name("焊接方法");
        $this->item->col("wps_wire")->type("string")->name("焊丝型号");
        $this->item->col("wps_rod")->type("string")->name("焊条型号");
        $this->item->col("wps_base_metal_type_A")->type("string")->name("母材A")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basetype");
        })->multiple()->size(5);
        $this->item->col("wps_base_metal_type_B")->type("string")->name("母材B")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basetype");
        })->multiple()->size(5);
        $this->item->col("wps_diameter_lower_limit")->type("string")->name("管径下限");
        $this->item->col("wps_diameter_upper_limit")->type("string")->name("管径上限");
        $this->item->col("wps_thickness_lower_limit")->type("string")->name("厚度下限");
        $this->item->col("wps_thickness_upper_limit")->type("string")->name("厚度上限");
        $this->item->col("wps_ht_id")->type("string")->name("热处理工艺");

        $this->item->unique("wps_code");

        $this->status_control();

        $this->version_control();

    }



    function wps_del(){
        $this->table_data($this->items(array("id","version"),array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_model();
        $this->data->without("avail");
        //$data->where(function($query) use ($para){
            //$query->where("setting_type",$para);
        //});
        return $this->data->render();
    }

    function wps_proc(){
        $this->table_data($this->items(7,array("id","version"),array("name","created_at")),"user");
        //$this->data->add_edit();
        $this->data->add_status_proc();
        $this->data->without("avail");
        $this->data->where(function($query){
            $query->where("status","<>",$this->status_avail);
        });
        return $this->data->render();
    }

   

    function wps_list(){
        $this->table_data($this->items(7,array("id","version"),array("name","created_at")),"user");
        $this->data->add_version_update();
        return $this->data->render();
    }
}
