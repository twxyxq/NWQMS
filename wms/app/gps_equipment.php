<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;



class gps_equipment extends table_model
{
    //

    function column(){
        $this->item->col("gps_equipment_sn")->type("string")->name("编码")->bind("gps","gps_SN");
        $this->item->col("gps_equipment_name")->type("string")->name("名称");

    	$this->item->unique("gps_equipment_sn");
    }


    function gps_equipment_del(){
        $this->table_data(array("id","gps_equipment_sn","gps_equipment_name","name","created_at"),"user");
        $this->data->add_del();
        $this->data->add_edit();
        return $this->data->render();
    }

}
