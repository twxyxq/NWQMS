<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class alternation extends Controller
{
   
	//检验任务列表
    function alt_data_add(){
        $model = new \App\wj();
        $sview = new \datatables("layouts/panel_table","wj@wj_alt_data");
        $sview->title($model->titles_init("操作"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
	//检验任务列表
    function alt_data_check(){
        $sview = new \datatables("layouts/panel_table","procedure@alt_data_check");
        $sview->title(array("操作","流程类型","焊口数","当前责任人","发起人","发起时间"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
	//检验任务列表
    function alt_data_list(){
        $model = new \App\wj();
        $sview = new \datatables("layouts/panel_table","procedure@alt_data_list");
        $sview->title(array("操作","流程类型","焊口数","发起人","发起时间","完成时间"));
        //$sview->info("panel_body",$input_view->render());
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
    //水压变更待审批
    function alt_pressure_test_check(){
        $sview = new \datatables("layouts/panel_table","procedure@alt_pressure_test_check");
        $sview->title(array("操作","流程类型","焊口数","当前责任人","发起人","发起时间"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //水压变更完成清单
    function alt_pressure_test_list(){
        $model = new \App\wj();
        $sview = new \datatables("layouts/panel_table","procedure@alt_pressure_test_list");
        $sview->title(array("操作","流程类型","焊口数","发起人","发起时间","完成时间"));
        //$sview->info("panel_body",$input_view->render());
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
    //指定检验比例待审批
    function alt_specify_rate_check(){
        $sview = new \datatables("layouts/panel_table","procedure@alt_exam_specify_check");
        $sview->title(array("操作","流程类型","焊口数","当前责任人","发起人","发起时间"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //指定检验比例完成清单
    function alt_specify_rate_list(){
        $model = new \App\wj();
        $sview = new \datatables("layouts/panel_table","procedure@alt_exam_specify_list");
        $sview->title(array("操作","流程类型","焊口数","发起人","发起时间","完成时间"));
        //$sview->info("panel_body",$input_view->render());
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
    //焊口作废页面
    function wj_cancel_detail(){
        $model = new \App\wj();
        $sview = new \datatables("wj/wj_cancel_detail",$model->whereIn("id",multiple_to_array($_GET["ids"]))->select($model->items_init("id"))->get()->toArray());
        $sview->title($model->titles_init("操作"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }


}
