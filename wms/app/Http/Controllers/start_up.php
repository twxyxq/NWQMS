<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use datatables;
use view;

class start_up extends Controller
{
   

    function index(){
        $app = new \App\eps();
        $app = new \App\exam();
        $app = new \App\exam_item();
        $app = new \App\exam_plan();
        $app = new \App\exam_report();
        $app = new \App\exam_sheet();
        $app = new \App\gps();
        $app = new \App\material_sheet();
        $app = new \App\modify_history();
        $app = new \App\pp();
        $app = new \App\procedure();
        $app = new \App\procedure_item();
        $app = new \App\qf_range();
        $app = new \App\qp();
        $app = new \App\qp_proc();
        $app = new \App\qp_proc_model();
        $app = new \App\qualification();
        $app = new \App\secondary_store();
        $app = new \App\setting();
        $app = new \App\tsk();
        $app = new \App\User();
        $app = new \App\validation();
        $app = new \App\validation_plan();
        $app = new \App\wj();
        $app = new \App\wj_base();
        $app = new \App\wj_model();
        $app = new \App\wpq();
        $app = new \App\wps();
    }

    function user_password_default(){
        DB::table("users")->update(["password" => Crypt::encrypt("111111")]);
    }

    

}
