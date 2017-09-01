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
        $this->item->col("ss_trademark")->type("string")->name("牌号")->bind("setting","setting_name",function($query){
            $query->where("setting_type","wmtype");
        });;
        $this->item->col("ss_type")->type("string")->name("类型")->input("exec")->def("null");
        $this->item->col("ss_diameter")->type("decimal",5,1)->name("直径");
        $this->item->col("ss_weight")->type("decimal")->name("重量")->tip("kg");
        $this->item->col("ss_in_date")->type("date")->name("入库日期");
        $this->item->col("ss_out_date")->type("date")->name("退库日期")->def("null")->input("exec");
        $this->item->col("ss_out_weight")->type("decimal")->name("退库重量")->def("null")->input("exec")->restrict(function($value){
            if (is_numeric($value)){
                if (isset($this->ss_weight)) {
                    $max = $this->ss_weight;
                    if ($value <= $max) {
                        return true;
                    } else {
                        return "退库数量不能大于入库数量！";
                    }
                } else {
                    return true;
                }
            } else {
                return "重量只能为数值！";
            }
        });
        $this->item->col("ss_out_reason")->type("string")->name("退库原因")->def("")->input("exec");

        $this->default_col[] = "ss_out_date";

    }

    function wmtype($builder){
        $builder->leftJoin('setting AS L1','L1.setting_name',$this->get_table().".ss_trademark");
        $builder->leftJoin('setting AS L2','L2.setting_name',"L1.setting_r0");
        $builder->where(function($query){
            $query->orWhere('L1.setting_type',"wmtrademark");
            $query->orWhere('L1.setting_type',null);
        });
        $builder->where(function($query){
            $query->orWhere('L2.setting_type',"wmtype");
            $query->orWhere('L2.setting_type',null);
        });
        return $builder;
    }


    function LOC(){
        $this->parent("LOC","ss_warehouse");
    }

    function PRE(){
        $this->parent("PRE","ss_warehouse");
    }

    //额外的禁止删除
    function addition_valid_deleting($data){
        if ($this->get_obj_data($data,"ss_out_date") == null) {
            return true;
        }
        $this->msg = "该焊材已发放，不能删除";
        return false;
    }


    function in_show($para){
        $this->$para();
        $this->table_data($this->items_init(array("id","L2.setting_r0"),array("name","created_at")),array("user","wmtype"));
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
        $this->table_data($this->items_init(array("id","L2.setting_r0 as wmtype","L1.setting_r0 as m_type"),array("name","created_at")),array("user","wmtype"));
        $this->data->whereNull("ss_out_date");
        return $this->data->render();
    }

    function store_record($para){
        $this->$para();
        $this->table_data($this->items("id",array("name","created_at")),"user");
        return $this->data->render();
    }
}
