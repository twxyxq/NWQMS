<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class qp_proc_model extends table_model
{
    //

    function column(){

    	$this->item->col("qpm_name")->type("string")->name("名称");
        $this->item->col("qpm_comment")->type("string")->name("备注")->def("null");
        $this->item->col("qpm_condition")->type("string")->name("条件")->restrict(array("全部","预制","安装",">=DN150","<DN150"));
    	$this->item->unique("qpm_name");
        $this->item->lock(model_restrict::create(array("qp","qp_proc_model")));

        $this->version_control();
    }

    function qpm_list(){
        //echo "<p><p>";
        //print_r(expression);
    	$this->table_data(array("id","version","qpm_name","qpm_comment","qpm_condition","name","created_at"),"user");
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->add_version_update();
        $this->data->add_button("工序","detail_flavr",function($data,$model){
            return array("/qp/qp_proc_detail","《".$data["qpm_name"]."》工序设置",$data["id"]);
        });
        return $this->data->render();
    }
    
}
