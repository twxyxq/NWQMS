<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;



class gps extends table_model
{
    //

    function column(){

    	$this->item->col("gps_info")->type("string")->name("信息")->def("null");
    	$this->item->col("gps_lon")->type("string")->name("信息")->def("null");
    	$this->item->col("gps_lat")->type("string")->name("信息")->def("null");
    	$this->item->col("gps_alt")->type("string")->name("信息")->def("null");
    	$this->item->col("gps_speed")->type("string")->name("信息")->def("null");
    	$this->item->col("gps_Batt")->type("string")->name("信息")->def("null");
    	$this->item->col("gps_SN")->type("string")->name("信息")->def("null");
    	$this->item->col("gps_LOC")->type("string")->name("信息")->def("null");
        $this->item->col("gps_CI")->type("string")->name("信息")->def("null");
        $this->item->col("gps_jz")->type("decimal")->name("基站")->def("0");


        //$this->item->lock(model_restrict::create(array("wj","qid")));

        $this->version_control();


    }


}
