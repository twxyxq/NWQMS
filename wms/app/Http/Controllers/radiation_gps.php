<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use datatables;
use view;

class radiation_gps extends Controller
{
   

    function equipment(){
    	$equipment = \App\gps::where("gps_jz",0)->orderby("created_at","desc")->groupby("gps_SN")->get();
        $sview = new view("radiation_gps/equipment",["equipment" => $equipment]);
        return $sview;
    }

    function all_path(){
    	$position = DB::table("gps")->where("gps_SN",$_GET["sn"])->where("gps_jz",0)->whereNotNull("gps_lon")->whereNotNull("gps_lat")->orderBy("id","asc");
    	if (isset($_GET["start"]) && strlen($_GET["start"]) > 0) {
    		$position->where("created_at",">=",$_GET["start"]);
    	}
    	if (isset($_GET["end"]) && strlen($_GET["end"]) > 0) {
    		$position->where("created_at","<=",$_GET["end"]);
    	}
        return view("gps.gd_map",["position" => $position->get()]);
    }

    function current_path(){
    	return view("gps.gd_map_current",["sn" => $_GET["sn"]]);
    }

    function get_current(){
    	if (isset($_POST["sn"])) {
    		$position = DB::table("gps")->where("gps_SN",$_POST["sn"])->where("gps_jz",0)->whereNotNull("gps_lon")->whereNotNull("gps_lat")->orderBy("created_at","desc")->limit(20)->get();
    		$naviTransform = new \naviTransform();
    		$gps = array();
    		foreach ($position as $p) {
    			$pos_transform = $naviTransform->transform($p["gps_lat"],$p["gps_lon"]);
    			$gps[] = array($pos_transform[1],$pos_transform[0],$p["gps_Batt"],$p["created_at"]);
    		}
    		$r = array(
    				"suc" => 1,
    				"msg" => "获取成功",
    				"gps" => $gps
    			);
    		echo json_encode($r);
    	} else {
    		die("数据错误");
    	}
    }
}
