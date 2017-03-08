<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class material extends Controller
{
   

    function in(){
        $model = new \App\secondary_store();
        $model->$_GET["store"]();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","secondary_store@in_show",$_GET["store"]);
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function out(){
        $model = new \App\secondary_store();
        $model->$_GET["store"]();
        $sview = new datatables("material/wm_out","secondary_store@out_show",$_GET["store"]);
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        return $sview;
    }

    function store_list(){
        $model = new \App\secondary_store();
        $model->$_GET["store"]();
        $sview = new datatables("layouts/panel_table","secondary_store@store_list",$_GET["store"]);
        $sview->title($model->titles_init("序号",array("录入人","时间")));
        return $sview;
    }

    function store_record(){
        $model = new \App\secondary_store();
        $model->$_GET["store"]();
        $sview = new datatables("layouts/panel_table","secondary_store@store_record",$_GET["store"]);
        $sview->title($model->titles("序号",array("录入人","时间")));
        return $sview;
    }

    



}
