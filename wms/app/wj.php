<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class wj extends table_model
{
    //

    function column(){


        $this->item->col("wj_type")->type("string")->name("类型")->restrict("管道","结构")->size(2);

    	$this->item->col("project")->type("string")->name("项目")->restrict(array("常规岛安装","BOP安装"))->size(2);
    	$this->item->col("ild")->type("integer")->name("机组")->restrict(array(0,5,6,7))->size(2);
    	$this->item->col("sys")->type("string",20)->name("系统")->size(2);
    	$this->item->col("pipeline")->type("string",30)->name("管线")->size(2);
    	$this->item->col("vnum")->type("string",30)->name("焊口号")->size(2);
    	$this->item->col("vcode")->type("string")->name("焊口编码")->cal(array("ild","sys","pipeline","vnum"),function($ild,$sys,$pipeline,$vnum){
            return $ild.$sys."-".$pipeline."-".$vnum;
        },false)->size(5);
    	$this->item->col("drawing")->type("string")->name("图纸")->size(5);
    	$this->item->col("area")->type("string")->name("区域")->def("N/A")->size(2);
    	$this->item->col("temprature")->type("decimal")->name("温度")->def("null")->size(2);
    	$this->item->col("pressure")->type("decimal")->name("压力")->def("null")->size(2);
    	$this->item->col("ft")->type("string")->name("方式")->restrict("预制","安装")->size(2);
        $this->item->col("jtype")->type("string")->name("接头型式")->bind("setting","setting_name",function($query){
            $query->where("setting_type","jtype");
        })->size(2);
        $this->item->col("gtype")->type("string")->name("坡口型式")->bind("setting","setting_name",function($query){
            $query->where("setting_type","gtype");
        })->size(2);
    	$this->item->col("ac")->type("string")->name("材质A")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basemetal");
        });
    	$this->item->col("at")->type("decimal")->name("管径A")->def("0");
    	$this->item->col("ath")->type("decimal")->name("厚度A")->def("0");
        $this->item->col("bc")->type("string")->name("材质B")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basemetal");
        });
    	$this->item->col("bt")->type("decimal")->name("管径B")->def("0");
    	$this->item->col("bth")->type("decimal")->name("厚度B")->def("0");
    	$this->item->col("medium")->type("string")->name("介质")->bind("setting","setting_name",function($query){
            $query->where("setting_type","medium");
        });
    	$this->item->col("upstream")->type("string")->name("上游")->def("N/A");
    	$this->item->col("downstream")->type("string")->name("下游")->def("N/A");
    	$this->item->col("pressure_test")->type("integer")->name("水压")->def("0")->restrict(0,1);

        $this->item->col("RT")->type("string")->def("N/A")->input("cal");
        $this->item->col("RT_plan")->type("string")->def("")->input("exec");
        $this->item->col("RT_weight")->type("string")->def("")->input("cal");
        $this->item->col("RT_result")->type("string")->def("")->input("exec");
        $this->item->col("UT")->type("string")->def("N/A")->input("cal");
        $this->item->col("UT_plan")->type("string")->def("")->input("exec");
        $this->item->col("UT_weight")->type("string")->def("")->input("cal");
        $this->item->col("UT_result")->type("string")->def("")->input("exec");
        $this->item->col("PT")->type("string")->def("N/A")->input("cal");
        $this->item->col("PT_plan")->type("string")->def("")->input("exec");
        $this->item->col("PT_weight")->type("string")->def("")->input("cal");
        $this->item->col("PT_result")->type("string")->def("")->input("exec");
        $this->item->col("MT")->type("string")->def("N/A")->input("cal");
        $this->item->col("MT_plan")->type("string")->def("")->input("exec");
        $this->item->col("MT_weight")->type("string")->def("")->input("cal");
        $this->item->col("MT_result")->type("string")->def("")->input("exec");
        $this->item->col("SA")->type("string")->def("N/A")->input("cal");
        $this->item->col("SA_plan")->type("string")->def("")->input("exec");
        $this->item->col("SA_weight")->type("string")->def("")->input("cal");
        $this->item->col("SA_result")->type("string")->def("")->input("exec");
        $this->item->col("HB")->type("string")->def("N/A")->input("cal");
        $this->item->col("HB_plan")->type("string")->def("")->input("exec");
        $this->item->col("HB_weight")->type("string")->def("")->input("cal");
        $this->item->col("HB_result")->type("string")->def("")->input("exec");

        $this->item->col("qid")->type("integer")->def("0")->name("质量计划")->bind("qp","id","CONCAT(qp_code,'(',version,')')");
        $this->item->col("tsk_id")->type("integer")->def("0")->input("exec");
        $this->item->col("wb_id")->type("integer")->def("0")->input("exec");
        $this->item->col("qc_level")->type("string")->def("0")->input("exec");

        $this->item->col("R")->type("integer")->def("0")->input("exec");
        $this->item->col("R_src")->type("integer")->def("0")->input("exec");

    	$this->item->unique("ild","sys","pipeline","vnum");

    }

    function wj_list(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_model();
        return $this->data->render();
    }

    function wj_no_task(){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE_TYPE." as type",SQL_EXAM_RATE." as rate","ft","qid"),"user");
        $this->data->add_button("选择","wj_choose",function($data){return $data["id"];});
        return $this->data->render();
    }

}
