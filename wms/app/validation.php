<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;



class validation extends table_model
{
    //

    function column(){
        $this->item->col("valid_col")->type("string")->name("验证列");
        $this->item->col("valid_code")->type("string")->name("验证数据");
        $this->item->col("valid_date")->type("string")->name("验证日期");
        $this->item->col("valid_expiration_date")->type("string")->name("失效日期")->def("null");
        $this->item->col("valid_comment")->type("string")->name("验证说明")->def("N/A");

    	$this->item->unique("qf_code","qf_src");
    }


    function haf_qf(){
        $this->parent("valid_col","qf_code");
    }


    function validation_del($para){
        $this->$para();
        $this->table_data(array("id","qf_code","qf_name","qf_company","qf_info","qf_expiration_date"));
        $this->data->add_del();
        return $this->data->render();
    }
}
