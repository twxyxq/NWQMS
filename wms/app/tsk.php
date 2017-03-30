<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class tsk extends table_model
{
    //

    function column(){


        $this->item->col("wj_ids")->type("string")->name("焊口")->input("exec");

        $this->item->col("tsk_title")->type("string")->name("任务名称")->input("exec");
        $this->item->col("tsk_date")->type("date")->name("任务日期")->input("exec");
        $this->item->col("tsk_identity")->type("string")->name("识别码")->input("exec");
        $this->item->col("tsk_identity_record")->type("string")->name("记录序号")->input("exec");
        $this->item->col("tsk_print_history")->type("string")->name("打印记录")->input("exec");


        $this->item->col("tsk_finish_date")->type("date")->name("完成日期")->def("null")->input("exec");
        $this->item->col("tsk_pp")->type("string")->name("人员ID")->def("null")->input("exec");
        $this->item->col("tsk_pp_proportion")->type("string")->name("工作量分配")->def("null")->input("exec");
        $this->item->col("tsk_pp_show")->type("string")->name("施工人员")->def("null")->input("exec");
        $this->item->col("tsk_input_time")->type("datetime")->name("录入日期")->def("null")->input("exec");

        $this->item->col("tsk_ft")->type("string")->name("方式")->input("exec");
        $this->item->col("wps_id")->type("integer")->name("工艺卡")->input("exec");
        $this->item->col("qp_id")->type("integer")->name("质量计划ID")->input("exec");

        $this->item->col("tsk_wmethod")->type("string")->name("焊接方法")->input("exec");
        $this->item->col("tsk_wj_spec")->type("string")->name("焊口规格")->input("exec");
        $this->item->col("tsk_qp")->type("string")->name("质量计划")->input("exec");




    }

    //额外的禁止删除
    function valid_deleting($data){
        if (is_array($data)) {
            $tsk_finish_date = $data["tsk_finish_date"];
        } else if (is_object($data)) {
            $tsk_finish_date = $data->tsk_finish_date;
        } else {
            return false;
        }
        if ($tsk_finish_date == null && parent::valid_deleting($data)) {
            return true;
        }
        return false;
    }

    function wps($builder){
        $builder->leftJoin('wps','wps.id',$this->get_table().".wps_id");
        return $builder;
    }

    function tsk_list(){
        $this->table_data(array("id","tsk_title","tsk_date","tsk_wj_spec","tsk_wmethod","tsk_qp","CONCAT(wps_code,'(',wps.version,')')","name","created_at","tsk_pp_show","tsk_finish_date"),array("user","wps"));
        $this->data->add_del();
        $this->data->col("tsk_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/tsk/tsk_detail','任务详情',".$data["id"].")\">".$value."</a>";
        });
        return $this->data->render();
    }

    function tsk_not_finished(){
        $this->table_data(array("id","tsk_title","tsk_date","tsk_wj_spec","tsk_wmethod","tsk_qp","CONCAT(wps_code,'(',wps.version,')')","name","created_at"),array("user","wps"));
        $this->data->whereNull("tsk_pp");
        $this->data->whereNull("tsk_finish_date");
        $this->data->add_button("录入","add_finish_form",function($data,$model){
            return $data["id"];
        });
        $this->data->col("tsk_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/tsk/tsk_detail','任务详情',".$data["id"].")\">".$value."</a>";
        });
        return $this->data->render();
    }


}
