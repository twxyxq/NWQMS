<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class secondary_store extends table_model
{

    function column(){
    	$this->item->col("ss_warehouse")->type("string")->name("二级库")->input("null");
    	$this->item->col("ss_batch")->type("string")->name("批号");
        $this->item->col("ss_trademark")->type("string")->name("牌号");
        $this->item->col("ss_type")->type("string")->name("型号")->bind("setting","setting_name",function($query){
            $query->where("setting_type","wmtype");
        });
        $this->item->col("ss_diameter")->type("decimal")->name("直径");
        $this->item->col("ss_weight")->type("decimal")->name("重量")->tip("kg");
        $this->item->col("ss_in_date")->type("date")->name("入库日期");
        $this->item->col("ss_out_date")->type("date")->name("退库日期")->def("null")->input("exec");
        $this->item->col("ss_out_weight")->type("decimal")->name("退库重量")->def("null")->input("exec")->restrict(function($value,$id){
            if (is_numeric($value)){
                $max = $this->find($id)->ss_weight;
                if ($value <= $max) {
                    return true;
                } else {
                    return "退库数量不能大于入库数量！";
                }
            } else {
                return "重量只能为数值！";
            }
        });
        $this->item->col("ss_out_reason")->type("string")->name("退库原因")->def("")->input("exec");

        $this->default_col[] = "ss_out_date";

    }


    function LOC(){
        $this->parent("LOC","ss_warehouse");
    }

    function PRE(){
        $this->parent("PRE","ss_warehouse");
    }

    //额外的禁止删除
    function valid_deleting($data){
        if (is_array($data)) {
            $ss_out_date = $data["ss_out_date"];
        } else if (is_object($data)) {
            $ss_out_date = $data->ss_out_date;
        } else {
            return false;
        }
        if ($ss_out_date == null && parent::valid_deleting($data)) {
            return true;
        }
        return false;
    }


    function in_show($para){
        $this->$para();
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        return $this->data->render();
    }

    function out_show($para){
        $this->$para();
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->whereNull("ss_out_date");
        $this->data->add_button("退库","add_out_form",function($data,$model){
            return array($data["id"],$data["ss_batch"],$data["ss_weight"],$data["ss_in_date"]);
        });
        return $this->data->render();
    }

    function store_list($para){
        $this->$para();
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->whereNull("ss_out_date");
        return $this->data->render();
    }

    function store_record($para){
        $this->$para();
        $this->table_data($this->items("id",array("name","created_at")),"user");
        return $this->data->render();
    }
}
