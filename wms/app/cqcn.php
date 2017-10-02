<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;


require_once "table_model.php";

class cqcn extends table_model
{
    //

    function column(){
        $this->item->col("cqcn_type")->type("string")->name("证书类型")->restrict("民用核安全设备","特种设备");
        $this->item->col("cqcn_code")->type("string")->name("证书编号");
        $this->item->col("cqcn_method")->type("string")->name("方法")->restrict("RT","UT","PT","MT","ET","LT","VT");
        $this->item->col("cqcn_level")->type("string")->name("等级")->restrict("Ⅰ","Ⅱ","Ⅲ");
        $this->item->col("cqcn_expire_date")->type("date")->name("有效期");

    	$this->item->unique("cqcn_code");
    }


    function haf(){
        $this->parent("核级焊工证","qf_type");
    }


    function cqcn_del(){
        $this->table_data(array("id","cqcn_type","cqcn_code","CONCAT('cqcn_method',' ','cqcn_level')","cqcn_expire_date"));
        $this->data->add_del();
        $this->data->orderby("cqcn_expire_date","asc");
        //$this->data->add_button("查看","new_flavr",function($data){
            //return $data["qf_src"];
        //});
        return $this->data->render();
    }

    function qualification_range($para){
        $this->$para();
        $this->table_data(array("id","qf_code","qf_name","qf_company","qf_info","qf_expiration_date","IF(qf_D=-1,'--',qf_D)","IF(qf_t=-1,'--',qf_t)","IF(qf_h=-1,'--',qf_h)","IF(qf_α=-1,'--',qf_α)","IF(qf_Z=-1,'--',qf_Z)","qf_src"));
        //$this->data->orderby("created_at","desc");
        $this->data->add_button("查看","new_flavr",function($data){
            return $data["qf_src"];
        });
        /*
        $this->data->add_button("获取","get_val",function($data){
            return $data["qf_info"];
        });
        */

        //工艺筛选
        //$this->data->where("qf_expiration_date",">=",\Carbon\Carbon::now());
        if (isset($_GET["xyz"]) && strlen($_GET["xyz"]) > 0) {
            if ($_GET["xyz"] == "N") {
                $this->data->where("qf_Z",-1);
            } else {
                $this->data->where("qf_Z",4);
            }
        }

        if (isset($_GET["wmethod"]) && strlen($_GET["wmethod"]) > 0) {
            $this->data->where("qf_info","like","%".$_GET["wmethod"]."%");
        }
        if (isset($_GET["jtype"]) && strlen($_GET["jtype"]) > 0) {
            if ($_GET["jtype"] == "P") {
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% P %");
                    $query->orWhere(function($query){
                        $query->where("qf_info","like","% T %");
                        $query->where("qf_D",">",25);
                    });
                });
            }
            if ($_GET["jtype"] == "T") {
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% T %");
                    if (isset($_GET["wmethod"]) && strlen($_GET["wmethod"]) > 0) {
                        if (intval($_GET["wmethod"]) >= 500) {
                            $query->orWhere("qf_info","like","% P %");
                        } else if (intval($_GET["wmethod"]) >= 150) {
                            $query->orWhere(function($query){
                                $query->where("qf_info","like","% P %");
                                $query->where(function($query){
                                    $query->orWhere("qf_info","like","% PA %");
                                    $query->orWhere("qf_info","like","% PB %");
                                    $query->orWhere("qf_info","like","% PC %");
                                });
                            });
                        }
                    }
                });
            }
            if ($_GET["jtype"] == "P-T") {
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% P-T %");
                    $query->orWhere("qf_info","like","% T-T %");
                });
            }
            if ($_GET["jtype"] == "T-T") {
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% T-T %");
                });
            }
        }

        if (isset($_GET["gtype"]) && strlen($_GET["gtype"]) > 0) {
            if ($_GET["gtype"] == "GW") {
                $this->data->where("qf_info","like","% GW %");
            }
            if ($_GET["gtype"] == "FW") {
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% GW %");
                    $query->orWhere("qf_info","like","% FW %");
                });
            }
            if ($_GET["gtype"] == "D") {
                $this->data->where("qf_info","like","% D %");
            }
        }

        if (isset($_GET["diameter"]) && strlen($_GET["diameter"]) > 0) {
            if ($_GET["gtype"] == "GW") {
                if ($_GET["diameter"] < 25) {
                    $this->data->where("qf_D",">=",$_GET["diameter"]/2);
                    $this->data->where("qf_D","<=",$_GET["diameter"]);
                } else if ($_GET["diameter"] < 76) {
                    $this->data->where(function($query){
                        $query->orWhere("qf_D",">=",25);
                        $query->orWhere("qf_D",">=",$_GET["diameter"]/2);
                    });
                    $this->data->where("qf_D","<",76);
                }
            }
        }

        if (isset($_GET["thickness"]) && strlen($_GET["thickness"]) > 0) {
            if ($_GET["gtype"] == "GW") {
                $this->data->where("qf_t",">=",DB::raw("(".$_GET["thickness"]."-IF(qf_h=-1,0,qf_h))/2"));
                if ($_GET["thickness"] < 3) {
                    $this->data->where("qf_t","<=",$_GET["thickness"]);
                } else if ($_GET["thickness"] >= 3 && $_GET["thickness"] < 5) {
                    $this->data->where("qf_t","<",12);
                }
            }
            if ($_GET["gtype"] == "FW") {
                if ($_GET["thickness"] >= 3) {
                    $this->data->where("qf_t",">=",3);
                } else {
                    $this->data->where("qf_t","<",$_GET["thickness"]);
                }
            }
            if ($_GET["gtype"] == "D") {
                if ($_GET["thickness"] < 50) {
                    $this->data->where("qf_t","<",$_GET["thickness"]);
                }
            }
        }

        if (isset($_GET["position"]) && strlen($_GET["position"]) > 0) {
            if ($_GET["position"] == "PA" || $_GET["position"] == "PB") {
                $this->data->where(function($query){
                    $query->orWhere("qf_info","not like","% P %");
                    $query->orWhere("qf_info","not like","% PG %");
                });
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% P_ %");
                    $query->orWhere("qf_info","like","% _-L045 %");
                });
            }
            if ($_GET["position"] == "PC") {
                $this->where("qf_info","not like","% PA %");
                $this->where("qf_info","not like","% PB %");
                $this->where("qf_info","not like","% PF %");
                $this->where("qf_info","not like","% PG %");
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% P_ %");
                    $query->orWhere("qf_info","like","% _-L045 %");
                });
            }
            if ($_GET["position"] == "PD" || $_GET["position"] == "PE") {
                $this->where("qf_info","not like","% PA %");
                $this->where("qf_info","not like","% PB %");
                $this->where("qf_info","not like","% PC %");
                $this->data->where(function($query){
                    $query->orWhere("qf_info","not like","% P %");
                    $query->orWhere("qf_info","not like","% PF %");
                });
                $this->data->where(function($query){
                    $query->orWhere("qf_info","not like","% P %");
                    $query->orWhere("qf_info","not like","% PG %");
                });
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% P_ %");
                    $query->orWhere("qf_info","like","% _-L045 %");
                });
            }
            if ($_GET["position"] == "PF" && $_GET["jtype"] == "P") {
                $this->where("qf_info","not like","% PA %");
                $this->where("qf_info","not like","% PB %");
                $this->where("qf_info","not like","% PC %");
                $this->where("qf_info","not like","% PG %");
                $this->where("qf_info","not like","% J-L045 %");
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% P_ %");
                    $query->orWhere("qf_info","like","% _-L045 %");
                });
            }
            if ($_GET["position"] == "PF" && $_GET["jtype"] != "P") {
                $this->data->where(function($query){
                    $query->orWhere(function($query){
                        $query->where("qf_info","not like","% P %");
                        $query->where("qf_info","like","% PF %");
                    });
                    $query->orWhere("qf_info","like","% H-L045 %");
                });
            }
            if ($_GET["position"] == "PG" && $_GET["jtype"] == "P") {
                $this->data->where(function($query){
                    $query->orWhere("qf_info","like","% PG %");
                    $query->orWhere("qf_info","like","% J-L045 %");
                });
            }
            if ($_GET["position"] == "PG" && $_GET["jtype"] != "P") {
                $this->data->where(function($query){
                    $query->orWhere(function($query){
                        $query->where("qf_info","not like","% P %");
                        $query->where("qf_info","like","% PG %");
                    });
                    $query->orWhere("qf_info","like","% J-L045 %");
                });
            }
            if ($_GET["position"] == "H-L045") {
                $this->data->where("qf_info","like","% H-L045 %");
            }
            if ($_GET["position"] == "J-L045") {
                $this->data->where("qf_info","like","% J-L045 %");
            }
        }

        if ((isset($_GET["baseA"]) && $_GET["baseA"] > 0) || (isset($_GET["baseB"]) && $_GET["baseB"] > 0)) {
            $r_ones = array(1=> "Ⅰ", 2=>"Ⅱ", 3=>"Ⅲ", 4=>"Ⅳ", 5=>"Ⅴ", 6=>"Ⅵ", 7=>"Ⅶ", 8=>"Ⅷ", 9=>"Ⅸ"); 
            $max = max($_GET["baseA"],$_GET["baseB"]);
            if ($max < 5) {
                $this->data->where(function($query) use ($max,$r_ones){
                    for ($i=$max; $i < 5; $i++) { 
                        $query->orWhere("qf_info","like","% ".$r_ones[$i]." %");
                    }
                });
            } else if ($_GET["baseA"] == $_GET["baseB"]) {
                $this->data->where("qf_info","like","% ".$r_ones[$max]." %");
            } else if ($_GET["baseA"] > 0 && $_GET["baseB"] > 0) {
                $this->data->where(function($query) use ($max,$r_ones){
                    $query->orWhere("qf_info","like","% ".$r_ones[$_GET["baseA"]]."/".$r_ones[$_GET["baseB"]]." %");
                    $query->orWhere("qf_info","like","% ".$r_ones[$_GET["baseB"]]."/".$r_ones[$_GET["baseA"]]." %");
                });
            }
            //die(max($_GET["baseA"],$_GET["baseB"]));
        }

        if (isset($_GET["parameter"]) && strlen($_GET["parameter"]) > 0) {
            if ($_GET["parameter"] == "ss nb") {
                $this->data->where("qf_info","like","% ss nb%");
            }
            if ($_GET["parameter"] == "ml") {
                $this->data->where("qf_info","like","% ml%");
            }
        }

        return $this->data->render();
    }

    function qualification_no_valid($para){
        $this->$para();
        $this->table_data(array("id","qf_code","qf_name","qf_company","qf_info","qf_expiration_date"));
        $this->data->add_button("选择","wj_choose",function($data){return $data["id"];});
        return $this->data->render();
    }

}
