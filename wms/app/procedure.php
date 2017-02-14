<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class procedure extends table_model
{
    //
    public $table_version = false;

    function column(){
        $this->item->col("pd_name")->type("string")->name("流程名称");
        $this->item->col("pd_class")->type("string")->name("流程类");
        $this->item->col("pd_executed")->type("string")->name("执行状态")->restrict("CANC","PROC","EXEC");
        $this->item->col("pd_model")->type("string")->name("关联模型");
        $this->item->col("pd_ids")->type("string")->name("关联ID");

        //$this->item->unique("pd_name");
    }

    function procedure_list(){
        $this->table_data(array("id","pd_name","pd_exec","pd_rollback","pd_approve","created_at"));
        $this->data->add_del();
        return $this->data->render();
    }
}

