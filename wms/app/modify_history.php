<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;



class modify_history extends table_model
{
    //

    function column(){
    	$this->item->col("history")->type("longText")->name("数据");
    	$this->item->col("model")->type("string")->name("表名称");
    	$this->item->col("model_id")->type("string")->name("ID");
    	$this->item->col("auth_status")->type("string")->name("授权状况")->def("null");

    	//$this->item->unique("model","created_at");

    	//$this->item->unique("pcode","deleted_at");
    }

}
