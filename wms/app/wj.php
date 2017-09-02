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


        $this->item->col("wj_base_id")->type("integer")->input("exec")->def("0");

        $this->item->col("wj_type")->type("string")->name("类型")->restrict("管道","结构")->size(2);

    	$this->item->col("project")->type("string")->name("项目")->restrict(array("常规岛安装","BOP安装"))->size(2);
        $this->item->col("drawing")->type("string")->name("图纸")->size(5);
    	$this->item->col("ild")->type("integer")->name("机组")->restrict(array("0","5","6","7"))->size(2);
    	$this->item->col("sys")->type("string",20)->name("系统")->size(2)->restrict(function($value){
            if (preg_match('/^[A-Z]+$/', $value) && strlen($value) == 3) {
                return true;
            } else {
                return "系统号只能是三位大写字母";
            }
        });
    	$this->item->col("pipeline")->type("string",30)->name("管线")->size(2);
    	$this->item->col("vnum")->type("string",30)->name("焊口号")->size(2);
    	$this->item->col("vcode")->type("string")->name("焊口编码")->size(8);
    	//$this->item->col("area")->type("string")->name("区域")->def("N/A")->size(2);
        $this->item->col("jtype")->type("string")->name("接头型式")->bind("setting","setting_name",function($query){
            $query->where("setting_type","jtype");
        })->size(2);
        /*
        $this->item->col("gtype")->type("string")->name("坡口型式")->bind("setting","setting_name",function($query){
            $query->where("setting_type","gtype");
        })->size(2);
        */
        $this->item->col("at")->type("decimal")->name("管径A")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value >= 0){
                return true;
            } else {
                return "管径只能为正数！（“0”为非管道焊缝）";
            }
        })->size(2);
        $this->item->col("ath")->type("decimal")->name("厚度A")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value > 0){
                return true;
            } else {
                return "厚度为大于0的数值！";
            }
        })->size(2);
        $this->item->col("upstream")->type("string")->name("上游")->def("N/A")->size(2);
        $this->item->col("ac")->type("string")->name("材质A")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basemetal");
        })->size(2);
        $this->item->col("bt")->type("decimal")->name("管径B")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value >= 0){
                return true;
            } else {
                return "管径只能为正数！（“0”为非管道焊缝）";
            }
        })->size(2);
        $this->item->col("bth")->type("decimal")->name("厚度B")->def("0")->restrict(function($value){
            if (is_numeric($value) && $value > 0){
                return true;
            } else {
                return "厚度为大于0的数值！";
            }
        })->size(2);
        $this->item->col("downstream")->type("string")->name("下游")->def("N/A")->size(2);
        $this->item->col("bc")->type("string")->name("材质B")->bind("setting","setting_name",function($query){
            $query->where("setting_type","basemetal");
        })->size(2);
    	$this->item->col("temperature")->type("decimal")->name("温度")->def("null")->size(2)->restrict(function($value){
            if (is_numeric($value)){
                return true;
            } else {
                return "温度只能为数值！";
            }
        })->size(3);
    	$this->item->col("pressure")->type("decimal")->name("压力")->def("null")->size(2)->restrict(function($value){
            if (is_numeric($value)){
                return true;
            } else {
                return "压力只能为数值！";
            }
        })->size(3);
    	$this->item->col("ft")->type("string")->name("方式")->restrict("预制","安装")->size(3);
    	
    	$this->item->col("qid")->type("integer")->def("0")->name("质量计划")->bind("qp","id","CONCAT(qp_code,'(',version,')')")->bind_addition(array("暂不设置" => 0))->size(7);


    	$this->item->col("medium")->type("string")->name("介质")->bind("setting","setting_name",function($query){
            $query->where("setting_type","medium");
        })->bind_addition(array("N/A" => "N/A"));

        $this->item->col("pressure_test")->type("integer")->name("水压")->def("0")->restrict(0,1)->size(2);
        

        
        $this->item->col("level")->type("string")->name("焊缝级别")->def("null")->size(2);
        $this->item->col("exam_specify")->type("integer")->name("指定检验")->def("0")->input("init")->restrict("0","1")->size(2);
        $this->item->col("exam_specify_reason")->type("string")->name("指定理由")->def("null")->input("init")->size(2);
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

        $this->item->col("R")->type("integer")->def("0")->input("exec");//返修次数
        $this->item->col("R_id")->type("integer")->def("0")->input("exec");//对本焊口的返修
        $this->item->col("R_src")->type("integer")->def("0")->input("exec");//返修源焊口

    	$this->item->unique("project","ild","sys","pipeline","vnum");

        $this->item->cal(array("wj_type","medium","pressure","temperature","ac","bc"),"level",true,function($wj_type,$medium,$pressure,$temperature,$ac,$bc){
            if ($wj_type == "管道") {
                return level_cal($medium, $pressure, $temperature, $ac, $bc);
            } else {
                return false;
            }
        });
        $this->item->cal(array("exam_specify","level","pressure_test","ac","at","ath","bc","bt","bth","jtype"),array("RT","UT","PT","MT","SA","HB"),true,function($exam_specify,$level, $pressure_test, $ac, $at, $ath, $bc, $bt, $bth, $jtype){
            if ($exam_specify == 0) {
                return exam_rate_cal($level, $pressure_test, $ac, $at, $ath, $bc, $bt, $bth, $jtype);
            } else {
                return false;
            }
            
        });
        


        $this->default_col[] = "tsk_id";

        $this->status_control();

    }

    function tsk($builder){
        $builder->leftJoin('tsk','tsk.id',$this->get_table().".tsk_id")->leftJoin('wps','wps.id',"tsk.wps_id");
        return $builder;
    }
    function tsk_pure($builder){
        $builder->leftJoin('tsk','tsk.id',$this->get_table().".tsk_id");
        return $builder;
    }
    function exam($builder){
        $builder->leftJoin('exam','exam.exam_wj_id',$this->get_table().".id");
        return $builder;
    }

    
    //额外的禁止删除
    function addition_valid_deleting($data){
        if(!$value = $this->get_obj_data($data,array("tsk_id"))){
            $this->msg = "找不到对象";
            return false;
        }
        if ($data["tsk_id"] == 0 || $data["tsk_id"] == null) {
            return true;
        } else {
            $this->msg = "该焊口已经分配任务";
            return false;
        }
        
    }

    //（功能）增加新的检验
    function add_exam($wj_ids,$wj_sample_ids,$exam_plan){
        $plan = $exam_plan->ep_method."_plan";
        $weight = $exam_plan->ep_method."_weight";
        foreach ($wj_ids as $id) {
            $wj = $this->find($id);
            $wj->$plan = $wj->$plan."{".$exam_plan->id."}";
            if (in_array($id,$wj_sample_ids)) {
                $wj->$weight = 100;
            } else {
                $wj->$weight = $wj->$weight<$exam_plan->ep_weight?$exam_plan->ep_weight:$wj->$weight;
            }
            $wj->authorize_user("weld_syn");
            $wj->authorize_exec($plan,$weight);
            if (!$wj->save()) {
                die($wj->msg);
            }
        }
    }




    //焊缝新增清单
    function wj_add(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->add_model();
        $this->data->without("avail");
        $this->data->where("wj_type","管道");
        return $this->data->render();
    }

    //焊缝新增清单
    function wj_structure_add(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        $this->data->add_model();
        $this->data->without("avail");
        $this->data->where("wj_type","结构");
        return $this->data->render();
    }

    //焊缝清单
    function wj_list(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->col(array("vnum","vcode"),function($value,$data){
            return "<a href=\"###\" onclick=\"table_flavr('/wj/wj_detail?id=".$data["id"]."')\">".$value."</a>";
        });
        //$this->data->special_all = function($data){
            //return "onclick='table_flavr(\"/wj/wj_detail?id=".$data["id"]."\")'";
        //};
        return $this->data->render();
    }

    //焊缝执行情况清单
    function wj_exec_list(){
        $this->table_data(array("wj.id as wj_id",SQL_VCODE." as vcode",SQL_BASE." as type","tsk_date","tsk_pp_show","tsk_finish_date","RT_plan","UT_plan","PT_plan","MT_plan","SA_plan","HB_plan"),array("tsk_pure"));
        $this->data->col("vcode",function($value,$data){
            return "<a href=\"###\" onclick=\"table_flavr('/wj/wj_detail?id=".$data["id"]."')\">".$value."</a>";
        });
        $this->data->col(array("RT_plan","UT_plan","PT_plan","MT_plan","SA_plan","HB_plan"),function($value,$data){
            $show = "";
            foreach (multiple_to_array($value) as $v) {
                $show .= "[<a href=\"###\" onclick=\"table_flavr('/consignation/group_detail?id=".$v."')\">".$v."</a>]";
            }
            return $show;
        });
        $this->data->groupBy("wj.id");
        return $this->data->render();
    }

    //焊缝审核清单
    function manual_check(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->index(function($data){
            return "<input name=\"wj_id\" type=\"checkbox\" value=\"".$data["id"]."\" checked>";
        });
        $this->data->add_edit();
        $this->data->onlySoftDeletes();
        $this->data->unAvailable();
        $this->data->whereRaw("CHAR_LENGTH(wj.procedure) = 0");
        return $this->data->render();
    }
    //用于任务添加
    function wj_no_task(){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE." as type",SQL_EXAM_RATE." as rate","ft","qid","sys"));
        $this->data->where("tsk_id",0);
        $this->data->index(function($data){
            return "<input type=\"hidden\" id=\"sys_".$data["id"]."\" value=\"".$data["sys"]."\">";
        });
        $this->data->add_button("选择","wj_choose",function($data){return $data["id"];});
        return $this->data->render();
    }
    //用于变更水压试验
    function wj_alt_pressure_test(){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE." as type",SQL_EXAM_RATE." as rate","IF(pressure_test=1,'是','否') as ptest","pressure_test"),"user");
        $this->data->where("wj_type","管道");
        $this->data->add_button("选择","wj_choose",function($data){
            if ($data["procedure"] == "") {
                return $data["id"];
            }
            return "'[流程中]'";
        });
        return $this->data->render();
    }
    //用于指定检验比例
    function wj_alt_specify_rate(){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE." as type","RT","UT","PT","MT","SA","HB"),"user");
        $this->data->add_button("选择","wj_choose",function($data){
            if ($data["procedure"] == "") {
                return $data["id"];
            }
            return "'[流程中]'";
        });
        return $this->data->render();
    }
    //未委托的焊口，显示在委托分组
    function wj_no_consignation($emethod){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE." as type","CONCAT(".$emethod.",'%')","tsk_pp_show","CONCAT(wps_code,'(',wps.version,')') as wps","ild","sys","tsk_pp",$emethod),array("user","tsk"));
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

    //所有完成的焊口，显示在额外委托
    function wj_finished($emethod){
        $this->table_data(array("id","wj_type",SQL_VCODE." as vcode",SQL_BASE." as type","CONCAT(".$emethod.",'%')","tsk_pp_show","CONCAT(wps_code,'(',wps.version,')') as wps","ild","sys","tsk_pp",$emethod),array("user","tsk"));
        //$this->data->where($emethod,">",0);
        $this->data->whereNotNull("tsk.tsk_finish_date");
        //$this->data->whereRaw($emethod."_weight < ".$emethod);
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

    //焊口返修
    function wj_r(){
        $this->table_data($this->items_init(array("id","exam_conclusion"),"R"),"exam");
        $this->data->where("exam_conclusion","不合格");
        $this->data->where("R_id",0);
        $this->data->special_all = function($data){
            return "onclick='table_flavr(\"/console/dt_edit?model=wj&id=".$data["id"]."\")'";
        };
        $this->data->index(function($data){
            return "<button class=\"btn btn-warning btn-small\" onclick=\"dt_r(".$data["id"].")\">开启R".($data["R"]+1)."</button>";
        });
        return $this->data->render();
    }
    
    //焊口返修
    function wj_r_list(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->where("R",">",0);
        $this->data->without("avail");
        //$this->data->special_all = function($data){
            //return "onclick='table_flavr(\"/console/dt_edit?model=wj&id=".$data["id"]."\")'";
        //};
        $this->data->add_button("查看","table_flavr",function($data){
            return "/console/dt_edit?model=wj&id=".$data["id"];
        });
        $this->data->index(function($data){
            if ($this->status_control !== false && !$this->status_control->valid_status($data["status"])) {
                return "未生效";
            }
            return "";
        });
        return $this->data->render();
    }


    //焊缝可变更清单
    function wj_alt_data(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->special_all = function($data){
            return "onclick='table_flavr(\"/console/dt_edit?model=wj&id=".$data["id"]."\")'";
        };
        $this->data->where("tsk_id",0);
        $this->data->index(function($data){
            if (!$this->valid_updating($data)) {
                if (strlen($data["procedure"]) == 0) {
                    return "<button class=\"btn btn-default btn-small\" onclick=\"dt_alt_info('wj',".$data["id"].")\">变更</button>";
                } else {
                    return "【流程中】";
                }
            }
            return "";
        });
        return $this->data->render();
    }
    //焊缝可变更清单
    function wj_cancel_data(){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->special_all = function($data){
            return "onclick='table_flavr(\"/console/dt_edit?model=wj&id=".$data["id"]."\")'";
        };
        $this->data->where("tsk_id",0);
        $this->data->index(function($data){
            if (!$this->valid_updating($data)) {
                if (strlen($data["procedure"]) == 0) {
                    return "<input type=\"checkbox\" name=\"wj_id\" value=\"".$data["id"]."\">";
                } else {
                    return "【流程中】";
                }
            }
            return "";
        });
        return $this->data->render();
    }


    //焊缝完成统计
    function statistic_wj_finish(){
        if (isset($_GET["period"])) {
            switch ($_GET["period"]) {
                case 'day':
                    $period_item = "CONCAT(date_format(tsk_finish_date, '%y'),'/',date_format(tsk_finish_date, '%m'),'/',date_format(tsk_finish_date, '%e'))";
                    $group_item = DB::raw("date_format(tsk_finish_date, '%Y-%m-%d')");
                    break;
                case 'month':
                    $period_item = "CONCAT(date_format(tsk_finish_date, '%y'),'年',date_format(tsk_finish_date, '%m'),'月')";
                    $group_item = DB::raw("date_format(tsk_finish_date, '%Y-%M')");
                    break;
                case 'year':
                    $period_item = "CONCAT(date_format(tsk_finish_date, '%y'),'年')";
                    $group_item = DB::raw("date_format(tsk_finish_date, '%Y')");
                    break;
                
                default:
                    $period_item = "CONCAT(date_format(tsk_finish_date, '%y'),'年',date_format(tsk_finish_date, '%V'),'周')";
                    $group_item = DB::raw("date_format(tsk_finish_date, '%Y-%V')");
                    break;
            }
        } else {
            $period_item = "CONCAT(date_format(tsk_finish_date, '%y'),'年',date_format(tsk_finish_date, '%V'),'周')";
            $group_item = DB::raw("date_format(tsk_finish_date, '%Y-%V')");
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
        $this->table_data(array("wj.id as wj_id",$period_item." as period", $ild_item, $sys_item, "COUNT(wj.id) as amount"),"tsk_pure");
        if ($ild_item != "'全部'") {
            $this->data->where("ild",$_GET["ild"]);
        }
        if ($sys_item != "'全部系统'") {
            $this->data->where("sys",$_GET["sys"]);
        }
        $this->data->whereNotNull("tsk_finish_date");
        $this->data->groupBy($group_item);
        return $this->data->render();
    }
    

}
