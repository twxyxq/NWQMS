<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class wps extends Controller
{
   

    function wps_add(){
        $model = new \App\wps();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wps@wps_del");
        $sview->title($model->titles(array("操作","版本"),array("创建者","时间")));
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function wps_proc(){
        $model = new \App\wps();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wps@wps_proc");
        $sview->title($model->titles(7,array("操作","版本"),array("创建者","时间")));
        $sview->info("panel-body","工艺评定生效流程");
        return $sview;
    }

    function wps_list(){
        $model = new \App\wps();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wps@wps_list");
        $sview->title($model->titles(7,array("操作","版本"),array("创建者","时间")));
        //$sview->info("panel-body","工艺评定生效流程");
        return $sview;
    }

    


}
