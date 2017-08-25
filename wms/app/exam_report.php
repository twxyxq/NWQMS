<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class exam_report extends table_model
{
    //

    function column(){

        $this->item->col("exam_report_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("exam_report_code")->type("string")->name("报告号");
        $this->item->col("exam_report_exam_ids")->type("string")->name("检验ID")->input("exec");
        $this->item->col("exam_report_date")->type("date")->name("报告日期");

        $this->item->col("exam_report_col_width")->type("string")->name("列宽")->def("null");
        $this->item->col("exam_report_confirm_p")->type("integer")->name("确认人")->input("exec")->def("0");
        $this->item->col("exam_report_confirm_time")->type("datetime")->name("确认时间")->input("exec")->def("null");

        $this->item->unique("exam_report_method","exam_report_code");




    }

    function report_list($para){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->where("exam_report_method",$para);
        //$this->data->add_del();
        //$this->data->add_edit($para);
        //$this->data->add_model();
        $this->data->col("exam_report_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/exam/report_detail?report_id=".$data["id"]."')\">".$value."</a>";
        });
        return $this->data->render();
    }

   
}
