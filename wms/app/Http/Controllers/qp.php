<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class qp extends Controller
{
   

    function qp_add(){
        $model = new \App\qp();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","qp@qp_del");
        $sview->title(array("操作","版本","机组","系统","编码","名称","录入人","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function qp_list(){
        $model = new \App\qp();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","qp@qp_update");
        $sview->title(array("操作","ID","版本","系统","编码","名称","录入人","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function qp_proc(){
        $model = new \App\qp_proc_model();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","qp_proc_model@qpm_list");
        $sview->title(array("操作","版本","名称","备注","条件","录入人","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function qp_proc_detail(){
        $model = new \App\qp_proc();
        $model->parent($_GET["id"]);
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/page_table_detail","qp_proc@qpp_list",$_GET["id"]);
        $sview->title(array("操作","工序号","名称","程序","QC2","QC3","行高","创建者","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function qp_proc_height_change(){
        $sview = new view("qp/qp_proc_height_change");
        return $sview;
    }

    function qp_proc_height_change_exec(){
        if (valid_post("id","height")) {
            $qp_proc = \App\qp_proc::find($_POST["id"]);
            $qp_proc->qpp_height = $_POST["height"];
            $qp_proc->authorize_user("weld_syn");
            if (!$qp_proc->save()) {
                die($qp_proc->msg);
            } else {
                $r = array(
                    "suc" => 1,
                    "msg" => "操作成功"
                );
                echo json_encode($r);
            }
        } else {
            die("数据错误");
        }
    }

}
