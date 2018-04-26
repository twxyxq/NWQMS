<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;



class ai_lock extends table_model
{
    //

    function column(){
        $this->item->col("ai_lock_code")->type("string")->name("锁编号");
        $this->item->col("ai_lock_name")->type("string")->name("名称");
        $this->item->col("ai_lock_branch")->type("string")->name("单位");


    	$this->item->unique("ai_lock_code");
    }


    function ai_lock_del(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_edit();
        
        return $this->data->render();
    }

}
