<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class tsk extends table_model
{
    //编辑结果时使用，用于运行编辑
    public $edit_finished = false;

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
        $this->item->col("tsk_input_p")->type("integer")->name("录入人")->def("0")->input("exec");

        $this->item->col("tsk_ft")->type("string")->name("方式")->input("exec")->restrict("预制","安装");
        $this->item->col("wps_id")->type("integer")->name("工艺卡")->input("exec")->bind("wps","id","CONCAT(wps_code,'(',version,')')");
        $this->item->col("qp_id")->type("integer")->name("质量计划ID")->input("exec")->bind("qp","id","CONCAT(qp_code,'(',version,')')");

        $this->item->col("tsk_wmethod")->type("string")->name("焊接方法")->input("exec");
        $this->item->col("tsk_wj_spec")->type("string")->name("焊口规格")->input("exec");
        $this->item->col("tsk_qp")->type("string")->name("质量计划")->input("exec");


        $this->item->unique("tsk_title","tsk_date","wj_ids");

    }

    //额外的禁止删除
    function addition_valid_deleting($data){
        $tsk_finish_date = $this->get_obj_data($data,"tsk_finish_date");
        if ($tsk_finish_date == null) {
            return true;
        } else {
            $this->msg = "该任务已经录入";
            return false;
        }
    }
    //额外的禁止修改
    function addition_valid_updating($data){
        if (is_object($data) && $data->tsk_finish_date != $data->original["tsk_finish_date"]) {
            $tsk_finish_date = $data->original["tsk_finish_date"];
        } else {
            $tsk_finish_date = $this->get_obj_data($data,"tsk_finish_date");
        }
        if ($tsk_finish_date == null || $this->edit_finished) {
            //为了表格加载速度，只在object的时候验证是否可删除
            if (!is_object($data) || \App\exam::whereIn("exam_wj_id",multiple_to_array($data->wj_ids))->count() == 0) {
                return true;
            } else {
                $this->msg = "焊口已委托，不允许修改";
            }
        } else {
            $this->msg = "该任务已经不允许修改";
        }
        return false;
    }
    //修改完成状态标志
    function edit_finished(){
        $this->edit_finished = true;
    }
    //删除后的连带操作，需要将wj中的tsk_id删除
    function deleted_exec(){
        if (isset($this->id)) {
            $wjs = \App\wj::where("tsk_id",$this->id)->get();
            foreach ($wjs as $wj) {
                $wj->tsk_id = 0;
                $wj->authorize_user(Auth::user()->id);
                $wj->authorize_exec("tsk_id");
                $wj->save_with_exception();
            }
        }
    }


    function wps($builder){
        $builder->leftJoin('wps','wps.id',$this->get_table().".wps_id");
        return $builder;
    }

    function tsk_list(){
        $this->table_data(array("id","tsk_title","tsk_date","tsk_wj_spec","tsk_wmethod","qp_id","CONCAT(wps_code,'(',wps.version,')')","name","created_at","tsk_pp_show","tsk_finish_date"),array("user","wps"));
        $this->data->add_del();
        $this->data->add_button("修改","table_flavr",function($data,$model){
            $model->edit_finished();
            if (strlen($data["tsk_finish_date"]) > 0 && $model->valid_updating($data)) {
                return "/tsk/tsk_finish_form?id=".$data["id"];
            }
            return "";
        });
        $this->data->index(function($data){
            return " <input type=\"checkbox\" class=\"tsk_id\" value=\"".$data["id"]."\"> ";
        });
        $this->data->col("tsk_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/tsk/tsk_detail','任务详情',".$data["id"].")\">".$value."</a>";
        });
        if (isset($_GET["year"]) && strlen($_GET["year"]) > 0) {
            $this->data->where(DB::raw("YEAR(tsk_date)"),$_GET["year"]);
        }
        if (isset($_GET["month"]) && strlen($_GET["month"]) > 0) {
            $this->data->where(DB::raw("MONTH(tsk_date)"),$_GET["month"]);
        }
        if (isset($_GET["day"]) && strlen($_GET["day"]) > 0) {
            $this->data->where(DB::raw("DAY(tsk_date)"),$_GET["day"]);
        }

        return $this->data->render();
    }

    function tsk_unfinished_list(){
        $this->table_data(array("id","tsk_title","tsk_date","tsk_wj_spec","tsk_wmethod","qp_id","CONCAT(wps_code,'(',wps.version,')')","name","created_at","tsk_pp_show","tsk_finish_date"),array("user","wps"));
        $this->data->add_del();
        $this->data->add_button("修改","table_flavr",function($data,$model){
            $model->edit_finished();
            if (strlen($data["tsk_finish_date"]) > 0 && $model->valid_updating($data)) {
                return "/tsk/tsk_finish_form?id=".$data["id"];
            }
            return "";
        });
        $this->data->whereNull("tsk_finish_date");
        $this->data->index(function($data){
            return " <input type=\"checkbox\" class=\"tsk_id\" value=\"".$data["id"]."\"> ";
        });
        $this->data->col("tsk_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/tsk/tsk_detail','任务详情',".$data["id"].")\">".$value."</a>";
        });
        if (isset($_GET["year"]) && strlen($_GET["year"]) > 0) {
            $this->data->where(DB::raw("YEAR(tsk_date)"),$_GET["year"]);
        }
        if (isset($_GET["month"]) && strlen($_GET["month"]) > 0) {
            $this->data->where(DB::raw("MONTH(tsk_date)"),$_GET["month"]);
        }
        if (isset($_GET["day"]) && strlen($_GET["day"]) > 0) {
            $this->data->where(DB::raw("DAY(tsk_date)"),$_GET["day"]);
        }
        return $this->data->render();
    }

    function tsk_my_list(){
        $this->table_data(array("id","tsk_title","tsk_date","tsk_wj_spec","tsk_wmethod","qp_id","CONCAT(wps_code,'(',wps.version,')')","name","created_at","tsk_pp_show","tsk_finish_date"),array("user","wps"));
        $this->data->add_del();
        $this->data->add_button("修改","table_flavr",function($data,$model){
            $model->edit_finished();
            if (strlen($data["tsk_finish_date"]) > 0 && $model->valid_updating($data)) {
                return "/tsk/tsk_finish_form?id=".$data["id"];
            }
            return "";
        });
        $this->data->where("tsk.created_by",Auth::user()->id);
        $this->data->whereNull("tsk_finish_date");
        $this->data->index(function($data){
            return " <input type=\"checkbox\" class=\"tsk_id\" value=\"".$data["id"]."\"> ";
        });
        $this->data->col("tsk_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/tsk/tsk_detail','任务详情',".$data["id"].")\">".$value."</a>";
        });
        if (isset($_GET["year"]) && strlen($_GET["year"]) > 0) {
            $this->data->where(DB::raw("YEAR(tsk_date)"),$_GET["year"]);
        }
        if (isset($_GET["month"]) && strlen($_GET["month"]) > 0) {
            $this->data->where(DB::raw("MONTH(tsk_date)"),$_GET["month"]);
        }
        if (isset($_GET["day"]) && strlen($_GET["day"]) > 0) {
            $this->data->where(DB::raw("DAY(tsk_date)"),$_GET["day"]);
        }
        return $this->data->render();
    }

    function tsk_not_finished(){
        $this->table_data(array("id","tsk_title","tsk_date","tsk_wj_spec","tsk_wmethod","tsk_qp","CONCAT(wps_code,'(',wps.version,')')","name","created_at","tsk_finish_date"),array("user","wps"));
        $this->data->whereNull("tsk_pp");
        $this->data->whereNull("tsk_finish_date");
        $this->data->add_button("录入","add_finish_form",function($data,$model){
            if ($model->valid_updating($data)) {
                return $data["id"];
            }
            return "";
        });
        $this->data->col("tsk_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/tsk/tsk_detail','任务详情',".$data["id"].")\">".$value."</a>";
        });
        return $this->data->render();
    }


}
