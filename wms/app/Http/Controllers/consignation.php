<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class consignation extends Controller
{
   

    function manual_add(){
        $sview = new datatables("consignation/manual_add","wj@wj_no_consignation",$_GET["emethod"]);
        $sview->title(array("操作","类型","焊口号","规格",$_GET["emethod"]."比例","焊工","工艺"));
        //$sview->option("info: false");
        $sview->option("length: 5");
        //$sview->option("lengthChange: false");
        $sview->option("lengthMenu: [ 5, 10, 20 ]");
        return $sview;
    }

    function group_list(){
        $sview = new datatables("layouts/panel_table","exam_plan@ep_list");
        $sview->title(array("操作","分组名称","方法","类型","系统","焊工","工艺卡","录入人","完工日期"));
        //$sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function no_sheet(){
        $sview = new datatables("layouts/panel_table","exam@no_sheet_list");
        $sview->title(array("操作","分组名称","方法","类型","系统","焊工","工艺卡","录入人","完工日期"));
        //$sview->info("panel-body",$input_view->render());
        return $sview;
    }



}
