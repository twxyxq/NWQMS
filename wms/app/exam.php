<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class exam extends table_model
{
    public $child_col = "exam_method";

    public $default_method = "method_select";

    function column(){

    	$this->item->col("exam_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("exam_wj_id")->type("integer")->name("焊口ID")->input("exec");
        $this->item->col("exam_date")->type("date")->name("检验日期")->input("exec")->def("null");
        $this->item->col("exam_eps_id")->type("integer")->name("工艺卡")->input("exec")->def("0");
        $this->item->col("exam_plan_id")->type("integer")->name("检验计划")->input("exec")->def("0");
        $this->item->col("exam_sheet_id")->type("integer")->name("委托单")->input("exec")->def("0");
        $this->item->col("exam_report_id")->type("integer")->name("报告")->input("exec")->def("0");
        $this->item->col("exam_total")->type("integer")->name("总数")->input("exec")->def("0");
        $this->item->col("exam_unaccept")->type("integer")->name("不合格")->input("exec")->def("0");
        $this->item->col("exam_conclusion")->type("string")->name("结论")->input("exec")->def("null");
        $this->item->col("exam_r_id")->type("integer")->name("返修ID")->input("exec")->def("0");
        $this->item->col("exam_input_p")->type("integer")->name("录入人")->input("exec")->def("0");
        $this->item->col("exam_input_time")->type("datetime")->name("录入时间")->input("exec")->def("null");

        $this->item->col("exam_check_p")->type("integer")->name("审核人")->input("exec")->def("0");
        $this->item->col("exam_check_time")->type("datetime")->name("审核时间")->input("exec")->def("null");

        $this->item->col("exam_info_model")->type("string")->name("信息模板")->def("null")->input("exec");
        $this->item->col("exam_info_0")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_1")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_2")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_3")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_4")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_5")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_6")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_7")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_8")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_9")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_10")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_11")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_12")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_13")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_14")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_15")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_16")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_17")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_18")->type("string")->name("信息")->def("null");
        $this->item->col("exam_info_19")->type("string")->name("信息")->def("null");




    }

    function method_select($para){
        $exam_status = $this->where("exam_method",$para."_STATUS")->get();
        $exam_model = $this->where("exam_method",$para."_MODEL")->get();
        if (sizeof($exam_status) == 0 || sizeof($exam_model) == 0) {
            die("请先设置模板");
        } else {
            $this->parent($para);
            for ($i=0; $i < 30; $i++) { 
                $index = "exam_info_".$i;
                if ($exam_status[0]->$index == null) {
                    $this->item->col($index)->input("exec");
                } else {
                    $this->item->col($index)->name($exam_model[0]->$index);
                }
            }
        }
        
    }

    function wj($builder){
        $builder->leftJoin('wj','wj.id',$this->get_table().".exam_wj_id");
        return $builder;
    }

    function exam_plan($builder){
        $builder->leftJoin('exam_plan','exam_plan.id',$this->get_table().".exam_plan_id");
        return $builder;
    }

    function exam_sheet($builder){
        $builder->leftJoin('exam_sheet','exam_sheet.id',$this->get_table().".exam_sheet_id");
        return $builder;
    }

    function exam_item($builder){
        $builder->leftJoin('exam_item','exam_item.exam_item_exam_id',$this->get_table().".id");
        return $builder;
    }

    function exam_report($builder){
        $builder->leftJoin('exam_report','exam_report.id',$this->get_table().".exam_report_id");
        return $builder;
    }

    function eps($builder){
        $builder->leftJoin('eps','eps.id',$this->get_table().".exam_eps_id");
        return $builder;
    }

    //（功能）添加新的exam
    function new_exam($samples_array,$emethod,$exam_plan_id){
        foreach ($samples_array as $id) {
            $exam = new static;
            $exam->exam_wj_id = $id;
            $exam->exam_method = $emethod;
            $exam->exam_plan_id = $exam_plan_id;
            if (!$exam->save()) {
                die($this->msg);
            }
        }
    }
    //（功能）选择eps
    function select_eps($eps_id){
        $this->para_input();
        if (!isset($this->id)) {
            $this->msg = "找不到数据";
            return false;
        }
        $this->exam_eps_id = $eps_id;
        if ($this->save()) {
            $this->msg = "操作成功";
            return true;
        }
        return false;
    }
    //（功能）dt_edit设置,用于录入信息
    function para_input(){
        $this->authorize_user(Auth::user()->id);
    }

    function exam_confirm($id){
        $exam = $this->find($_POST["exam_id"]);
        $exam->para_input();
        $this->method_select($exam->exam_method);
        $exam_item = \App\exam_item::where("exam_item_exam_id",$exam->id)->get();

        if ($exam->exam_eps_id > 0 && sizeof($exam_item) > 0) {
            //判断是否额外参数全部有值
            $pass = 1;
            $unpass_item = "";
            foreach ($this->items_init() as $key) {
                if (strlen($exam->$key) == 0) {
                    $pass = 0;
                    $unpass_item .= "[".$key."]";
                }
            }
            if ($pass == 0) {
                $this->msg = "额外参数".$unpass_item."未填写完整";
                return false;
            } else {
                $exam->exam_input_p = Auth::user()->id;
                $exam->exam_input_time = \Carbon\Carbon::now();
                $exam->exam_conclusion = "合格";
                $exam->exam_total = sizeof($exam_item);
                $exam->exam_unaccept = 0;
                foreach ($exam_item as $ei) {
                    if ($ei->exam_item_conclusion == "不合格") {
                        $exam->exam_unaccept++;
                        $exam->exam_conclusion = "不合格";
                    }
                }
                if ($exam->save()) {
                    return true;
                } else {
                    $this->msg = $exam->msg;
                    return false;
                }
            }
        } else {
            $this->msg = "工艺、结果未填写完整";
            return false;
        }
    }

    //尚未打印委托单的焊口
    function no_sheet_list($emethod=""){
        $this->table_data(array("wj.id as wj_id",SQL_VCODE." as vcode","exam_method","ep_code","ep_ild_sys","ep_pp","name","created_at"),array("user","wj","exam_plan"));
        if ($emethod != "") {
            $this->data->where("exam_method",$emethod);
        }
        $this->data->where("exam_sheet_id",0);
        $this->data->index(function($data,$model){
            return "<input type='checkbox' class='wj_no_sheet' value='".$data["wj_id"]."'>";
        });
        $this->data->col("vcode",function($value,$data){
            return "<a href='###'>".$value."</a>";
        });
        return $this->data->render();
    }

    //检验任务
    function exam_list($emethod=""){
        $this->table_data(array("id",SQL_VCODE." as vcode","exam_method","CONCAT(".$emethod.",'%')","es_code","ep_pp","name","created_at","wj.id as wj_id"),array("user","wj","exam_plan","exam_sheet"));
        
        $this->data->whereNull("exam_input_time");

        if ($emethod != "") {
            $this->data->where("exam_method",$emethod);
        }

        $this->data->col("es_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/sheet_detail?sheet_code=".$data["es_code"]."','".$data["es_code"]."委托单')\">".$value."</a>";
        });
        $this->data->col("vcode",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/tsk/tsk_detail?wj_id=".$data["wj_id"]."','".$data["vcode"]."任务详情')\">".$value."</a>";
        });
        $this->data->add_button("出报告","new_flavr",function($data){
            if (strlen($data["es_code"])) {
                return "/exam/report_detail?exam_id=".$data["id"];
            }
            return "'委托单未出'";
        });
        return $this->data->render();
    }

    //检验草稿
    function exam_draft($emethod=""){
        if ($emethod != "") {
            $this->method_select($emethod);
        }
        $this->table_data($this->items_init(array("id","vcode","es_code"),"wj.id as wj_id"),array("wj","exam_sheet","exam_item"));

        $this->data->groupby("exam.id");
        
        $this->data->whereNull("exam_input_time");
        
        $this->data->havingRaw("SUM(exam_eps_id) > 0");
        $this->data->orhavingRaw("COUNT(exam_item.id)>0");

        $this->data->col("es_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/sheet_detail?sheet_code=".$data["es_code"]."','".$data["es_code"]."委托单')\">".$value."</a>";
        });
        $this->data->col("vcode",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/tsk/tsk_detail?wj_id=".$data["wj_id"]."','".$data["vcode"]."任务详情')\">".$value."</a>";
        });
        $this->data->add_button("修改","new_flavr",function($data){
            return "/exam/report_detail?exam_id=".$data["id"];
        });
        return $this->data->render();
    }

    //检验结果
    function exam_record($emethod=""){
        if ($emethod != "") {
            $this->method_select($emethod);
        }
        $this->table_data($this->items_init(array("id","vcode","es_code","exam_report_code"),array("CONCAT(exam_unaccept,'/',exam_total)","exam_conclusion","exam_input_time","wj.id as wj_id","exam_report_id")),array("wj","exam_sheet","exam_report"));
        
        $this->data->whereNotNull("exam_input_time");

        $this->data->col("es_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/sheet_detail?sheet_code=".$data["es_code"]."','".$data["es_code"]."委托单')\">".$value."</a>";
        });
        $this->data->col("vcode",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/tsk/tsk_detail?wj_id=".$data["wj_id"]."','".$data["vcode"]."任务详情')\">".$value."</a>";
        });
        $this->data->col("exam_report_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/exam/report_detail?report_id=".$data["exam_report_id"]."')\">".$value."</a>";
        });
        $this->data->add_button("修改","new_flavr",function($data){
            if ($data["exam_report_id"] == 0) {
                return "/exam/report_detail?exam_id=".$data["id"]."&edit=1";
            }
            return false;
        });
        return $this->data->render();
    }
    //报告出版
    function exam_report_create($emethod=""){
        if ($emethod != "") {
            $this->method_select($emethod);
        }
        $this->table_data($this->items_init(array("id","vcode","es_code","eps_code"),array("wj.id as wj_id","exam_report_id")),array("wj","exam_sheet","eps"));
        
        $this->data->whereNotNull("exam_input_time");
        $this->data->where("exam_report_id",0);

        $this->data->col("es_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/sheet_detail?sheet_code=".$data["es_code"]."','".$data["es_code"]."委托单')\">".$value."</a>";
        });
        $this->data->col("vcode",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/tsk/tsk_detail?wj_id=".$data["wj_id"]."','".$data["vcode"]."任务详情')\">".$value."</a>";
        });
        $this->data->index(function($data){
            return "<input type=\"checkbox\" name=\"exam_id\" value=\"".$data["id"]."\">";
        });
        return $this->data->render();
    }

    function exam_no_consignation_sheet($emethod = ""){
        $this->table_data(array("id",SQL_VCODE." as vcode","wj_type","exam_method","ep_code","ep_ild_sys","ep_pp","name","created_at","wj.id as wj_id","exam_plan.id as ep_id"),array("user","wj","exam_plan"));
        if ($emethod != "") {
            $this->data->where("exam_method",$emethod);
        }
        $this->data->where("exam_sheet_id",0);
        $this->data->index(function($data,$model){
            return "<input type='checkbox' class='wj_no_sheet' value='".$data["id"]."'>";
        });
        $this->data->col("vcode",function($value,$data){
            return "<a href='###' onclick='new_flavr(\"/console/dt_edit?model=wj&id=".$data["wj_id"]."\")'>".$value."</a>";
        });
        $this->data->col("ep_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/group_detail?id=".$data["ep_id"]."')\">".$value."</a>";
        });
        return $this->data->render();
    }


    function statistic_exam_amount(){
        if (isset($_GET["period"])) {
            switch ($_GET["period"]) {
                case 'day':
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'/',date_format(exam_date, '%m'),'/',date_format(exam_date, '%e'))";
                    $group_item = DB::raw("date_format(exam_date, '%Y-%m-%d')");
                    break;
                case 'month':
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'年',date_format(exam_date, '%m'),'月')";
                    $group_item = DB::raw("date_format(exam_date, '%Y-%M')");
                    break;
                case 'year':
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'年')";
                    $group_item = DB::raw("date_format(exam_date, '%Y')");
                    break;
                
                default:
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'年',date_format(exam_date, '%V'),'周')";
                    $group_item = DB::raw("date_format(exam_date, '%Y-%V')");
                    break;
            }
        } else {
            $period_item = "CONCAT(date_format(exam_date, '%y'),'年',date_format(exam_date, '%V'),'周')";
            $group_item = DB::raw("date_format(exam_date, '%Y-%V')");
        }
        if (isset($_GET["emethod"]) && $_GET["emethod"] != "") {
            $emethod_item = "'".$_GET["emethod"]."'";
        } else {
            $emethod_item = "'不限'";
        }
        if (isset($_GET["ild"]) && $_GET["ild"] != -1) {
            $ild_item = "'".$_GET["ild"]."'";
        } else {
            $ild_item = "'全部'";
        }
        if (isset($_GET["sys"]) && $_GET["sys"] != "") {
            $sys_item = "'".$_GET["sys"]."'";
        } else {
            $sys_item = "'全部系统'";
        }
        $this->table_data(array("exam.id as exam_id",$period_item." as period", $emethod_item, $ild_item, $sys_item, "COUNT(exam.id) as amount","SUM(IF(exam_report_id>0,1,0)) as report"),"wj");
        if ($emethod_item != "'不限'") {
            $this->data->where("exam_method",$_GET["emethod"]);
        }
        if ($ild_item != "'全部'") {
            $this->data->where("ild",$_GET["ild"]);
        }
        if ($sys_item != "'全部系统'") {
            $this->data->where("sys",$_GET["sys"]);
        }
        $this->data->whereNotNull("exam_date");
        $this->data->groupBy($group_item);
        return $this->data->render();
    }

    //（统计）焊口合格率
    function statistic_exam_pass_rate(){
        if (isset($_GET["period"])) {
            switch ($_GET["period"]) {
                case 'day':
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'/',date_format(exam_date, '%m'),'/',date_format(exam_date, '%e'))";
                    $group_item = DB::raw("date_format(exam_date, '%Y-%m-%d')");
                    break;
                case 'month':
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'年',date_format(exam_date, '%m'),'月')";
                    $group_item = DB::raw("date_format(exam_date, '%Y-%M')");
                    break;
                case 'year':
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'年')";
                    $group_item = DB::raw("date_format(exam_date, '%Y')");
                    break;
                
                default:
                    $period_item = "CONCAT(date_format(exam_date, '%y'),'年',date_format(exam_date, '%V'),'周')";
                    $group_item = DB::raw("date_format(exam_date, '%Y-%V')");
                    break;
            }
        } else {
            $period_item = "CONCAT(date_format(exam_date, '%y'),'年',date_format(exam_date, '%V'),'周')";
            $group_item = DB::raw("date_format(exam_date, '%Y-%V')");
        }
        if (isset($_GET["emethod"]) && $_GET["emethod"] != "") {
            $emethod_item = "'".$_GET["emethod"]."'";
        } else {
            $emethod_item = "'不限'";
        }
        if (isset($_GET["ild"]) && $_GET["ild"] != -1) {
            $ild_item = "'".$_GET["ild"]."'";
        } else {
            $ild_item = "'全部'";
        }
        if (isset($_GET["sys"]) && $_GET["sys"] != "") {
            $sys_item = "'".$_GET["sys"]."'";
        } else {
            $sys_item = "'全部系统'";
        }
        $this->table_data(array("exam.id as exam_id",$period_item." as period", $emethod_item, $ild_item, $sys_item, "COUNT(exam.id) as amount","SUM(IF(exam_unaccept>0,1,0)) as unaccept", "ROUND(SUM(IF(exam_unaccept>0,0,1))*100/COUNT(exam.id),2) as rate"),"wj");
        if ($emethod_item != "'不限'") {
            $this->data->where("exam_method",$_GET["emethod"]);
        }
        if ($ild_item != "'全部'") {
            $this->data->where("ild",$_GET["ild"]);
        }
        if ($sys_item != "'全部系统'") {
            $this->data->where("sys",$_GET["sys"]);
        }
        $this->data->whereNotNull("exam_date");
        $this->data->groupBy($group_item);
        return $this->data->render();
    }
}
