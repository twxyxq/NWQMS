<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\wj_model;
use App\wj_base_model;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

//use Illuminate\Support\Facades\URL;


use view;
use datatables;
class test extends Controller
{
    function fd(){
        return "/".get_class($this)."_".__METHOD__."_data";
    }

    function index(){
        //echo action("test@show",["id" => 1]);
        //echo Crypt::decrypt("\$2y\$10\$mWNDyLCOOPqPh4L6rSzmPOnZxQdG8nWnaZyhxlGC2dFBZm7f9Bzm.");
        //DB::table("users")->update(["password" => Crypt::encrypt("XXXXXX")]);
        DB::table("users")->update(["password" => bcrypt("XXXXXX")]);
        echo 1;
    }


}
