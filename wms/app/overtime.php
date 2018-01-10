<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class overtime extends table_model
{
    //

    function column(){

    	$this->item->col("overtime_date")->type("date")->name("日期");
        $this->item->col("overtime_interval")->type("string")->name("时间段");
        $this->item->col("overtime_duration")->type("decimal",8,1)->name("时长");

    }



    function overtime_personal(){
        $this->table_data(array("id","overtime_date","overtime_duration"),"user");
        $this->data->where(function($query){
            $query->orWhere("overtime.created_by",Auth::user()->id);
            $query->orWhere("overtime.owner",Auth::user()->id);
        })->get();
        $this->data->add_del();
        return $this->data->render();
    }

}
