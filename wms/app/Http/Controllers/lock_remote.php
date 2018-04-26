<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use datatables;
use view;

class lock_remote extends Controller
{
   

    function store(){
        if (isset($_POST["sn"])) {
            $lock = \App\ai_lock::where("ai_lock_code",$_POST["sn"])->get();
            if (sizeof($lock) > 0) {
                $auth = \App\ai_lock_auth::where("ala_lock_id",$lock[0]->id)->where("created_at",">",\Carbon\Carbon::now()->subWeek())->get();
                if (sizeof($auth) > 0) {
                    echo $auth[0]->ala_auth_users;
                } else {
                    die("false");
                }
            } else {
                die("false");
            }
        }
    }

    function index(){
        if (isset($_GET["sn"])) {
            $lock = \App\ai_lock::where("ai_lock_code",$_GET["sn"])->get();
            if (sizeof($lock) > 0) {
                $auth = \App\ai_lock_auth::where("ala_lock_id",$lock[0]->id)->where("created_at",">",\Carbon\Carbon::now()->subWeek())->get();
                if (sizeof($auth) > 0) {
                    echo $auth[0]->ala_auth_users;
                } else {
                    die("false");
                }
            } else {
                die("false");
            }
        }
    }



}
