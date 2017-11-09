<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;
use model_restrict;


require_once "table_model.php";

class sys_check_list extends table_model
{
    
    public $modifyHistory = false;

    function column(){

    	$this->item->col("check_name")->type("string")->name("检查项目")->input("exec");
        $this->item->col("check_model")->type("string")->name("模型")->def("N/A")->input("exec");
        $this->item->col("check_ids")->type("longText")->name("涉及的id")->def("")->input("exec");

        $this->item->unique("check_name");

    }

   

    
}
