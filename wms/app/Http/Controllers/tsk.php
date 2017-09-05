<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use datatables;
use view;

class tsk extends Controller
{
   

    function tsk_add(){
        $sview = new datatables("tsk/tsk_add","wj@wj_no_task");
        $sview->title(array("操作","类型","焊口号","规格","检验比例","方法","QP"));
        $sview->option("info: false");
        $sview->option("length: 5");
        //$sview->option("lengthChange: false");
        $sview->option("lengthMenu: [ 5, 10, 20 ]");
        return $sview;
    }

    function tsk_list(){
        //$model = new \App\qp();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("tsk/tsk_list","tsk@tsk_list");
        $sview->title(array("操作","任务名称","任务日期","规格","焊接方法","质量计划","工艺卡","录入人","时间","焊工","完工日期"));
        $sview->order(2,"desc");
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function tsk_unfinished_list(){
        //$model = new \App\qp();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("tsk/tsk_list","tsk@tsk_unfinished_list");
        $sview->title(array("操作","任务名称","任务日期","规格","焊接方法","质量计划","工艺卡","录入人","时间","焊工","完工日期"));
        $sview->order(2,"desc");
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function tsk_my_list(){
        //$model = new \App\qp();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("tsk/tsk_list","tsk@tsk_my_list");
        $sview->title(array("操作","任务名称","任务日期","规格","焊接方法","质量计划","工艺卡","录入人","时间","焊工","完工日期"));
        $sview->order(2,"desc");
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }


    function tsk_finish(){
        //$model = new \App\qp();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("tsk/tsk_finish","tsk@tsk_not_finished");
        $sview->title(array("操作","任务名称","任务日期","规格","焊接方法","质量计划","工艺卡","录入人","时间"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function tsk_detail(){
        

        $model = new \App\tsk();
        $wj = new \App\wj();

        if (isset($_GET["id"])) {
            $id = $_GET["id"];
        } else if (isset($_GET["wj_id"])){
            $id = $wj->find($_GET["wj_id"])->tsk_id;
        } else {
            die("数据传入不合法");
        }


       
        $data = $model->onlySoftDeletes()->find($id);

        $sview = new view("tsk/tsk_detail",["data" => $data]);
        return $sview;
    }

    function sheets(){
        $tsks = \App\tsk::whereIn("id",multiple_to_array($_GET["ids"]))->get();
        $html = "<button class=\"btn btn-success\" onclick=\"print_object('#print_all')\">打印全部</button>";
        $html .= "<div id=\"print_all\"><div style=\"page-break-after:always\"></div>";
        foreach ($tsks as $tsk) {
            $html .= view("sheet/tsk_record",["tsk" => $tsk])->render();
        }
        $html .= "</div>";
        $sview = new view("layouts/page_detail",["panel_body" => $html]);
        return $sview;
    }

    //(POST,页面)完工录入表单
    function tsk_finish_form(){

        //如果是POST，返回json，如果是GET，返回页面
        if (isset($_GET["id"])) {
            $id = $_GET["id"];       
        } else if (isset($_POST["id"])) {
            $id = $_POST["id"]; 
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "失败"
            );
            die(json_encode($r));
        }

        //完工录入表格均是form.tsk_finish_form
        $form = new \view("form.tsk_finish_form",["id" => $id]);

        //如果是POST，返回json，如果是GET，返回页面
        if (isset($_GET["id"])) {
            $sview = new view("layouts/page_detail");
            $sview->info("panel_body",$form->render());
            return $sview;
        } else if (isset($_POST["id"])) {
            $r = array(
                "suc" => 1,
                "msg" => "成功",
                "form" => $form->render()
            );
            die(json_encode($r));
        }
        
    }

    //(POST)完工录入
    function tsk_finished(){
        if (isset($_POST["id"]) && isset($_POST["tsk_pp"]) && isset($_POST["tsk_finish_date"])) {

            
            $pp_array = array();
            for ($i=0; $i < sizeof($_POST["tsk_pp"]); $i++) { 
                $pp_array[$_POST["tsk_pp"][$i]] = $_POST["tsk_pp_proportion"][$i];
            }
            ksort($pp_array);
            $pp_ids = array_keys($pp_array);
            $pp_proportions = array_values($pp_array);


            $tsk = new \App\tsk();
            //data为写入的数据
            $data = $tsk->find($_POST["id"]);

            $pp = new \App\pp();
            $pps = $pp->whereIn("id",$pp_ids)->get();
            $tsk_pp_show = "";
            foreach ($pps as $p) {
                $tsk_pp_show .= "/".$p->pcode." ".$p->pname;
            }
            $data->tsk_pp_show = substr($tsk_pp_show,1);



            
            $data->tsk_pp = array_to_multiple($pp_ids);
            $data->tsk_pp_proportion = array_to_multiple($pp_proportions);
            $data->tsk_finish_date = $_POST["tsk_finish_date"];
            $data->tsk_input_time = \Carbon\Carbon::now();
            $data->tsk_input_p = Auth::user()->id;
            $data->authorize_user("weld_syn");
            if ($data->save()) {
                $r = array(
                    "suc" => 1,
                    "msg" => "成功",
                    "tsk_pp_show" => $data->tsk_pp_show,
                    "tsk_pp_proportion" => $data->tsk_pp_proportion,
                    "tsk_finish_date" => $data->tsk_finish_date
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => 0,
                    "msg" => $data->msg
                );
                die(json_encode($r));
            }
            
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "指令错误"
            );
            die(json_encode($r));
        }
        
    }

    //(POST)任务添加执行
    function tsk_add_exec(){
        $data = $_POST;
        unset($data["_token"]);
        unset($data["_method"]);
        DB::transaction(function() use ($data){
            $wj_model = new \App\wj();
            $wps_model = new \App\wps();
            $qp_model = new \App\qp();
            $suc_tsk_ids = array();
            $html = "";
            //try{
                foreach ($data as $key => $value) {
                    $wj = $wj_model->select("*",DB::raw(SQL_BASE." as wj_spec"))->whereIn("id",multiple_to_array($value[0]))->get();
                    if (sizeof($wj) == 0) {
                        die("焊口已删除");
                    }
                    if ($wj[0]->tsk_id > 0) {
                        die("已经添加任务");
                    }
                    //获取工艺卡
                    $wps = $wps_model->find($value[3]);
                    //获取质量计划
                    $qp = $qp_model->find($value[1]);
                    //后续需加上对不同类别的验证
                    $task = new \App\tsk();
                    if (strpos($value[0], "{") === 0) {
                        $task->wj_ids = $value[0];
                    } else {
                        $task->wj_ids = "{".$value[0]."}";
                    }
                    $task->tsk_title = $wj[0]->vcode;
                    if (sizeof($wj) > 1) {
                        $task->tsk_title .= "等（".sizeof($wj)."道）";
                    }
                    $task->qp_id = $value[1];
                    $task->tsk_ft = $value[2];
                    $task->wps_id = $value[3];
                    $task->tsk_date = \Carbon\Carbon::today();
                    $task->tsk_identity = $wj[0]->ild.$wj[0]->sys;//先使用第一个值，后续需添加验证
                    $task->tsk_identity_record = $task->where("tsk_identity",$task->tsk_identity)->count()+1;
                    $task->tsk_print_history = Auth::user()->id.":".\Carbon\Carbon::now();
                    $task->tsk_wmethod = $wps->wps_method;
                    $task->tsk_wj_spec = $wj[0]->wj_spec;
                    $task->tsk_qp = $qp->qp_code.$qp->qp_name;
                    if (!$task->save()) {
                        die($task->msg);
                    }
                    //DB::table("wj")->whereIn("id",multiple_to_array($value[0]))->update(["tsk_id" => $task->id,"qid" => $value[1]]);
                    $wj[0]->tsk_id = $task->id;
                    $wj[0]->qid = $value[1];
                    $wj[0]->authorize_user("weld_syn");
                    $wj[0]->authorize_exec("tsk_id","qid");
                    if (!$wj[0]->save()) {
                        die($wj[0]->msg);
                    }
                    $suc_tsk_ids[] = $task->id;
                }
                $r = array(
                    "suc" => 1,
                    "tsk_ids" => $suc_tsk_ids,
                    "msg" => "任务添加成功",
                    "print" => $html
                );
                echo(json_encode($r));
            //} catch(\Exception $e){
                //$r = array(
                    //"suc" => -1,
                    //"msg" => "操作失败"
                //);
                //die(json_encode($r));
            //}
            
        });
    }


    

}
