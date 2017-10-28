<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

    //（功能）报告出版
    function report_create($exam_ids){
        $exam = new \App\exam();

        $emethod = "";//传入参数，获取检验方法

        if (!$exam->check_if_report_create($exam_ids,$emethod)) {
            $this->msg = $exam->msg;
            return false;
        }

        $this->exam_report_method = $emethod;
        $this->exam_report_code = $_POST["exam_report_code"];
        $this->exam_report_date = $_POST["exam_report_date"];
        $this->exam_report_exam_ids = array_to_multiple($exam_ids);

        try {
            DB::transaction(function() use ($exam,$exam_ids) {
                if (!$this->save()) {
                    throw new \Exception($this->msg);
                }
                if (!$exam->add_report_flag($exam_ids,$this->id)) {
                    throw new \Exception($exam->msg);
                }
            });
        } catch (\Exception $e) {
            $this->msg = $e->getMessage();
            return false;
        }

        return true;
        
    }

    //（功能）报告删除
    function report_delete($report_ids){

        //授权
        $auth = AUTH_EXAM_REPORT_CANCEL;

        if (!is_array($report_ids)) {
            if (!is_integer($report_ids)) {
                throw new \Exception("报告ID错误");
            } else {
                $report_ids = array($report_ids);
            }
        }

        foreach ($report_ids as $report_id) {
            $exam = new \App\exam();

            if (!$exam->remove_report_flag($report_id)) {
                throw new \Exception($exam->msg);
            }

            if (!$this->destroy($report_id,$auth,"deleted_at")) {
                throw new \Exception($this->msg);
            }
        }

    }



    //（页面）报告清单
    function report_list($para){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->where("exam_report_method",$para);
        //$this->data->add_del();
        //$this->data->add_edit($para);
        //$this->data->add_model();
        $this->data->col("exam_report_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/exam/report_detail?report_id=".$data["id"]."')\">".$value."</a>";
        });
        $this->data->add_proc("cancel_report_procedure", "删除流程", function($data){
            return $data["exam_report_code"]."作废流程";    
        },"确定作废该报告？");
        return $this->data->render();
    }

    //（页面）报告撤销清单，显示自己的，可撤销的
    function report_cancel_list(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->where("exam_report.created_by",Auth::user()->id);
        $this->data->col("exam_report_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/exam/report_detail?report_id=".$data["id"]."')\">".$value."</a>";
        });
        $this->data->add_proc("cancel_report_procedure", "删除流程", function($data){
            return $data["exam_report_code"]."作废流程";    
        },"确定作废该报告？");
        return $this->data->render();
    }

   
}
