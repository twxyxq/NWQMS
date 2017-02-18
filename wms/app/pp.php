<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class pp extends table_model
{
    //

    function column(){
    	$this->item->col("pcode")->type("string")->name("钢印号");
    	$this->item->col("pname")->type("string")->name("姓名");
    	$this->item->col("psex")->type("string")->name("性别")->restrict(array("男","女"));
        $this->item->col("pidcard")->type("string")->name("身份证")->def("null");
        $this->item->col("pbirth")->type("date")->name("生日")->def("null");
    	$this->item->col("pdate_in")->type("date")->name("进场")->def("null");
        $this->item->col("pdate_out")->type("date")->name("离场")->def("null")->input("exec");
        $this->item->col("user")->type("integer")->name("用户")->def(0)->input("exec");

    	$this->item->unique("pcode");
    }

    function pp_del(){
        $this->table_data(array("id","pcode","pname","psex","pbirth","pdate_in","pdate_out","created_at"));
        $this->data->add_del();
        return $this->data->render();
    }
}
