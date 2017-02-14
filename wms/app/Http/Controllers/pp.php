<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class pp extends Controller
{
   

    function pp_add(){
        $model = new \App\pp();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","pp@pp_del");
        $sview->title(array("操作","钢印号","姓名","性别","生日","进场","离场","时间"));
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function pp_list(){
    }


}
