<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class wj_base extends table_model
{
    //

    function column(){

    	$this->item->col("project")->type("string")->def("N/A")->restrict(array("核岛土建"));
    	$this->item->col("ild")->type("string")->def("N/A")->restrict(array(0,5,6,7));
    	$this->item->col("sys")->type("string")->def("N/A");
    	$this->item->col("pipeline")->type("string")->def("N/A");
    	$this->item->col("vnum")->type("string")->def("N/A");
    	$this->item->col("vcode")->type("string")->def("N/A");
    	$this->item->col("drawing")->type("string")->def("N/A");
    	$this->item->col("area")->type("string")->def("N/A");
    	$this->item->col("temprature")->type("string")->def("N/A");
    	$this->item->col("pressure")->type("string")->def("N/A");
    	$this->item->col("ft")->type("string")->def("N/A");
    	$this->item->col("jtype")->type("string")->def("N/A");
    	$this->item->col("gtype")->type("string")->def("N/A");
    	$this->item->col("ac")->type("string")->def("N/A");
    	$this->item->col("at")->type("string")->def("N/A");
    	$this->item->col("ath")->type("string")->def("N/A");
    	$this->item->col("bc")->type("string")->def("N/A");
    	$this->item->col("bt")->type("string")->def("N/A");
    	$this->item->col("bth")->type("string")->def("N/A");
    	$this->item->col("medium")->type("string")->def("N/A");
    	$this->item->col("upstream")->type("string")->def("N/A");
    	$this->item->col("downstream")->type("string")->def("N/A");
    	$this->item->col("pressure_test")->type("string")->def("N/A");
    	$this->item->col("qid")->type("string")->def("N/A");
    }
}
