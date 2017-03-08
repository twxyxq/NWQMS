<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;


require_once "table_model.php";

class wj extends table_model
{
    //

    function column(){


        $this->item->col("wj_type")->type("string")->name("类型")->restrict("管道","结构")->size(2);

    	$this->item->col("project")->type("string")->name("项目")->restrict(array("常规岛安装","BOP安装"))->size(2);
    	$this->item->col("ild")->type("integer")->name("机组")->restrict(array(0,5,6,7))->size(2);
    	$this->item->col("sys")->type("string",20)->name("系统")->size(2)->restrict(function($value){
            if (preg_match('/^[A-Z]+$/', $value) && strlen($value) == 3) {
                return true;
            } else {
                return "系统号只能是三位大写字母";
            }
        });
    	$this->item->col("pipeline")->type("string",30)->name("管线")->size(2);
    	$this->item->col("vnum")->type("string",30)->name("焊口号")->size(2);
    	$this->item->col("vcode")->type("string")->name("焊口编码")->size(5);
    	$this->item->col("drawing")->type("string")->name("图纸")->size(5);
    	$this->item->col("area")->type("string")->name("区域")->def("N/A")->size(2);
    	$this->item->col("temperature")->type("decimal")->name("温度")->def("null")->size(2)->restrict(function($value){
            if (is_numeric($value)){
                return true;
            } else {
                return "温度只能为数值！";
            }
        });
    	$this->item->col("pressure")->type("decimal")->name("压力")->def("null")->size(2)->restrict(function($value){
            if (is_numeric($value)){
                return true;
            } else {
                return "压力只能为数值！";
            }
        });
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
    	$this->item->col("at")->type("decimal")->name("管径A")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value >= 0){
                return true;
            } else {
                return "管径只能为正数！（“0”为非管道焊缝）";
            }
        });
    	$this->item->col("ath")->type("decimal")->name("厚度A")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value > 0){
                return true;
            } else {
                return "厚度为大于0的数值！";
            }
        });
        $this->item->col("bc")->type("string")->name("材质B")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basemetal");
        });
    	$this->item->col("bt")->type("decimal")->name("管径B")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value >= 0){
                return true;
            } else {
                return "管径只能为正数！（“0”为非管道焊缝）";
            }
        });
    	$this->item->col("bth")->type("decimal")->name("厚度B")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value > 0){
                return true;
            } else {
                return "厚度为大于0的数值！";
            }
        });
    	$this->item->col("medium")->type("string")->name("介质")->bind("setting","setting_name",function($query){
            $query->where("setting_type","medium");
        });
    	$this->item->col("upstream")->type("string")->name("上游")->def("N/A");
    	$this->item->col("downstream")->type("string")->name("下游")->def("N/A");
        $this->item->col("pressure_test")->type("integer")->name("水压")->def("0")->restrict(0,1);
        $this->item->col("qid")->type("integer")->def("0")->name("质量计划")->bind("qp","id","CONCAT(qp_code,'(',version,')')")->size(7);

        
        $this->item->col("level")->type("string")->name("焊缝级别")->def("null");
        $this->item->col("exam_specify")->type("integer")->name("指定检验")->def("0")->input("init")->restrict("0","1");
        $this->item->col("exam_specify_reason")->type("string")->name("指定理由")->def("null")->input("init");
        $this->item->col("RT")->type("integer")->name("RT")->def("0")->input("init")->restrict(function($value){
            if (is_numeric($value) && $value >= 0 && $value <= 100){
                return true;
            } else {
                return "检验比例为0~100之间的数值";
            }
        })->size(1)->tip("<span style='position:absolute;bottom:3px;right:5px;'>%</span>");
        $this->item->col("RT_plan")->type("string")->def("")->input("exec");
        $this->item->col("RT_weight")->type("integer")->def("0")->input("cal");
        $this->item->col("RT_result")->type("string")->def("")->input("exec");
        $this->item->col("UT")->type("integer")->name("UT")->def("0")->input("init")->restrict(function($value){
            if (is_numeric($value) && $value >= 0 && $value <= 100){
                return true;
            } else {
                return "检验比例为0~100之间的数值";
            }
        })->size(1)->tip("<span style='position:absolute;bottom:3px;right:5px;'>%</span>");
        $this->item->col("UT_plan")->type("string")->def("")->input("exec");
        $this->item->col("UT_weight")->type("integer")->def("0")->input("cal");
        $this->item->col("UT_result")->type("string")->def("")->input("exec");
        $this->item->col("PT")->type("integer")->name("PT")->def("0")->input("init")->restrict(function($value){
            if (is_numeric($value) && $value >= 0 && $value <= 100){
                return true;
            } else {
                return "检验比例为0~100之间的数值";
            }
        })->size(1)->tip("<span style='position:absolute;bottom:3px;right:5px;'>%</span>");
        $this->item->col("PT_plan")->type("string")->def("")->input("exec");
        $this->item->col("PT_weight")->type("integer")->def("0")->input("cal");
        $this->item->col("PT_result")->type("string")->def("")->input("exec");
        $this->item->col("MT")->type("integer")->name("MT")->def("0")->input("init")->restrict(function($value){
            if (is_numeric($value) && $value >= 0 && $value <= 100){
                return true;
            } else {
                return "检验比例为0~100之间的数值";
            }
        })->size(1)->tip("<span style='position:absolute;bottom:3px;right:5px;'>%</span>");
        $this->item->col("MT_plan")->type("string")->def("")->input("exec");
        $this->item->col("MT_weight")->type("integer")->def("0")->input("cal");
        $this->item->col("MT_result")->type("string")->def("")->input("exec");
        $this->item->col("SA")->type("integer")->name("SA")->def("0")->input("init")->restrict(function($value){
            if (is_numeric($value) && $value >= 0 && $value <= 100){
                return true;
            } else {
                return "检验比例为0~100之间的数值";
            }
        })->size(1)->tip("<span style='position:absolute;bottom:3px;right:5px;'>%</span>");
        $this->item->col("SA_plan")->type("string")->def("")->input("exec");
        $this->item->col("SA_weight")->type("integer")->def("0")->input("cal");
        $this->item->col("SA_result")->type("string")->def("")->input("exec");
        $this->item->col("HB")->type("integer")->name("HB")->def("0")->input("init")->restrict(function($value){
            if (is_numeric($value) && $value >= 0 && $value <= 100){
                return true;
            } else {
                return "检验比例为0~100之间的数值";
            }
        })->size(1)->tip("<span style='position:absolute;bottom:3px;right:5px;'>%</span>");
        $this->item->col("HB_plan")->type("string")->def("")->input("exec");
        $this->item->col("HB_weight")->type("integer")->def("0")->input("cal");
        $this->item->col("HB_result")->type("string")->def("")->input("exec");

        $this->item->col("tsk_id")->type("integer")->def("0")->input("exec");
        $this->item->col("wj_tsk_finish_date")->type("date")->def("null")->input("exec");
        $this->item->col("wb_id")->type("integer")->def("0")->input("exec");
        $this->item->col("qc_level")->type("string")->def("0")->input("exec");

        $this->item->col("R")->type("integer")->def("0")->input("exec");
        $this->item->col("R_src")->type("integer")->def("0")->input("exec");

    	$this->item->unique("ild","sys","pipeline","vnum");


        $this->item->cal(array("ild","sys","pipeline","vnum"),"vcode",function($ild,$sys,$pipeline,$vnum){
            return $ild.$sys."-".$pipeline."-".$vnum;
        });
        $this->item->cal(array("medium","pressure","temperature","ac","bc"),"level",true,function($medium,$pressure,$temperature,$ac,$bc){
            return level_cal($medium, $pressure, $temperature, $ac, $bc);
        });
        $this->item->cal(array("level","pressure_test","ac","at","ath","bc","bt","bth","jtype"),array("RT","UT","PT","MT","SA","HB"),"exam_specify",function($level, $pressure_test, $ac, $at, $ath, $bc, $bt, $bth, $jtype){
            return exam_rate_cal($level, $pressure_test, $ac, $at, $ath, $bc, $bt, $bth, $jtype);
        });


        $this->default_col[] = "tsk_id";

    }

    function tsk($builder){
        $builder->leftJoin('tsk','tsk.id',$this->get_table().".tsk_id")->leftJoin('wps','wps.id',"tsk.wps_id");
        return $builder;
    }

    //额外的禁止删除
    function valid_deleting($data){
        if (is_array($data)) {
            $tsk_id = $data["tsk_id"];
        } else if (is_object($data)) {
            $tsk_id = $data->tsk_id;
        } else {
            return false;
        }
        if (($tsk_id == 0 || $tsk_id == null) && parent::valid_deleting($data)) {
            return true;
        }
        return false;
    }

    function wj_list(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_model();
        return $this->data->render();
    }

    function wj_no_task(){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE_TYPE." as type",SQL_EXAM_RATE." as rate","ft","qid"),"user");
        $this->data->where("tsk_id",0);
        $this->data->add_button("选择","wj_choose",function($data){return $data["id"];});
        return $this->data->render();
    }

    function wj_no_consignation($emethod){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE_TYPE." as type","CONCAT(".$emethod.",'%')","tsk_pp_show","CONCAT(wps_code,'(',wps.version,')') as wps","ild","sys","tsk_pp",$emethod),array("user","tsk"));
        $this->data->where($emethod,">",0);
        $this->data->whereNotNull("tsk.tsk_finish_date");
        $this->data->whereRaw($emethod."_weight < ".$emethod);
        $this->data->add_button("选择","wj_choose",function($data){return $data["id"];});
        $this->data->index(function($data,$model) use ($emethod){
            $html = "<input id='identity_".$data["id"]."' type='hidden' value='".$emethod.$data[$emethod]."%-".$data["ild"].$data["sys"]."-".$data["tsk_pp_show"]."-".\Carbon\Carbon::today()->toDateString()."'>";
            $html .= "<input id='ild_sys_".$data["id"]."' type='hidden' value='".$data["ild"].$data["sys"]."'>";
            $html .= "<input id='tsk_pp_".$data["id"]."' type='hidden' value='".$data["tsk_pp"]."'>";
            $html .= "<input id='rate_".$data["id"]."' type='hidden' value='".$data[$emethod]."'>";
            return $html;
        });
        return $this->data->render();
    }

}
