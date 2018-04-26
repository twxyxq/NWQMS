<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;



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

        $this->item->lock(\model_restrict::create(array("tsk","tsk_pp")));
        $this->item->lock(\model_restrict::create(array("material_sheet","ms_pp_ids")));
    }

    function exam_item($builder){
        $builder->leftJoin("exam_item","exam_item.exam_item_pp_ids","LIKE",DB::raw("CONCAT('%{',".$this->get_table().".id,'}%')"));
        $builder->leftJoin("exam","exam.id","exam_item.exam_item_exam_id");
        return $builder;
    }

    function pp_del(){
        $this->table_data(array("id","pcode","pname","psex","pbirth","pdate_in","pdate_out","created_at"));
        $this->data->add_del();
        return $this->data->render();
    }

    function pp_qrcode(){
        $this->table_data(array("id","pcode","pname","psex","pbirth","pdate_in","pdate_out","created_at"));
        $this->data->index(function($data){
            return "<input class=\"pid\" type=\"checkbox\" value=\"".$data["id"]."\">";
        });
        return $this->data->render();
    }

    //（统计）焊工合格率
    function statistic_exam_pass_rate_pp(){
        //周期设定
        $period_item = "";
        if (isset($_GET["sts_start"]) && strlen($_GET["sts_start"]) > 0) {
            $period_item .= $_GET["sts_start"]."-";
        }
        if (isset($_GET["sts_end"]) && strlen($_GET["sts_end"]) > 0) {
            $period_item .= "-".$_GET["sts_end"];
        }
        if ($period_item == "") {
            $period_item = "'全部'";
        } else {
            $period_item = "'".$period_item."'";
        }
        //检验方法设定
        if (isset($_GET["emethod"]) && $_GET["emethod"] != "") {
            $emethod_item = "'".$_GET["emethod"]."'";
        } else {
            $emethod_item = "'不限'";
        }
        $this->table_data(array("exam_item.id as exam_item_id","CONCAT(pcode,pname) as period", $emethod_item, $period_item." as p_range", "COUNT(exam_item.id) as amount","SUM(IF(exam_item_conclusion='不合格',1,0)) as unaccept", "ROUND(SUM(IF(exam_item_conclusion='不合格',0,1))*100/COUNT(exam_item.id),2) as rate"),"exam_item");
        if (isset($_GET["sts_start"]) && strlen($_GET["sts_start"]) > 0) {
            $this->data->where("exam_date",">=",\Carbon\Carbon::createFromFormat('Y-m-d', $_GET["sts_start"])->toDateString());
        }
        if (isset($_GET["sts_end"]) && strlen($_GET["sts_end"]) > 0) {
            $this->data->where("exam_date","<",\Carbon\Carbon::createFromFormat('Y-m-d', $_GET["sts_end"])->addDay()->toDateString());
        }
        if ($emethod_item != "'不限'") {
            $this->data->where("exam_method",$_GET["emethod"]);
        }
        $this->data->whereNotNull("exam_item.id");
        $this->data->whereNotNull("exam_date");
        $this->data->groupBy("pp.id");
        return $this->data->render();
    }
}
