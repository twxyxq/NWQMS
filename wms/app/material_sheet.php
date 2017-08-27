<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class material_sheet extends table_model
{

    function column(){
    	$this->item->col("ms_tsk_ids")->type("string")->name("任务")->input("exec");
        $this->item->col("ms_title")->type("string")->name("标题");
        $this->item->col("ms_wj_ids")->type("string")->name("焊口")->input("exec");
        $this->item->col("ms_pp_ids")->type("string")->name("焊工ID")->input("exec");
        $this->item->col("ms_pp_show")->type("string")->name("焊工");
        $this->item->col("ms_m_type")->type("string")->name("焊材类型");
        $this->item->col("ms_type")->type("string")->name("焊材型号");
        $this->item->col("ms_diameter")->type("string")->name("直径");
        $this->item->col("ms_dep")->type("string")->name("部门")->def("null");
        $this->item->col("ms_amount")->type("decimal")->name("发放数量");

        $this->item->col("ms_store")->type("string")->name("存储")->def("null");
        $this->item->col("ms_s_id")->type("integer")->name("焊材ID")->def("0")->input("exec");
        $this->item->col("ms_s_show")->type("string")->name("焊材")->def("null");
        $this->item->col("ms_back_amount")->type("decimal")->name("回收数量")->def("0");
        $this->item->col("ms_time")->type("datetime")->name("发放时间")->def("null");
        $this->item->col("ms_back_time")->type("datetime")->name("回收时间")->def("null");
        $this->item->col("ms_by")->type("integer")->name("发放人")->def("0");
        $this->item->col("ms_back_by")->type("integer")->name("回收人")->def("0");

        $this->item->col("ms_spot")->type("integer")->name("点口")->def("0");

    }


    function secondary_store($builder){
        $builder->leftJoin('secondary_store','secondary_store.id',$this->get_table().".ms_s_id");
        return $builder;
    }


    function LOC(){
        $this->parent("LOC","ss_warehouse");
    }

    function PRE(){
        $this->parent("PRE","ss_warehouse");
    }

    //额外的禁止删除
    function addition_valid_deleting($data){
        if ($this->get_obj_data($data,"ms_time") == null) {
            return true;
        }
        $this->msg = "该焊材已发放，不能删除";
        return false;
    }


    function in_show($para){
        $this->$para();
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->add_del();
        return $this->data->render();
    }

    function out_show($para){
        $this->$para();
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        $this->data->whereNull("ss_out_date");
        $this->data->add_button("退库","add_out_form",function($data,$model){
            return array($data["id"],$data["ss_batch"],$data["ss_weight"],$data["ss_in_date"]);
        });
        return $this->data->render();
    }

    function ms_list($warehouse=""){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        if ($warehouse != "") {
            $this->data->where("ms_store",$warehouse);
        }
        $this->data->col("ms_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/material/sheet_detail','领用单详情',".$data["id"].")\">".$value."</a>";
        });
        $this->data->where("ms_spot",0);
        $this->data->add_del();
        return $this->data->render();
    }

    function ms_list_spot($warehouse=""){
        $this->table_data($this->items_init("id",array("name","created_at")),"user");
        if ($warehouse != "") {
            $this->data->where("ms_store",$warehouse);
        }
        $this->data->col("ms_title",function($value,$data){
            return "<a href=\"###\" onclick=\"detail_flavr('/material/sheet_detail','领用单详情',".$data["id"].")\">".$value."</a>";
        });
        $this->data->where("ms_spot",1);
        $this->data->add_del();
        return $this->data->render();
    }

    function store_record($para){
        $this->$para();
        $this->table_data($this->items("id",array("name","created_at")),"user");
        return $this->data->render();
    }

    function statistic_material_used(){
        if (isset($_GET["period"])) {
            switch ($_GET["period"]) {
                case 'day':
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'/',date_format(ms_time, '%m'),'/',date_format(ms_time, '%e'))";
                    $group_item = DB::raw("date_format(ms_time, '%Y-%m-%d')");
                    break;
                case 'month':
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'年',date_format(ms_time, '%m'),'月')";
                    $group_item = DB::raw("date_format(ms_time, '%Y-%M')");
                    break;
                case 'year':
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'年')";
                    $group_item = DB::raw("date_format(ms_time, '%Y')");
                    break;
                
                default:
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'年',date_format(ms_time, '%V'),'周')";
                    $group_item = DB::raw("date_format(ms_time, '%Y-%V')");
                    break;
            }
        } else {
            $period_item = "CONCAT(date_format(ms_time, '%y'),'年',date_format(ms_time, '%V'),'周')";
            $group_item = DB::raw("date_format(ms_time, '%Y-%V')");
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
        $this->table_data(array("material_sheet.id as ms_id",$period_item." as period", $ild_item, $sys_item, "SUM(IF(ms_m_type='焊条',ms_amount-ms_back_amount,0)) as rod", "SUM(IF(ms_m_type='焊丝',ms_amount-ms_back_amount,0)) as wire"));
        if ($ild_item != "'全部'") {
            $this->data->where("ms_title","LIKE",$_GET["ild"]."%");
        }
        if ($sys_item != "'全部系统'") {
            $this->data->where("ms_title","LIKE","%".$_GET["sys"]."%");
        }
        $this->data->whereNotNull("ms_time");
        $this->data->groupBy($group_item);
        return $this->data->render();
    }



    function statistic_material_used_dept(){
        if (isset($_GET["period"])) {
            switch ($_GET["period"]) {
                case 'day':
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'/',date_format(ms_time, '%m'),'/',date_format(ms_time, '%e'))";
                    $group_item = DB::raw("date_format(ms_time, '%Y-%m-%d')");
                    break;
                case 'month':
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'年',date_format(ms_time, '%m'),'月')";
                    $group_item = DB::raw("date_format(ms_time, '%Y-%M')");
                    break;
                case 'year':
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'年')";
                    $group_item = DB::raw("date_format(ms_time, '%Y')");
                    break;
                
                default:
                    $period_item = "CONCAT(date_format(ms_time, '%y'),'年',date_format(ms_time, '%V'),'周')";
                    $group_item = DB::raw("date_format(ms_time, '%Y-%V')");
                    break;
            }
        } else {
            $period_item = "CONCAT(date_format(ms_time, '%y'),'年',date_format(ms_time, '%V'),'周')";
            $group_item = DB::raw("date_format(ms_time, '%Y-%V')");
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
        $this->table_data(array("material_sheet.id as ms_id",$period_item." as period", $ild_item, $sys_item, "SUM(IF(ms_dep='热机',IF(ms_m_type='焊条',ms_amount-ms_back_amount,0),0)) as rj_rod", "SUM(IF(ms_dep='热机',IF(ms_m_type='焊丝',ms_amount-ms_back_amount,0),0)) as rj_wire", "SUM(IF(ms_dep='机械化',IF(ms_m_type='焊条',ms_amount-ms_back_amount,0),0)) as jxh_rod", "SUM(IF(ms_dep='机械化',IF(ms_m_type='焊丝',ms_amount-ms_back_amount,0),0)) as jxh_wire", "SUM(IF(ms_dep='电仪',IF(ms_m_type='焊条',ms_amount-ms_back_amount,0),0)) as dy_rod", "SUM(IF(ms_dep='电仪',IF(ms_m_type='焊丝',ms_amount-ms_back_amount,0),0)) as dy_wire"));
        if ($ild_item != "'全部'") {
            $this->data->where("ms_title","LIKE",$_GET["ild"]."%");
        }
        if ($sys_item != "'全部系统'") {
            $this->data->where("ms_title","LIKE","%".$_GET["sys"]."%");
        }
        $this->data->whereNotNull("ms_time");
        $this->data->groupBy($group_item);
        return $this->data->render();
    }



    function statistic_material_used_type(){
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
        $this->table_data(array("material_sheet.id as ms_id", "ms_type as period", "ms_diameter as d_addition_period","ms_dep as dep_addition_period", $period_item." as p_range", "SUM(IF(ms_store='PRE',ms_amount-ms_back_amount,0)) as pre_amount", "SUM(IF(ms_store='LOC',ms_amount-ms_back_amount,0)) as loc_amount", "SUM(ms_amount-ms_back_amount) as amount"));
        if (isset($_GET["sts_start"]) && strlen($_GET["sts_start"]) > 0) {
            $this->data->where("ms_time",">=",\Carbon\Carbon::createFromFormat('Y-m-d', $_GET["sts_start"])->toDateTimeString());
        }
        if (isset($_GET["sts_end"]) && strlen($_GET["sts_end"]) > 0) {
            $this->data->where("ms_time","<",\Carbon\Carbon::createFromFormat('Y-m-d', $_GET["sts_end"])->addDay()->toDateTimeString());
        }
        $this->data->whereNotNull("ms_time");
        $this->data->groupBy(DB::raw("CONCAT(ms_type,ms_diameter,ms_dep)"));
        return $this->data->render();
    }



    function statistic_material_used_trademark(){
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
        $this->table_data(array("material_sheet.id as ms_id", "ss_trademark as period", "ms_diameter as d_addition_period", "ms_dep as dep_addition_period" ,$period_item." as p_range", "SUM(IF(ms_store='PRE',ms_amount-ms_back_amount,0)) as pre_amount", "SUM(IF(ms_store='LOC',ms_amount-ms_back_amount,0)) as loc_amount", "SUM(ms_amount-ms_back_amount) as amount"),"secondary_store");
        if (isset($_GET["sts_start"]) && strlen($_GET["sts_start"]) > 0) {
            $this->data->where("ms_time",">=",\Carbon\Carbon::createFromFormat('Y-m-d', $_GET["sts_start"])->toDateTimeString());
        }
        if (isset($_GET["sts_end"]) && strlen($_GET["sts_end"]) > 0) {
            $this->data->where("ms_time","<",\Carbon\Carbon::createFromFormat('Y-m-d', $_GET["sts_end"])->addDay()->toDateTimeString());
        }
        $this->data->whereNotNull("ms_time");
        $this->data->where("ms_s_id",">",0);
        $this->data->groupBy(DB::raw("CONCAT(ss_trademark,ms_diameter,ms_dep)"));
        return $this->data->render();
    }
}
