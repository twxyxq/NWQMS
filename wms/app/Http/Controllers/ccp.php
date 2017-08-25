<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class ccp extends Controller
{
   

    function node(){
        $sview = new view("ccp/node");
        return $sview;
    }

    

}
