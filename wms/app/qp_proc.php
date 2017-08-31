<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Builder;


require_once "table_model.php";

class qp_proc extends table_model
{
    public $parent_lock = true;

    public $parent_name = "qp_proc_model";

    public $child_col = "qpp_model_id";

    function column(){

    	$this->item->col("qpp_model_id")->type("string");
    	$this->item->col("qpp_num")->type("string")->name("工序号");
    	$this->item->col("qpp_name")->type("string")->name("名称");
    	$this->item->col("qpp_procedure")->type("string")->name("程序")->def("N/A");
    	$this->item->col("qpp_qc2")->type("string")->name("QC2")->def("null")->restrict(array("","W","H","R","Wq"));
    	$this->item->col("qpp_qc3")->type("string")->name("QC3")->def("null")->restrict(array("","W","H","R","Wq"));
    	$this->item->col("qpp_height")->type("integer")->name("行高")->def(100)->restrict(array(100,120,140,160,180,200,80,60));
       
    }

    

    function qpp_list($id){
        $this->parent($id);
        $this->table_data(array("id","qpp_num","qpp_name","qpp_procedure","qpp_name","qpp_qc2","qpp_qc3","qpp_height","name","created_at"),"user");
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->col("qpp_height",function($value,$data){
            return "<a href=\"###\" onclick=\"table_flavr('/qp/qp_proc_height_change?id=".$data["id"]."')\">".$value."</a>";
        });
        return $this->data->render();
    }

}
