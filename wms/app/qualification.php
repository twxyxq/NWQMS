<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class qualification extends table_model
{
    //

    function column(){
    	$this->item->col("qf_code")->type("string")->name("证书编号");
        $this->item->col("qf_src")->type("string")->name("数据源");
        $this->item->col("qf_info")->type("string")->name("证书信息");
        $this->item->col("qf_institution")->type("string")->name("颁发机构");
        $this->item->col("qf_company")->type("string")->name("聘用单位");
    	$this->item->col("qf_date")->type("date")->name("颁发日期")->def("null");
    	$this->item->col("qf_expiration_date")->type("date")->name("截止日期")->def("null");
        $this->item->col("qf_range")->type("string")->name("有效期")->def("null")->input("exec");
        $this->item->col("qf_standard")->type("string")->name("标准");

    	$this->item->unique("qf_code");
    }

    function qualification_del(){
        $this->table_data(array("id","pcode","pname","psex","pbirth","pdate_in","pdate_out","created_at"));
        $this->data->add_del();
        return $this->data->render();
    }
}
