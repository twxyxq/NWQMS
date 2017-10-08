<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

use datatables;
use view;

class radiation_gps extends Controller
{
   

    function equipment(){
    	$equipment = \App\gps::groupby("gps_SN")->get();
        $sview = new view("radiation_gps/equipment",["equipment" => $equipment]);
        return $sview;
    }

    

}
