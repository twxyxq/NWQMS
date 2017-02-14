<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\wj_model;
use App\wj_base_model;

use Illuminate\Database\Eloquent\Collection;

//use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

use view;
use datatables;
class setting extends Controller
{
    public $item = array(
            array("supplier","供应商","single"),
            array("wmtype","型号","double"),
            array("trademark","牌号","double")
        );

    public $model_name = "setting"; 

    //function __construct(){
        //parent::__construct("App\setting");
    //}





    function is_register($page){
        for ($i=0; $i < sizeof($this->item); $i++) { 
            if ($page == $this->item[$i][0]) {
                return $i+1;
            }
        }
        return false;
    }

    function index(){
        $sview = new view("setting/panel");
        return view("conn/top").$sview->render().view("conn/bottom");
    }


    function medium(){
        $sview = new datatables("setting/single","setting@single_view","medium");
        $sview->title(array("操作","<!--type_name-->","备注","录入人","时间"));
        $sview->info("type","medium");
        $sview->info("type_name","介质");
        return $sview;
    }

    function supplier(){
        $model = new \App\setting();
        $model->supplier();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","setting@single_view","supplier");
        $sview->title(array("操作","<!--type_name-->","备注","录入人","时间"));
        $sview->info("type_name","供应商");
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function basetype(){
        $model = new \App\setting();
        $model->basetype();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","setting@single_view","basetype");
        $sview->title(array("操作","<!--type_name-->","备注","录入人","时间"));
        $sview->info("type_name","母材类型");
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }


    function basemetal(){
        $model = new \App\setting();
        $model->basemetal();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","setting@double_view","basemetal");
        $sview->title(array("操作","<!--type_name-->","<!--r0-->","备注","录入人","时间"));
        //$sview->info("type","basemetal");
        $sview->info("type_name","母材材质");
        //$sview->info("type_r0","basetype");
        $sview->info("r0","母材类型");
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }




}
