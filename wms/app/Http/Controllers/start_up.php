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
        $app = new \App\User();
    }

    function user_password_default(){
        DB::table("users")->update(["password" => Crypt::encrypt("111111")]);
        return "1";
    }

    function user_password_default1(){
        DB::table("users")->update(["password" => bcrypt("111111")]);
        return "1";
    }

    

}
