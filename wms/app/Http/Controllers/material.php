<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use datatables;
use view;

class material extends Controller
{
   

    function in(){
        $model = new \App\secondary_store();
        $model->$_GET["warehouse"]();
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","secondary_store@in_show",$_GET["warehouse"]);
        $sview->title($model->titles_init(array("操作","类别"),array("录入人","时间")));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    function out(){
        $model = new \App\secondary_store();
        $model->$_GET["warehouse"]();
        $sview = new datatables("material/wm_out","secondary_store@out_show",$_GET["warehouse"]);
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        return $sview;
    }

    function store_list(){
        $model = new \App\secondary_store();
        $model->$_GET["warehouse"]();
        $sview = new datatables("layouts/panel_table","secondary_store@store_list",$_GET["warehouse"]);
        $sview->title($model->titles_init("序号",array("录入人","时间")));
        return $sview;
    }

    function store_record(){
        $model = new \App\secondary_store();
        $model->$_GET["warehouse"]();
        $sview = new datatables("layouts/panel_table","secondary_store@store_record",$_GET["warehouse"]);
        $sview->title($model->titles("序号",array("录入人","时间")));
        return $sview;
    }

    function sheet_add(){
        $sview = new view("material/sheet_add");
        return $sview;
    }

    function sheet_list(){
        $model = new \App\material_sheet();
        $sview = new datatables("layouts/panel_table",["width" => "1950px"],"material_sheet@ms_list",isset($_GET["warehouse"])?$_GET["warehouse"]:"");
        $sview->title($model->titles_init("序号",array("录入人","时间")));
        $sview->order(17,"desc");
        return $sview;
    }

    function sheet_list_spot(){
        $model = new \App\material_sheet();
        $sview = new datatables("layouts/panel_table",["width" => "1950px"],"material_sheet@ms_list_spot",isset($_GET["warehouse"])?$_GET["warehouse"]:"");
        $sview->title($model->titles_init("序号",array("录入人","时间")));
        $sview->order(17,"desc");
        return $sview;
    }

    function sent(){
        $sview = new view("material/sent",["url" => "/material/get_sent_sheet?sent=1", "post" => array("warehouse" => $_GET["warehouse"])]);
        return $sview;
    }

    function back(){
        $sview = new view("material/back",["url" => "/material/get_sent_sheet?back=1", "post" => array("warehouse" => $_GET["warehouse"])]);
        return $sview;
    }

    function sheet_detail(){

        $model = new \App\material_sheet();
        $material_sheet = $model->find($_GET["id"]);

        $model = new \App\wj();
        $wj = $model->select("vcode",DB::raw(SQL_BASE." as base"))->whereIn("id",multiple_to_array($material_sheet->ms_wj_ids))->get();

        $model = new \App\user();
        $user = $model->find($material_sheet->ms_by);

        $sheet_view = new view("sheet/m_sheet",["data" => $material_sheet, "wj" => $wj, "user" => $user]);

        $sview = new view("material/sheet_detail",["sheet" => $sheet_view->render()]);
        return $sview;
    }

    //创建焊材领用单
    function m_sheet(){
        if (isset($_POST["tsk_ids"]) && isset($_POST["tsk_material"]) && sizeof($_POST["tsk_material"]) > 0) {

            if ($_POST["tsk_spot"] == 1 && sizeof($_POST["tsk_material"]) > 1) {
                die("点口单只能选择一种焊材");
            }
            if ($_POST["tsk_spot"] != 1 && sizeof($_POST["tsk_ids"]) > 1) {
                $dm_limit = \App\wj::whereIn("tsk_id",$_POST["tsk_ids"])->where(function($qurey){
                    $qurey->orWhere("at",">=",25);
                    $qurey->orWhere("bt",">=",25);
                })->get();
                if (sizeof($dm_limit) > 0) {
                    die("非点口单(>=25mm)只能选择一个任务");
                }
            }

            //获得所有的焊口ID
            $wj_ids = array();
            $tsk_model = new \App\tsk();
            foreach ($_POST["tsk_ids"] as $tsk_id) {
                $tsk = $tsk_model->find($tsk_id);
                $wj_ids = array_merge($wj_ids,multiple_to_array($tsk->wj_ids));
            }

            //标题构建
            $wj_model = new \App\wj();
            $wj_datas = $wj_model->whereIn("id",$wj_ids)->orderBy("ild","asc")->orderBy("sys","asc")->orderBy("pipeline","asc")->orderBy("vnum","asc")->get();

            $title_base = "";
            $ild = "";
            $sys = "";
            $pipeline = "";
            foreach ($wj_datas as $wj_data) {
                if ($ild != $wj_data->ild) {
                    $ild = $wj_data->ild;
                    $title_base .= ",".$wj_data->ild.$wj_data->sys."-".$wj_data->pipeline."-".$wj_data->vnum;
                } else if ($sys != $wj_data->sys) {
                    $sys = $wj_data->sys;
                    $title_base .= ",".$wj_data->sys."-".$wj_data->pipeline."-".$wj_data->vnum;
                } else if ($pipeline != $wj_data->pipeline) {
                    $pipeline = $wj_data->pipeline;
                    $title_base .= ",".$wj_data->pipeline."-".$wj_data->vnum;
                } else {
                    $title_base .= ",".$wj_data->vnum;
                }
            }
            $title_base = substr($title_base,1);

            //焊工构建
            $pp_ids = $_POST["pp_ids"];
            $pp_model = new \App\pp();
            $pp_datas = $pp_model->whereIn("id",$pp_ids)->orderBy("id","asc")->get();
            $pp_show = "";
            foreach ($pp_datas as $pp_data) {
                $pp_show .= "/".$pp_data->pcode." ".$pp_data->pname;
            }
            $pp_show = substr($pp_show,1);

            //parameter
            $tsk_ids = $_POST["tsk_ids"];


            DB::transaction(function() use ($title_base,$tsk_ids,$pp_ids,$wj_ids,$pp_show){
                foreach ($_POST["tsk_material"] as $m) {
                    $model = new \App\material_sheet();
                    $model->ms_title = $title_base;
                    $model->ms_dep = $_POST["tsk_dept"];
                    $model->ms_spot = $_POST["tsk_spot"];
                    $model->ms_tsk_ids = array_to_multiple($tsk_ids);
                    $model->ms_pp_ids = array_to_multiple($pp_ids);
                    $model->ms_wj_ids = array_to_multiple($wj_ids);
                    $model->ms_pp_show = $pp_show;
                    //获取数组中的焊材信息
                    $model->ms_m_type = $m[1];
                    $model->ms_type = $m[0];
                    $model->ms_diameter = $m[2];
                    $model->ms_amount = (int)$m[3];
                    if (!is_integer($model->ms_amount)) {
                        die("输入数据不是整数");
                    } else if ($model->ms_amount <= 0) {
                        die("输入数据必须是正整数");
                    }
                    if (!$model->save()) {
                        die("写入失败");
                    }
                }
            });
            
            
            /*
            if (isset($_POST["tsk_wire"])) {
                $model = new \App\material_sheet();
                $model->ms_title = $title_base;
                $model->ms_dep = $_POST["tsk_dept"];
                $model->ms_spot = $_POST["tsk_spot"];
                $model->ms_tsk_ids = array_to_multiple($tsk_ids);
                $model->ms_pp_ids = array_to_multiple($pp_ids);
                $model->ms_wj_ids = array_to_multiple($wj_ids);
                $model->ms_m_type = "焊丝";
                $model->ms_type = $_POST["tsk_wire"];
                $model->ms_diameter = $_POST["tsk_wire_diameter"];
                $model->ms_amount = $_POST["tsk_wire_amount"];
                $model->ms_pp_show = $pp_show;
                $model->save();
            }
            if (isset($_POST["tsk_rod"])) {
                $model = new \App\material_sheet();
                $model->ms_title = $title_base;
                $model->ms_dep = $_POST["tsk_dept"];
                $model->ms_spot = $_POST["tsk_spot"];
                $model->ms_tsk_ids = array_to_multiple($tsk_ids);
                $model->ms_pp_ids = array_to_multiple($pp_ids);
                $model->ms_wj_ids = array_to_multiple($wj_ids);
                $model->ms_m_type = "焊条";
                $model->ms_type = $_POST["tsk_rod"];
                $model->ms_diameter = $_POST["tsk_rod_diameter"];
                $model->ms_amount = $_POST["tsk_rod_amount"];
                $model->ms_pp_show = $pp_show;
                $model->save();
            }
            */
            $r = array(
                "suc" => 1,
                "msg" => "操作成功"
            );
            echo json_encode($r);
        } else {
            echo "数据错误";
        }
    }

    //获取发放表格
    function get_sent_sheet(){
        if (floor($_POST["code_input"]/10000000000) == 4) {
            
            $id = intval($_POST["code_input"])%(40000000000+PJCODE*1000000);

            $model = new \App\material_sheet();
            $material_sheet = $model->where("id",$id)->get();

        } else if (floor($_POST["code_input"]/10000000000) == 1) {
            
            $tsk_id = intval($_POST["code_input"])%(10000000000+PJCODE*1000000);

            //通过任务（记录单）查询领料单，点口单暂时不能通过记录单领取
            $model = new \App\material_sheet();
            if (isset($_GET["back"])) {
                $material_sheet = $model->where("ms_tsk_ids","like","%{".$tsk_id."}%")->where("ms_s_id",">",0)->where("ms_store",$_POST["warehouse"])->whereNull("ms_back_time")->get();
            } else if (isset($_GET["sent"])) {
                $material_sheet = $model->where("ms_tsk_ids","like","%{".$tsk_id."}%")->where("ms_s_id",0)->get();
            } else {
                $material_sheet = $model->where("ms_tsk_ids","like","%{".$tsk_id."}%")->get();
            }
            

        } else {
            $r = array(
                "suc" => -9,
                "msg" => "输入值不合法"
            );
            die(json_encode($r));
        }
        
        
        if (sizeof($material_sheet) == 0) {
            $r = array(
                "suc" => -1,
                "msg" => "无此记录"
            );
            echo json_encode($r);
        } else {
            
                $html = "";

                foreach ($material_sheet as $item) {
                    $model = new \App\wj();
                    $wj = $model->select("vcode",DB::raw(SQL_BASE." as base"))->whereIn("id",multiple_to_array($item->ms_wj_ids))->get();

                    if (isset($_GET["sent"]) && $item->ms_s_id == 0) {
                        
                        $model = new \App\secondary_store();
                        $secondary_store = $model->leftjoin("setting","setting.setting_name","secondary_store.ss_trademark")->where("setting.setting_r0",$item->ms_type)->where("setting.setting_type","wmtrademark")->where("ss_warehouse",$_POST["warehouse"])->whereNull("ss_out_date")->get();

                        $sheet_view = new view("sheet/m_sheet",["data" => $item, "wj" => $wj, "store" => $secondary_store, "warehouse" => $_POST["warehouse"]]);
                    } else {

                        $sheet_view = new view("sheet/m_sheet",["data" => $item, "wj" => $wj, "warehouse" => $_POST["warehouse"]]);

                    }
                    

                    if ($html != "") {
                        $html .= "<br>";
                    }

                    $html .= $sheet_view->render();
                }


            
            
            $r = array(
                "suc" => 1,
                "msg" => $html
            );
            echo json_encode($r);
        }

        
    }


    function m_sent(){
        $model = new \App\material_sheet();
        $material_sheet = $model->find($_POST["id"]);
        $material_sheet->ms_s_id = $_POST["ms_s_id"];
        $material_sheet->ms_s_show = $_POST["ms_s_show"];
        $material_sheet->ms_store = $_POST["ms_store"];
        $material_sheet->ms_by = Auth::user()->id;
        $material_sheet->ms_time = \Carbon\Carbon::now();
        $material_sheet->authorize_user("m_".$_POST["ms_store"]);
        if($material_sheet->save()){

            $r = array(
                "suc" => 1,
                "msg" => $material_sheet->ms_s_show."/".Auth::user()->name." ".$material_sheet->ms_time
            );
            echo json_encode($r);
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "操作失败"
            );
            echo json_encode($r);
        }
    }


