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

    

}
