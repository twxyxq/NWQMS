<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class wpq extends Controller
{
   

    function wpq_add(){
        $model = new \App\wpq();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wpq@wpq_del");
        $sview->title($model->titles(array("操作","版本"),array("创建者","时间")));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function wpq_proc(){
        $model = new \App\wpq();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wpq@wpq_proc");
        $sview->title($model->titles(7,array("操作","版本"),array("创建者","时间")));
        $sview->info("panel_body","工艺评定生效流程");
        return $sview;
    }

    function wpq_list(){
        $model = new \App\wpq();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wpq@wpq_list");
        $sview->title($model->titles(7,array("操作","版本"),array("创建者","时间")));
        //$sview->info("panel_body","工艺评定生效流程");
        return $sview;
    }

    


}
