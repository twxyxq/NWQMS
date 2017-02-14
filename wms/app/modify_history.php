<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class modify_history extends table_model
{
    //

    function column(){
    	$this->item->col("history")->type("longText")->name("数据");
    	$this->item->col("model")->type("string")->name("表名称");
    	$this->item->col("model_id")->type("string")->name("ID");

    	//$this->item->unique("model","created_at");

    	//$this->item->unique("pcode","deleted_at");
    }

}