    function m_back(){
        $model = new \App\material_sheet();
        $material_sheet = $model->find($_POST["id"]);
        $material_sheet->ms_back_amount = $_POST["ms_back_amount"];
        $material_sheet->ms_back_by = Auth::user()->id;
        $material_sheet->ms_back_time = \Carbon\Carbon::now();
        $material_sheet->authorize_user("m_".$material_sheet->ms_store);
        if($material_sheet->save()){
            $r = array(
                "suc" => 1,
                "msg" => Auth::user()->name." ".$material_sheet->ms_back_time,
                "back_amount" => $material_sheet->ms_back_amount
            );
            echo json_encode($r);
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "操作失败"
            );
            echo json_encode($r);
        }
    }

    //(POST)领料单表格
    function material_sheet_add(){
        if (isset($_POST["code_input"])) {
            if (floor($_POST["code_input"]/1000000) == 10000+PJCODE) {
                $tsk_id = $_POST["code_input"]%1000000;
                $tsk = new \App\tsk();
                $data = $tsk->find($tsk_id);
                if ($data == null) {
                    $r = array(
                        "suc" => -1,
                        "msg" => "未查到该任务"
                    );
                    die(json_encode($r));
                } else if (strlen($data->tsk_pp) > 0) {
                    $r = array(
                        "suc" => -1,
                        "msg" => "该焊缝已经完工"
                    );
                    die(json_encode($r));
                }
                $wps = new \App\wps();
                $wps_data = $wps->withoutGlobalScopes()->find($data->wps_id);
                if ($wps_data == null) {
                    $r = array(
                        "suc" => -1,
                        "msg" => "获取工艺信息失败"
                    );
                    die(json_encode($r));
                }
                $store = new \App\secondary_store();
                //获取焊条信息
                $m_info = array();
                $rod_store = $store->leftjoin("setting","setting.setting_name","secondary_store.ss_trademark")->where("setting.setting_r0",$wps_data->wps_rod)->where("setting.setting_type","wmtrademark")->whereNull("ss_out_date")->get()->toArray();
                foreach ($rod_store as $rod_s) {
                    if (isset($m_info[$wps_data["wps_rod"]."_".$rod_s["ss_diameter"]])) {
                        $m_info[$wps_data["wps_rod"]."_".$rod_s["ss_diameter"]]["store"] .= "<br>[".$rod_s["ss_warehouse"]."] &nbsp; ".$rod_s["ss_trademark"]." φ".$rod_s["ss_diameter"]." &nbsp; 批号：".$rod_s["ss_batch"]." &nbsp; ".$rod_s["ss_weight"]."kg";
                    } else {
                        $m_info[$wps_data["wps_rod"]."_".$rod_s["ss_diameter"]] = array(
                            "type" => "焊条",
                            "title" => $wps_data["wps_rod"]." φ".$rod_s["ss_diameter"],
                            "name" => $wps_data["wps_rod"],
                            "diameter" => $rod_s["ss_diameter"],
                            "store" => "[".$rod_s["ss_warehouse"]."] &nbsp; ".$rod_s["ss_trademark"]." φ".$rod_s["ss_diameter"]." &nbsp; 批号：".$rod_s["ss_batch"]." &nbsp; ".$rod_s["ss_weight"]."kg"
                        );
                    }
                    
                }
                //获取焊丝信息
                $wire_store = $store->leftjoin("setting","setting.setting_name","secondary_store.ss_trademark")->where("setting.setting_r0",$wps_data->wps_wire)->where("setting.setting_type","wmtrademark")->whereNull("ss_out_date")->get()->toArray();
                foreach ($wire_store as $wire_s) {
                    if (isset($m_info[$wps_data["wps_wire"]."_".$wire_s["ss_diameter"]])) {
                        $m_info[$wps_data["wps_wire"]."_".$wire_s["ss_diameter"]]["store"] .= "<br>[".$wire_s["ss_warehouse"]."] &nbsp; ".$wire_s["ss_trademark"]." φ".$wire_s["ss_diameter"]." &nbsp; 批号：".$wire_s["ss_batch"]." &nbsp; ".$wire_s["ss_weight"]."kg";
                    } else {
                        $m_info[$wps_data["wps_wire"]."_".$wire_s["ss_diameter"]] = array(
                            "type" => "焊丝",
                            "title" => $wps_data["wps_wire"]." φ".$wire_s["ss_diameter"],
                            "name" => $wps_data["wps_wire"],
                            "diameter" => $wire_s["ss_diameter"],
                            "store" => "[".$wire_s["ss_warehouse"]."] &nbsp; ".$wire_s["ss_trademark"]." φ".$wire_s["ss_diameter"]." &nbsp; 批号：".$wire_s["ss_batch"]." &nbsp; ".$wire_s["ss_weight"]."kg"
                        );
                    }
                    
                }

                $r = array(
                    "suc" => 1,
                    "tsk_id" => $data["id"],
                    "tsk_info" => $data,
                    "tsk_wps" => $wps_data,
                    "tsk_title" => $data["tsk_title"],
                    "tsk_wire" => $wps_data["wps_wire"],
                    "tsk_rod" => $wps_data["wps_rod"],
                    "m_info" => $m_info,
                    "msg" => "操作成功"
                );
                
                die(json_encode($r));
            } else if (floor($_POST["code_input"]/1000000) == 20000+PJCODE){
                $pp_id = $_POST["code_input"]%1000000;
                $pp = new \App\pp();
                $data = $pp->find($pp_id);
                if ($data == null) {
                    $r = array(
                        "suc" => -1,
                        "msg" => "未查到该焊工"
                    );
                    die(json_encode($r));
                } else {
                    $r = array(
                        "suc" => 1,
                        "pp_id" => $data["id"],
                        "pcode" => $data["pcode"],
                        "pname" => $data["pname"],
                        "msg" => "操作成功"
                    );
                }
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => "输入的不是任务和焊工"
                );
                die(json_encode($r));
            }
        }
    }


}
