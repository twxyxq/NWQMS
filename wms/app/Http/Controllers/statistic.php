<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use datatables;
use view;

class statistic extends Controller
{
   

    function wj_finish(){
        $sview = new datatables("statistic/wj_finish","wj@statistic_wj_finish");
        $sview->title(array("选择","统计周期", "机组", "系统", "数量"));
        $sview->order([1, "asc"]);
        return $sview;
    }


    function exam_amount(){
        $sview = new datatables("statistic/exam_amount","exam@statistic_exam_amount");
        $sview->title(array("选择","统计周期", "方法", "机组", "系统", "检验数量", "报告出版"));
        $sview->order([1, "asc"]);
        return $sview;
    }

    function exam_pass_rate(){
        $sview = new datatables("statistic/exam_pass_rate",["item_name" => "焊口"],"exam@statistic_exam_pass_rate");
        $sview->title(array("选择","统计周期", "方法", "机组", "系统", "检验数量", "不合格","合格率(%)"));
        $sview->order([1, "asc"]);
        return $sview;
    }

    function exam_pass_rate_weight(){
        $sview = new datatables("statistic/exam_pass_rate",["item_name" => "当量"],"exam_item@statistic_exam_pass_rate");
        $sview->title(array("选择","统计周期", "方法", "机组", "系统", "检验数量", "不合格","合格率(%)"));
        $sview->order([1, "asc"]);
        return $sview;
    }

    function exam_pass_rate_weight_pp(){
        $sview = new datatables("statistic/exam_pass_rate_pp",["item_name" => "焊工"],"pp@statistic_exam_pass_rate_pp");
        $sview->title(array("选择","焊工", "方法", "统计周期", "检验数量", "不合格","合格率(%)"));
        $sview->order([1, "asc"]);
        return $sview;
    }

    function material_used(){
        $sview = new datatables("statistic/material_used","material_sheet@statistic_material_used");
        $sview->title(array("选择","统计周期", "机组", "系统", "焊条(根)", "焊丝(根)"));
        $sview->order([1, "asc"]);
        return $sview;
    }

    function material_used_dept(){
        $sview = new datatables("statistic/material_used_dept","material_sheet@statistic_material_used_dept");
        $sview->title(array("选择","统计周期", "机组", "系统", "热机焊条", "热机焊丝", "机械化焊条", "机械化焊丝", "电仪焊条", "电仪焊丝"));
        $sview->order([1, "asc"]);
        return $sview;
    }

    function material_used_type(){
        $sview = new datatables("statistic/material_used_type","material_sheet@statistic_material_used_type");
        $sview->title(array("选择", "型号", "直径", "部门", "统计周期", "准备区", "现场", "合计"));
        $sview->order([1, "asc"]);
        return $sview;
    }

    function material_used_trademark(){
        $sview = new datatables("statistic/material_used_type","material_sheet@statistic_material_used_trademark");
        $sview->title(array("选择", "牌号", "直径", "部门", "统计周期", "准备区", "现场", "合计"));
        $sview->order([1, "asc"]);
        return $sview;
    }


}
