<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;
use model_restrict;



class eps extends table_model
{
    public $child_col = "eps_method";

    public $default_method = "method_select";

    function column(){

    	$this->item->col("eps_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("eps_code")->type("string")->name("工艺卡编号")->def("N/A");

        $this->item->col("eps_info_0")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_1")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_2")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_3")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_4")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_5")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_6")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_7")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_8")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_9")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_10")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_11")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_12")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_13")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_14")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_15")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_16")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_17")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_18")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_19")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_20")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_21")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_22")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_23")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_24")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_25")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_26")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_27")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_28")->type("string")->name("信息")->def("null");
        $this->item->col("eps_info_29")->type("string")->name("信息")->def("null");

        $this->item->unique("eps_code","eps_method");


    }

    function method_select($para){
        $eps_status = $this->where("eps_method",$para."_STATUS")->get();
        $eps_model = $this->where("eps_method",$para."_MODEL")->get();
        if (sizeof($eps_status) == 0 || sizeof($eps_model) == 0) {
            die("请先设置模板");
        } else {
            $this->parent($para);
            for ($i=0; $i < 30; $i++) { 
                $index = "eps_info_".$i;
                if ($eps_status[0]->$index == null) {
                    $this->item->col($index)->input("exec");
                } else {
                    $this->item->col($index)->name($eps_model[0]->$index);
                }
            }
        }
        
    }

    function eps_list($para){
        $this->method_select($para);
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_edit($para);
        $this->data->add_model();
        return $this->data->render();
    }
    function eps_select($para){
        $this->method_select($para);
        $this->table_data($this->items_init(7,"id"));
        $this->data->index(function($data){
            return "<input type=\"radio\" name=\"exam_eps_id\" value=\"".$data["id"]."\">";
        });
        return $this->data->render();
    }

    
}
