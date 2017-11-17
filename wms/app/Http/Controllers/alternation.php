<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class alternation extends Controller
{

    public $default_page = "alt_redirect";

    function alt_redirect($para){
        if (strpos($para,"[c]") === false) {
            return $this->alt_list($para);
        } else {
            return $this->alt_check(substr($para,3));
        }
    }
    
    //信息变更待审批
    function alt_check($para){
        $sview = new \datatables("layouts/panel_table","procedure@proc_check",$para);
        $sview->title(array("操作","流程类型","数量","当前责任人","发起人","发起时间"));
        $sview->order(5,"desc");
        return $sview;
    }
    //信息变更列表
    function alt_list($para){
        $model = new \App\wj();
        $sview = new \datatables("layouts/panel_table","procedure@proc_list",$para);
        $sview->title(array("操作","流程类型","数量","发起人","发起时间","完成时间"));
        $sview->order(5,"desc");
        return $sview;
    }


    //信息变更添加
    function alt_data_add(){
        $model = new \App\wj();
        $sview = new \datatables("layouts/panel_table","wj@wj_alt_data");
        $sview->title($model->titles_init("操作"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //信息变更添加
    function alt_data_all_add(){
        $model = new \App\wj();
        $sview = new \datatables("layouts/panel_table","wj@wj_alt_data_all");
        $sview->title($model->titles_init("操作"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //误删恢复添加
    function tsk_recovery_add(){
        $model = new \App\tsk();
        $sview = new \datatables("layouts/panel_table","tsk@tsk_recovery_add");
        $sview->title(array("操作","任务名称","任务日期","规格","焊接方法","质量计划","工艺卡","录入人","删除时间"));
        $sview->order(8,"desc");
        return $sview;
    }
    //压力变更添加
    function alt_pressure_test_add(){
        $sview = new \datatables("alternation/alt_pressure_test","wj@wj_alt_pressure_test");
        $sview->title(array("操作","类型","焊口号","规格","检验比例","水压试验"));
        $sview->option("info: false");
        $sview->option("length: 5");
        //$sview->option("lengthChange: false");
        $sview->option("lengthMenu: [ 5, 10, 20 ]");
        return $sview;
    }
    //指定检验比例添加
    function alt_specify_rate_add(){
        $sview = new \datatables("alternation/alt_specify_rate","wj@wj_alt_specify_rate");
        $sview->title(array("操作","类型","焊口号","规格","RT","UT","PT","MT","SA","HB"));
        $sview->option("info: false");
        $sview->option("length: 5");
        //$sview->option("lengthChange: false");
        $sview->option("lengthMenu: [ 5, 10, 20 ]");
        return $sview;
    }
    //焊口作废
    function cancel_add(){
        $model = new \App\wj();
        $sview = new \datatables("wj/wj_cancel","wj@wj_cancel_data");
        $sview->title($model->titles_init("操作"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //报告作废
    function cancel_report_procedure_add(){
        $model = new \App\exam_report();
        $sview = new \datatables("layouts/panel_table","exam_report@report_cancel_list");
        $sview->title($model->titles_init(array("操作"),array("创建人","时间")));
        return $sview;
    }
    //焊口作废
    function cancel_exam_plan_add(){
        $sview = new \datatables("layouts/panel_table","exam_plan@ep_cancel_list");
        $sview->title(array("操作","分组名称","方法","类型","系统","焊工","工艺卡","创建人","日期"));
        $sview->order(8,"desc");
        return $sview;
    }
    //委托单撤销
    function cancel_exam_sheet_add(){
        $sview = new \datatables("layouts/panel_table","exam_sheet@sheet_cancel_list");
        $sview->title(array("序号","委托单号","检验方法","焊口类型","系统","录入人","日期"));
        $sview->order(6,"desc");
        return $sview;
    }
    //委托单修改
    function modify_exam_sheet_add(){
        $sview = new \datatables("layouts/panel_table","exam_sheet@sheet_modify_list");
        $sview->title(array("序号","委托单号","检验方法","焊口类型","系统","录入人","日期"));
        $sview->order(6,"desc");
        return $sview;
    }
    //领用单变更
    function alt_material_sheet_add(){
        $model = new \App\material_sheet();
        $sview = new \datatables("layouts/panel_table",["width" => "1950px"],"material_sheet@ms_alt_list",isset($_GET["warehouse"])?$_GET["warehouse"]:"");
        $sview->title($model->titles_init("序号",array("录入人","时间")));
        $sview->order(17,"desc");
        return $sview;
    }


    //（详细页）委托单修改表格
    function alt_material_sheet_form(){
        $material_sheet = \App\material_sheet::find($_GET["id"]);
        $sview = new \view("alternation/alt_material_sheet_form",["material_sheet" => $material_sheet]);
        return $sview;
    }
    //（POST）委托单修改执行
    function alt_material_sheet_exec(){
        if (valid_post("ms_title","ms_pp_ids","id","ms_amount","ms_back_amount")) {

            if (!is_numeric($_POST["ms_amount"]) || !is_numeric($_POST["ms_back_amount"])) {
                die("输入数值不合法");
            }


            try{
                $proc = new \App\procedure\alt_procedure("","material_sheet",$_POST["id"]);
                $pd_class = "alt_procedure";
                $proc->name($_POST["ms_title"]."领用单变更");

                $material_sheet = \App\material_sheet::find($_POST["id"]);
                $dirty = array();
                $original = array();

                $original_pp_ids = multiple_to_array($material_sheet->ms_pp_ids);
                sort($_POST["ms_pp_ids"]);
                sort($original_pp_ids);
                if ($_POST["ms_pp_ids"] != $original_pp_ids) {
                    $dirty["ms_pp_ids"] = array_to_multiple($_POST["ms_pp_ids"]);
                    $original["ms_pp_ids"] = $material_sheet->ms_pp_ids;
                    $pp = \App\pp::whereIn("id",$_POST["ms_pp_ids"])->get();
                    $dirty["ms_pp_show"] = "";
                    foreach ($pp as $p) {
                        $dirty["ms_pp_show"] .= "/".$p->pcode." ".$p->pname;
                    }
                    $dirty["ms_pp_show"] = substr($dirty["ms_pp_show"],1);
                    $original["ms_pp_show"] = $material_sheet->ms_pp_show;
                }
                if (floatval($_POST["ms_amount"]) != floatval($material_sheet->ms_amount)) {
                    $dirty["ms_amount"] = $_POST["ms_amount"];
                    $original["ms_amount"] = $material_sheet->ms_amount;
                }
                if (floatval($_POST["ms_back_amount"]) != floatval($material_sheet->ms_back_amount)) {
                    $dirty["ms_back_amount"] = $_POST["ms_back_amount"];
                    $original["ms_back_amount"] = $material_sheet->ms_back_amount;
                }

                if (sizeof($dirty) == 0) {
                    die("没有任何更改");
                }
                    
                $proc->info(json_encode(array("dirty" => $dirty, "original" => $original)));
                if (!$proc->create_proc()) {
                    $r = array(
                        "suc" => -1,
                        "msg" => $proc->msg
                    );
                    echo(json_encode($r));
                } else {
                    $r = array(
                        "suc" => 1,
                        "msg" => "流程创建成功",
                        "proc_id" => $proc->proc_id,
                        "pd_class" => $pd_class
                    );
                    echo(json_encode($r));
                }
            } catch(\Exception $e){
                $r = array(
                    "suc" => -1,
                    "msg" => "流程创建失败。".$e->getMessage()
                );
                die(json_encode($r));
            }
            
        } else {
            die("数据错误");
        }
    }

    


}
