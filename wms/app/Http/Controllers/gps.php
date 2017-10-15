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
            if (isset($_POST["Alt"])) {
                $gps->gps_alt = $_POST["Alt"];
            }
            if (isset($_POST["Speed"])) {
                $gps->gps_speed = $_POST["Speed"];
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
            if (!isset($_POST["lon"]) && !isset($_POST["lat"]) && isset($_POST["LOC"]) && isset($_POST["CI"])) {
                $position = json_decode(file_get_contents("http://apilocate.amap.com/position?key=3026d4f895b5c5b38e42845cdb90c62f&accesstype=0&imei=352315052834187&cdma=0&bts=460,0,".hexdec($_POST["LOC"]).",".hexdec($_POST["CI"]).",-65&output=json"));
                $location = explode(",",$position->result->location);
                $gps->gps_lon = $location[0];
                $gps->gps_lat = $location[1];
                $gps->gps_jz = 1;
            }
            $gps->created_by = -1;
            $gps->save();
            echo json_encode($_POST);
        }
        //return "aaa";
    }

    function index(){
        exit(-1);
        $position = DB::table("gps")->whereNotNull("gps_lon")->whereNotNull("gps_lat")->orderBy("id","asc")->get();
        return view("gps.gd_map",["position" => $position]);
    }

    function cal(){
        $msg = "";
        foreach (\App\gps::whereNull("gps_lon")->whereNull("gps_lat")->get() as $gps) {
            if (isset($gps->gps_LOC) && isset($gps->gps_CI)) {
                $position = json_decode(file_get_contents("http://apilocate.amap.com/position?key=3026d4f895b5c5b38e42845cdb90c62f&accesstype=0&imei=352315052834187&cdma=0&bts=460,0,".hexdec($gps->gps_LOC).",".hexdec($gps->gps_CI).",-65&output=json"));
                $location = explode(",",$position->result->location);
                $msg .= $position->result->location."<br>";
                $gps->gps_lon = $location[0];
                $gps->gps_lat = $location[1];
                $gps->gps_jz = 1;
                $gps->authorize_user(1);
                if (!$gps->save()) {
                    $msg .= $gps->msg."<br>";
                }
            }
        }
        return $msg;
    }

    

}
