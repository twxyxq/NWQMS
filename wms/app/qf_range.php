<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class qf_range extends table_model
{
    //

    function column(){

    	$this->item->col("qf_range_name")->type("string")->name("名称");
        $this->item->col("qf_range_wmethod")->type("string")->name("焊接方法");
        $this->item->col("qf_range_jtype")->type("string")->name("试件型式");
        $this->item->col("qf_range_gtype")->type("string")->name("焊缝型式");
        $this->item->col("qf_range_diameter")->type("decimal")->name("管径");
        $this->item->col("qf_range_thickness")->type("decimal")->name("厚度");
        $this->item->col("qf_range_position")->type("string")->name("焊接方法");
        $this->item->col("qf_range_baseA")->type("string")->name("材质A");
        $this->item->col("qf_range_baseB")->type("string")->name("材质B");
        $this->item->col("qf_range_parameter")->type("string")->name("要素");
        $this->item->col("qf_range_xyz")->type("string")->name("专项");

        $this->item->unique("qf_range_name");


        //$this->item->lock(model_restrict::create(array("wj","qid")));

        $this->version_control();


    }

    function qf_range_list(){
        $this->table_data($this->items_init("id"));
        $this->data->add_del();
        return $this->data->render();
    }



}
