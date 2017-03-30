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
        $sview = new datatables("layouts/panel_table","tsk@tsk_list");
        $sview->title(array("操作","任务名称","任务日期","规格","焊接方法","质量计划","工艺卡","录入人","时间","焊工","完工日期"));
        //$sview->info("panel-body",$input_view->render());
        return $sview;
    }


    function tsk_finish(){
        //$model = new \App\qp();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("tsk/tsk_finish","tsk@tsk_not_finished");
        $sview->title(array("操作","任务名称","任务日期","规格","焊接方法","质量计划","工艺卡","录入人","时间"));
        //$sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function tsk_detail(){
        $model = new \App\tsk();
        $data = $model->onlySoftDeletes()->find($_GET["id"]);
        $wj = new \App\wj();
        $wjs = $wj->select(array(DB::raw("*"),DB::raw(SQL_VCODE." as wj_code"),DB::raw(SQL_EXAM_RATE." as rate"),DB::raw(SQL_BASE_TYPE." as type")))->where("tsk_id",$_GET["id"])->get();
        $wps = new \App\wps();
        $wps_data = $wps->find($data->wps_id);
        $qp = new \App\qp();
        $qp_data = $qp->find($data->qp_id);

        $sheet = new view("sheet/tsk_record",["info" => $wjs[0],"tsk" => $data,"qp" => $qp_data,"wps" => $wps_data]);

        $sview = new view("tsk/tsk_detail",["model" => $model,"data" => $data,"wj_model" => $wj,"wjs" => $wjs,"wps" => $wps_data,"sheet" => $sheet->render()]);
        return $sview;
    }


}
