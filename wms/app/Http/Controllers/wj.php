<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\wj_model;
use App\wj_base_model;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class wj extends Controller
{
   

    function wj_list(){
        //$this->dd = "dd";
        //echo $this->dd;
        $wj_list = new datatables("wj/wj_list_data");
        $wj_list->db("dd");
        $wj_list->title(array("1","2"));
        return view("conn/top").$wj_list->render().view("conn/bottom");
    }

    function wj_list_data(){
        echo $_GET["db"];
    }

    function wj_del(){
        $ee = new wj_base_model();
        $ee->find(6)->delete();
    }

    function excel_input(){
        $sview = new view("wj/excel_input");
        return $sview;
    }

    function import($file){
        //$new_file = public_path('uploads/excel/'.date("y-m-d-H-i-s").'.xls');
        //move_uploaded_file(base_path(substr($file->getRealPath(),1)),$new_file);
        $file = $file->move(public_path("uploads/excel"),date("y-m-d-H-i-s")."-".Auth::user()->id.".xls");
        //$file->getClientOriginalName();
        $data = Excel::load($file, function($reader) {

            $reader->dump();

        });
        //print_r($data);
    }

    function manual_input(){
        $sview = new view("wj/manual_input");
        return $sview;
    }

    function wj_single_add(){
        $model = new \App\wj();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wj@wj_list");
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

}
