<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\wj_model;
use App\wj_base_model;

use Illuminate\Database\Eloquent\Collection;

//use Illuminate\Support\Facades\URL;


use view;
use datatables;
class test extends Controller
{
    function fd(){
        return "/".get_class($this)."_".__METHOD__."_data";
    }

    function index(){
        echo action("test@show",["id" => 1]);
    }


}
