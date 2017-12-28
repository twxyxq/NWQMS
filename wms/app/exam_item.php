<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class exam_item extends table_model
{
    public $child_col = "exam_item_exam_id";

    public $default_method = "id_select";

    function column(){

    	$this->item->col("exam_item_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("exam_item_exam_id")->type("integer")->name("检验ID")->input("exec")->def("0");
        $this->item->col("exam_item_pp_ids")->type("string")->name("所属焊工")->input("exec")->def("null");

        $this->item->col("exam_item_info_0")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_1")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_2")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_3")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_4")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_5")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_6")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_7")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_8")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_9")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_10")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_11")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_12")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_13")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_14")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_15")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_16")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_17")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_18")->type("string")->name("信息")->def("null");
        $this->item->col("exam_item_info_19")->type("string")->name("信息")->def("null");

        $this->item->col("exam_item_conclusion")->type("string")->name("结论")->input("init")->def("null")->restrict("合格","不合格");
        $this->item->col("exam_item_comment")->type("string")->name("备注")->input("exec")->def("null");

    }

    function exam_wj($builder){
        $builder->leftJoin('exam','exam.id',$this->get_table().".exam_item_exam_id");
        $builder->leftJoin('wj','wj.id',"exam.exam_wj_id");
        return $builder;
    }

    function id_select($exam_id){
        //载入exam
        $exam = \App\exam::find($exam_id);

        $exam_item_status = $this->where("exam_item_method",$exam->exam_method."_STATUS")->get();
        $exam_item_model = $this->where("exam_item_method",$exam->exam_method."_MODEL")->get();
        if (sizeof($exam_item_status) == 0 || sizeof($exam_item_model) == 0) {
            die("请先设置模板");
        } else {
            $this->parent($exam_id);
            for ($i=0; $i < 30; $i++) { 
                $index = "exam_item_info_".$i;
                if ($exam_item_status[0]->$index == null) {
                    $this->item->col($index)->input("exec");
                } else {
                    $this->item->col($index)->name($exam_item_model[0]->$index);
                }
            }
        }
        
    }


    //
    function exam_item_list($exam_id){
        $this->id_select($exam_id);
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_edit($exam_id);
        $this->data->add_model();
        return $this->data->render();
    }

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
        $this->table_data(array("exam_item.id as exam_item_id",$period_item." as period", $emethod_item, $ild_item, $sys_item, "COUNT(exam_item.id) as amount","SUM(IF(exam_item_conclusion='不合格',1,0)) as unaccept", "ROUND(SUM(IF(exam_item_conclusion='不合格',0,1))*100/COUNT(exam_item.id),2) as rate"),"exam_wj");
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
        $this->data->where("wj.R",0);
        $this->data->groupBy($group_item);
        return $this->data->render();
    }

    
}
