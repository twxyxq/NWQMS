<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use datatables;
use view;

class gps extends Controller
{
   

    function store(){
        if (isset($_POST)) {
            $gps = new \App\gps();
            $gps->gps_info = json_encode($_POST);
            if (isset($_POST["lon"])) {
                $gps->gps_lon = $_POST["lon"];
            }
            if (isset($_POST["lat"])) {
                $gps->gps_lat = $_POST["lat"];
            }
            if (isset($_POST["alt"])) {
                $gps->gps_alt = $_POST["alt"];
            }
            if (isset($_POST["speed"])) {
                $gps->gps_speed = $_POST["speed"];
            }
            if (isset($_POST["Batt"])) {
                $gps->gps_Batt = $_POST["Batt"];
            }
            if (isset($_POST["SN"])) {
                $gps->gps_SN = $_POST["SN"];
            }
            if (isset($_POST["LOC"])) {
                $gps->gps_LOC = $_POST["LOC"];
            }
            if (isset($_POST["CI"])) {
                $gps->gps_CI = $_POST["CI"];
            }
            $gps->created_by = -1;
            $gps->save();
            echo json_encode($_POST);
        }
        //return "aaa";
    }

    function index(){
        return view("gps.map");
    }

    

}
