<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class interior_management extends Controller
{
   

    function account_book_list(){
    	$book = \App\account_book::all();
        $sview = new view("interior_management/account_book_list",["book" => $book]);
        return $sview;
    }

    function overtime_personal(){
        $sview = new datatables("interior_management/overtime_personal","overtime@overtime_personal");
        $sview->title(array("操作","日期","时长","审批状态"));
        return $sview;
    }

    function overtime_examine_and_approve(){
    	$book = \App\overtime::all();
        $sview = new view("interior_management/overtime",["book" => $book]);
        return $sview;
    }

    function overtime_statistic(){
    	$book = \App\overtime::all();
        $sview = new view("interior_management/overtime",["book" => $book]);
        return $sview;
    }

    function my_report(){
        $sview = new datatables("interior_management/my_report","work_report@my_report");
        $sview->title(array("操作","分类","标题","内容","重要性"));
        if(strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false){
            $sview->option("searching: false");
            $sview->option("lengthChange: false");
        }
        return $sview;
    }

    function current_report(){
        $report = \App\work_report::orderByRaw('FIELD(wr_type,"总体","核岛土建","核岛安装","常规岛")')->orderByRaw('FIELD(wr_level,"极高","高","中","普通","-")')->get();
        $sview = new view("interior_management/current_report",["report" => $report]);
        return $sview;
    }

    

}
