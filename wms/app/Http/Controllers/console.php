<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use \Carbon\Carbon;
use depend_map;
use view;

class console extends Controller
{

    function show($type){
        if (in_array($type,array("datatables"))) {
            return $this->$type();
        } else {
            return parent::show($type);
        }
    }

    function datatables(){
        $class_name = "\App\\".$_GET["model"];
        $model = new $class_name();
        if (strlen($_GET["para"]) > 0) {
            return $model->$_GET["method"]($_GET["para"]);
        } else {
            return $model->$_GET["method"]();
        }
    	
    }

    function flexible_ajax(){
        DB::transaction(function()
        {
            try {
                $model_array = array();
                foreach ($_POST as $key => $value) {
                    if (!in_array($key,array("_token","_method","model","update","for_id"))) {
                        if ($value["value"] == "null") {
                            $value["value"] = null;
                        }
                        $class_name = "\\App\\".$value["model"];
                        if (!isset($model_array[$class_name])) {
                            $model_array[$class_name] = new $class_name();
                        }
                        //空值处理
                        if (strlen($value["value"]) == 0) {
                            if (in_array($value["col"],$model_array[$class_name]->default_col)) {
                                $value["value"] = 0;//默认列的处理都暂为0
                            } else if ($model_array[$class_name]->item->$value["col"]->def !== false && in_array($model_array[$class_name]->item->$value["col"]->type,array("integer","date","datetime"))) {
                                $value["value"] = $model_array[$class_name]->item->$value["col"]->def;
                            }
                        }
                        if (substr($value["id"],0,1) == "{" && substr($value["id"],-1) == "}") {
                            $ids = multiple_to_array($value["id"]);
                            foreach ($ids as $id) {
                                //echo $value["value"];
                                $c = $model_array[$class_name]->find($id);
                                $c->$value["col"] = $value["value"];
                                $c->save();
                            }
                        } else {
                            $c = $model_array[$class_name]->find($value["id"]);
                            $c->$value["col"] = $value["value"];
                            $c->save();
                        }
                        
                    }
                }
            } catch(\Exception $e) {
                $r = array(
                        "suc" => -1,
                        "msg" => "操作失败"
                    );
                die(json_encode($r));
            }
        });
        $r = array(
                "suc" => 1,
                "msg" => "操作成功"
            );
        echo json_encode($r);
    }

    function model_ajax(){
    	if (isset($_POST["model"])) {
			$class_name = "App\\".$_POST["model"];
    		$model_ajax = new $class_name();
    		if (isset($_POST["delete"]) && isset($_POST["id"])) {
        		if ($count = $model_ajax->destroy($_POST["id"])) {
                    $r = array(
                            "suc" => 1,
                            "msg" => $model_ajax->msg===false?"删除成功(".$count.")":$model_ajax->msg
                        );
                    echo json_encode($r);
                } else {
					$r = array(
	    					"suc" => -1,
	    					"msg" => $model_ajax->msg===false?"删除失败":$model_ajax->msg
	    				);
	        		echo json_encode($r);
        		}
    		} else if (isset($_POST["insert"])) {
                foreach ($_POST as $key => $value) {
                    if (!in_array($key,array("_token","_method","model","insert"))) {
                        if ($value == "null") {
                            $value = null;
                        }
                        $model_ajax->$key = $value;
                    }
                }
    			if ($model_ajax->save()) {
    				$r = array(
        					"suc" => 1,
        					"msg" => $model_ajax->msg===false?"添加成功":$model_ajax->msg
        				);
        			echo json_encode($r);
    			} else {
    				$r = array(
        					"suc" => -1,
        					"msg" => $model_ajax->msg===false?"添加失败":$model_ajax->msg
        				);
        			echo json_encode($r);
    			}
    		} else if (isset($_POST["update"])){
                $collection = $model_ajax->onlySoftDeletes()->find($_POST["for_id"]);
                foreach ($_POST as $key => $value) {
                    if (!in_array($key,array("_token","_method","model","update","for_id"))) {
                        if ($value == "null") {
                            $value = null;
                        }
                        $collection->$key = $value;
                    }
                }
                if ($collection->save()) {
                    $r = array(
                            "suc" => 1,
                            "msg" => $model_ajax->msg===false?"修改成功":$model_ajax->msg
                        );
                    echo json_encode($r);
                } else {
                    $r = array(
                            "suc" => -1,
                            "msg" => $model_ajax->msg===false?"修改失败":$model_ajax->msg
                        );
                    echo json_encode($r);
                }
    		}
    	}
    }

    function get_bind(){
        if ($_POST["model"] && $_POST["col"]) {

            //old**********************
            //$s_array = array($_POST["model"],$_POST["col"]);
            //*************************

            if (isset($_POST["refer"])) {
                $refer = intval($_POST["refer"]);
            } else {
                $refer = 0;
            }
            if (isset($_POST["show"])) {
                $show = $_POST["show"];
            } else {
                $show = "";
            }
            //设置要搜索的值
            if (isset($_POST["search"])) {
                $search = $_POST["search"];
            } else {
                $search = "";
            }
            //设置要匹配的值
            if (isset($_POST["value"])) {
                $value = $_POST["value"];
            } else {
                $value = "";
            }

            if (isset($_POST["limit"])) {
                $limit = $_POST["limit"];
            } else {
                $limit = 10;
            }
            if (isset($_POST["group"])) {
                $group = $_POST["group"];
            } else {
                $group = 1;
            }
            if (isset($_POST["current"])) {
                $current = $_POST["current"];
            } else {
                $current = false;
            }


            $class_name = "\App\\".$_POST["model"];
            $main_model = new $class_name();
            $main_model->parent_scope = false;//暂时关闭全局SCOPE
            if ($_POST["type"] && strlen($_POST["type"]) > 0) {
                if (method_exists($main_model, $_POST["type"])) {
                    $main_model->$_POST["type"]();
                } else {
                    $main_model->parent($_POST["type"]);
                }
            }
            if (strlen($show) == 0) {
                $bind = $main_model->item->$_POST["col"]->bind;
                $history = $main_model->item->$_POST["col"]->history;
            } else {
                $bind = array();
                $history = "";
            }
            


            //print_r($bind);
            if (sizeof($bind) >= 3 || $history !== false) {

                if (strlen($show) > 0) {//当显示与数值不一致时执行
                    $model = $main_model;
                    $value_col = $_POST["col"];
                    if ($show == "1") {
                        $where_col = DB::raw($value_col);
                        $select = array($value_col);
                    } else {
                        $where_col = DB::raw($show);
                        $select = array(DB::raw($show),DB::raw($value_col));
                    }
                } else if (sizeof($bind) >= 3) {//当读取bind时
                    $value_col = $bind[1];
                    if ($bind[1] == $bind[2]) {
                        $select = array($bind[1]);
                        $where_col = $bind[1];
                    } else {
                        $select = array(DB::raw($bind[2]),DB::raw($bind[1]));
                        $where_col = $bind[2];
                    }
                    $class_name = "\App\\".$bind[0];
                    $model = new $class_name();
                } else {//history时
                    $select = array(DB::raw($_POST["col"]));
                    $model = $main_model;
                    $main_model->parent_scope = true;//重新启用全局SCOPE
                    if ($_POST["type"] && strlen($_POST["type"]) > 0) {
                        $main_model->parent($_POST["type"]);
                    }
                    $value_col = $_POST["col"];
                    $where_col = $_POST["col"];
                }

                $where_col = DB::raw($where_col);

                //echo $main_model->item->get_only();
                $collection = $model->select($select)->where($where_col,"<>","")->WhereNotNull($where_col);
                if (sizeof($bind) > 3) {
                    //$collection->withoutGlobalScopes();
                    //$collection->bootSoftDeletes();
                    $collection->where($bind[3]);
                }
                if (is_object($history)) {
                    $collection->where($history);
                }
                if ($value != ""){
                    $value = str_replace("/",",",str_replace("{","",str_replace("}","",str_replace("}{",",",$value))));
                    $collection->whereIn($value_col,string_to_array($value));
                }
                if ($search != "") {
                    $words = preg_split("/[-.,;!\s']\s*/", $search);
                    foreach ($words as $word => $value) {
                        $collection->where($where_col,"LIKE","%".$value."%");
                    }   
                }
                if ($group == 1) {
                    foreach ($select as $value) {
                        $collection->groupby($value);
                    }
                }
                //if (get_parent_class($model) == "App\\table_model" && $model->table_version && $current !== false) {
                    //$collection->where($model->get_table().".current_version",$current);
                //}
                $data = $collection->get($limit);
                if ($data->isEmpty()) {
                    $r = array(
                        "suc" => -1,
                        "msg" => "数据为空",
                        "data" => $data->toArray()
                    );
                } else {
                    $r = array(
                        "suc" => 1,
                        "msg" => "获取数据成功",
                        "data" => $data->toArray()
                    );
                //print_r($r);
                }
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => "获取数据失败",
                    "data" => ""
                );
            }
            echo json_encode($r);
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "获取数据失败",
                "data" => array("获取数据失败")
            );
            echo json_encode($r);
        }
    }

    function version_update(){
        if (isset($_POST["model"]) && isset($_POST["id"])) {
            $class_name = "\\App\\".$_POST["model"];
            $version = new $class_name();
            if ($version->version_update($_POST["id"])) {
                $r = array(
                    "suc" => 1,
                    "msg" => "升版成功"
                );
                echo json_encode($r);
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => "升版失败(".$version->msg.")"
                );
                echo json_encode($r);
            }
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "获取数据失败"
            );
            echo json_encode($r);
        }
    }

    function is_bind(){
         if ($_POST["model"] && $_POST["col"]) {
            $s_array = array($_POST["model"],$_POST["col"]);
            if ($_POST["type"] && strlen($_POST["type"]) > 0) {
                array_push($s_array,$_POST["type"]);
            }
            if ($_POST["value"]) {
                $value = $_POST["value"];
            } else {
                $value = "";
            }
            if (depend_map::is_bind_value($s_array,$value)) {
                $r = array(
                    "suc" => 1,
                    "msg" => "获取数据成功",
                    "data" => "获取数据成功"
                );
                echo json_encode($r);
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => "获取数据失败",
                    "data" => "获取数据失败"
                );
                echo json_encode($r);
            }
            
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "获取数据失败",
                "data" => array("获取数据失败")
            );
            echo json_encode($r);
        }
    }

    function get_model_data(){
        if ($_POST["model"] && $_POST["col"]) {
            $class_name = "App\\".$_POST["model"];
            $model_data = new $class_name();
            $col = explode("#/#",$_POST["col"]);
            $model_data->select($col);
            $r = array(
                "suc" => 1,
                "msg" => "添加失败",
                "data" => array(
                        array(7,2,8)

                    )
            );
            echo json_encode($r);
        }

    }

    function dt_edit(){
        $class_name = "\\App\\".$_GET["model"];
        $model = new $class_name();
        if (isset($_GET["para"]) && $_GET["para"] != "") {
            $model->$_GET["para"]();
        }
        $collection = $model->onlySoftDeletes()->find($_GET["id"]);
        $input_view = new view("form/ajax_form",["model" => $model,"collection" => $collection]);
        $sview = new view("layouts/page_detail");
        //$sview->title(array("操作","名称","备注","条件","录入人","时间"));
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function dt_version_update(){
        $input_view = new view("form/proc_form");
        $sview = new view("layouts/page_detail");
        //$sview->title(array("操作","名称","备注","条件","录入人","时间"));
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function status_avail_procedure(){
        $proc = new \App\procedure\status_avail_procedure($_GET["proc_id"]);
        $input_view = new view("form/proc_form",["proc" => $proc]);
        $sview = new view("layouts/page_detail");
        $sview->info("panel-body",$input_view->render());
        return $sview;
    }

    function procedure_create(){
        if (isset($_POST["model"]) && isset($_POST["id"])) {
            try{
                $proc = new \App\procedure\status_avail_procedure("",$_POST["model"],$_POST["id"]);
                $proc->create_proc();
            } catch(\Exception $e){
                $r = array(
                    "suc" => -1,
                    "msg" => "流程创建失败",
                    "error" => $e
                );
                die(json_encode($r));
            }
            $r = array(
                "suc" => 1,
                "msg" => "流程创建成功",
                "proc_id" => $proc->proc_id
            );
            die(json_encode($r));
        }
    }

    function procedure_pass(){
        if (isset($_POST["proc_id"])) {
            $proc_id = $_POST["proc_id"];
        } else {
            $proc_id = 0;
        }
        if (isset($_POST["owner"])) {
            $owner = $_POST["owner"];
        } else {
            $owner = false;
        }
        if (isset($_POST["pdi_comment"])) {
            $comment = $_POST["pdi_comment"];
        } else {
            $comment = "";
        }
        if ($proc_id == 0) {
            $r = array(
                    "suc" => -1,
                    "msg" => "载入流程失败"
                );
            die(json_encode($r));
        } else if($owner != "finish" && ($owner == false || (sizeof($owner) > 0 && $owner[0] == 0))){
            $r = array(
                    "suc" => -1,
                    "msg" => "未设置责任人"
                );
            die(json_encode($r));
        } else {
            $proc = new \App\procedure\status_avail_procedure($proc_id);
            try{
                $proc->pass_proc($comment,$owner);
            } catch (\Exception $e){
                $r = array(
                    "suc" => -1,
                    "msg" => "操作失败",
                    "error" => $e
                );
                die(json_encode($r));
            }
            $r = array(
                "suc" => 1,
                "msg" => "操作成功"
            );
            die(json_encode($r));
        }


    }

    function procedure_rollback(){
        if (isset($_POST["proc_id"])) {
            if (isset($_POST["pdi_comment"])) {
                $comment = $_POST["pdi_comment"];
            } else {
                $comment = "";
            }
            try{
                $proc = new \App\procedure\status_avail_procedure($_POST["proc_id"]);
                $proc->rollback_proc($comment);
            } catch(\Exception $e){
                $r = array(
                    "suc" => -1,
                    "msg" => "退回失败"
                );
                die(json_encode($r));
            }
            $r = array(
                "suc" => 1,
                "msg" => "退回成功",
                "proc_id" => $proc->proc_id
            );
            die(json_encode($r));
        }
    }

    function tsk_add(){
        $data = $_POST;
        unset($data["_token"]);
        unset($data["_method"]);
        DB::transaction(function() use ($data){
            $wj_model = new \App\wj();
            $wps_model = new \App\wps();
            $qp_model = new \App\qp();
            $suc_tsk_ids = array();
            $html = "";
            try{
                foreach ($data as $key => $value) {
                    $wj = $wj_model->select("ild","sys","vcode","tsk_id",DB::raw(SQL_BASE_TYPE." as wj_type"))->whereIn("id",multiple_to_array($value[0]))->get();
                    if ($wj[0]->tsk_id != null) {
                        throw new \Exception("已经添加任务");
                    }
                    $wps = $wps_model->find($value[3]);
                    $qp = $qp_model->find($value[1]);
                    //后续需加上对不同类别的验证
                    $task = new \App\tsk();
                    if (strpos($value[0], "{") == 0) {
                        $task->wj_ids = $value[0];
                    } else {
                        $task->wj_ids = "{".$value[0]."}";
                    }
                    $task->tsk_title = $wj[0]->vcode;
                    if (sizeof($wj) > 1) {
                        $task->tsk_title .= "等（".sizeof($wj)."道）";
                    }
                    $task->qp_id = $value[1];
                    $task->tsk_ft = $value[2];
                    $task->wps_id = $value[3];
                    $task->tsk_date = Carbon::today();
                    $task->tsk_identity = $wj[0]->ild.$wj[0]->sys;//先使用第一个值，后续需添加验证
                    $task->tsk_identity_record = $task->where("tsk_identity",$task->tsk_identity)->count()+1;
                    $task->tsk_print_history = Auth::user()->id.":".Carbon::now();
                    $task->tsk_wmethod = $wps->wps_method;
                    $task->tsk_wj_spec = $wj[0]->wj_type;
                    $task->tsk_qp = $qp->qp_code.$qp->qp_name;
                    if (!$task->save()) {
                        throw new \Exception($task->msg);
                    }
                    DB::table("wj")->whereIn("id",multiple_to_array($value[0]))->update(["tsk_id" => $task->id,"qid" => $value[1]]);
                    $suc_tsk_ids[] = $task->id;
                }
                $r = array(
                    "suc" => 1,
                    "msg" => "任务添加成功",
                    "print" => $html
                );
                echo(json_encode($r));
            } catch(\Exception $e){
                $r = array(
                    "suc" => -1,
                    "msg" => "操作失败"
                );
                die(json_encode($r));
            }
            
        });
    }

    function tsk_finish_form(){
        if (isset($_POST["id"])) {
            $tsk = new \App\tsk();
            $data = $tsk->find($_POST["id"]);
            $wj = new \App\wj();
            $wjs = $wj->select(array("id",DB::raw(SQL_VCODE." as wj_code"),DB::raw(SQL_EXAM_RATE." as rate"),DB::raw(SQL_BASE_TYPE." as type")))->where("tsk_id",$_POST["id"])->get();
            $wps = new \App\wps();
            $wps_data = $wps->find($data->wps_id);


            $form = new \view("form.tsk_finish_form",["tsk" => $tsk,"data" => $data,"wjs" => $wjs,"wps" => $wps_data]);

            $r = array(
                "suc" => 1,
                "msg" => "成功",
                "form" => $form->render()
            );
            die(json_encode($r));
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "失败"
            );
            die(json_encode($r));
        }
        
    }

    function tsk_finished(){
        if (isset($_POST["id"]) && isset($_POST["tsk_pp"]) && isset($_POST["tsk_finish_date"])) {

            
            $pp_array = array();
            for ($i=0; $i < sizeof($_POST["tsk_pp"]); $i++) { 
                $pp_array[$_POST["tsk_pp"][$i]] = $_POST["tsk_pp_proportion"][$i];
            }
            ksort($pp_array);
            $pp_ids = array_keys($pp_array);
            $pp_proportions = array_values($pp_array);


            $tsk = new \App\tsk();
            $data = $tsk->find($_POST["id"]);

            $pp = new \App\pp();
            $pps = $pp->whereIn("id",$pp_ids)->get();
            $tsk_pp_show = "";
            foreach ($pps as $p) {
                $tsk_pp_show .= "/".$p->pcode.$p->pname;
            }
            $data->tsk_pp_show = substr($tsk_pp_show,1);



            
            $data->tsk_pp = array_to_multiple($pp_ids);
            $data->tsk_pp_proportion = array_to_multiple($pp_proportions);
            $data->tsk_finish_date = $_POST["tsk_finish_date"];
            $data->tsk_input_time = Carbon::now();
            if ($data->save()) {
                $r = array(
                    "suc" => 1,
                    "msg" => "成功",
                    "tsk_pp_show" => $data->tsk_pp_show,
                    "tsk_pp_proportion" => $data->tsk_pp_proportion,
                    "tsk_finish_date" => $data->tsk_finish_date
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => 0,
                    "msg" => "写入失败"
                );
                die(json_encode($r));
            }
            
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "指令错误"
            );
            die(json_encode($r));
        }
        
    }

    function model_cal(){
        $class_name = "\\App\\".$_POST["model"];
        $model = new $class_name();
        $cal = $model->item->get_cal();
        $result = array();
        foreach ($cal as $c) {
            $result_cal = false;
            if ($_POST["cal_para"] == $c[0]) {
                if (isset($result[$c[0]])) {
                    $para = $result[$c[0]];
                } else {
                    $para = $_POST[$c[0]];
                }
                $result_cal = call_user_func($c[3],$para);
            } else if (is_array($c[0]) && in_array($_POST["cal_para"],$c[0])){
                $para = array();
                foreach ($c[0] as $p) {
                    if (isset($result[$p])) {
                        $para[] = $result[$p];
                    } else {
                        $para[] = $_POST[$p];
                    }
                }
                $result_cal = call_user_func_array($c[3],$para);
            }
            //print_r($para);
            if ($result_cal !== false) {
                if (is_array($c[1])) {
                    $k = 0;
                    foreach ($c[1] as $r) {
                        $result[$r] = $result_cal[$k];
                        $k++;
                    }
                } else {
                    $result[$c[1]] = $result_cal;
                }
            }
        }
        if (sizeof($result) > 0) {
            $r = array(
                "suc" => 1,
                "msg" => "计算成功",
                "result" => $result
            );
            die(json_encode($r));
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "计算错误"
            );
            die(json_encode($r));
        }
    }

    function model_valid(){
        $class_name = "\\App\\".$_POST["model"];
        $model = new $class_name();
        $fn = $model->item->$_POST["valid_col"]->restrict;
        if (is_callable($fn)) {
            if (isset($_POST["id"])) {
                $id = $_POST["id"];
            } else {
                $id = 0;
            }
            $valid = $fn($_POST["valid_value"],$id);
            if ($valid === true) {
                $r = array(
                    "suc" => 1,
                    "msg" => "验证成功"
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => $valid,
                    "origin" => ""
                );
                die(json_encode($r));
            }
        } else {
            $r = array(
                "suc" => 1,
                "msg" => "无需验证"
            );
            die(json_encode($r));
        }
    }

    function consignation_add(){
        if (isset($_POST["wj_ids"])) {
            DB::transaction(function(){
                
                $exam_plan = new \App\exam_plan();

                $exam_plan->ep_code = $_POST["code"];
                $exam_plan->ep_method = $_POST["emethod"];
                $exam_plan->ep_wj_type = $_POST["wj_type"];
                $exam_plan->ep_ild_sys = $_POST["ild_sys"];
                $exam_plan->ep_pp = $_POST["pp_show"];
                $exam_plan->ep_wps = $_POST["wps"];

                $exam_plan->ep_wj_ids = array_to_multiple($_POST["wj_ids"]);
                
                $rate = floatval($_POST["rate"])/100;
                $count = sizeof($_POST["wj_ids"]);
                $samples_count = ceil($count*$rate);
                
                $exam_plan->ep_wj_count = $count;
                $exam_plan->ep_wj_samples_count = $samples_count;
                $exam_plan->ep_wj_all_samples_count = $samples_count;//首次抽样时，总抽样数与抽样数一致
                $exam_plan->ep_weight = ($samples_count/$count)*100;


                $samples_keys = array_rand($_POST["wj_ids"],$samples_count);
                $samples = "";
                $samples_array = array();
                if (is_array($samples_keys)) {
                    foreach ($samples_keys as $key) {
                        $samples_array[] = $_POST["wj_ids"][$key];
                        $samples .= "{".$_POST["wj_ids"][$key]."}";
                    }
                } else {
                    $samples_array[] = $_POST["wj_ids"][$samples_keys];
                    $samples .= "{".$_POST["wj_ids"][$samples_keys]."}";
                }

                $exam_plan->ep_wj_samples = $samples;

                if (!$exam_plan->save()) {
                    //throw new \Exception($exam_plan->msg);
                    $r = array(
                        "suc" => -1,
                        "msg" => $exam_plan->msg
                    );
                    die(json_encode($r));
                }

                foreach ($_POST["wj_ids"] as $id) {
                    $exam = new \App\exam();
                    $exam->exam_wj_id = $id;
                    $exam->exam_method = $_POST["emethod"];
                    $exam->exam_plan_id = $exam_plan->id;
                    if (!$exam->save()) {
                        //throw new \Exception($exam->msg);
                        $r = array(
                            "suc" => -1,
                            "msg" => $exam->msg
                        );
                        die(json_encode($r));
                    }
                }

                $wj = new \App\wj();
                $wj_items = $wj->whereIn("id",$_POST["wj_ids"])->update([$_POST["emethod"]."_plan" => DB::raw("CONCAT(".$_POST["emethod"]."_plan,'{".$exam_plan->id."}')"), $_POST["emethod"]."_weight" => DB::raw("GREATEST(".$_POST["emethod"]."_weight, ".$exam_plan->ep_weight.")")]);
                if ($count != $samples_count) {
                    $wj_items = $wj->whereIn("id",$samples_array)->update([$_POST["emethod"]."_weight" => 100]);
                }
                
            });
            $r = array(
                "suc" => 1,
                "msg" => "操作成功"
            );
            die(json_encode($r));
        }
    }

    function ss_out(){
        if (isset($_POST["id"]) && isset($_POST["ss_out_date"]) && isset($_POST["ss_out_weight"]) && isset($_POST["ss_out_reason"])) {

            $ss = new \App\secondary_store();
            $data = $ss->find($_POST["id"]);
            
            $data->ss_out_date = $_POST["ss_out_date"];
            $data->ss_out_weight = $_POST["ss_out_weight"];
            $data->ss_out_reason = $_POST["ss_out_reason"];
            if ($data->save()) {
                $r = array(
                    "suc" => 1,
                    "msg" => "成功",
                    "ss_out_date" => $data->ss_out_date,
                    "ss_out_weight" => $data->ss_out_weight,
                    "ss_out_reason" => $data->ss_out_reason
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => 0,
                    "msg" => "写入失败"
                );
                die(json_encode($r));
            }
            
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "指令错误"
            );
            die(json_encode($r));
        }
    }

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
                }
                $wps = new \App\wps();
                $wps_data = $wps->find($data->wps_id);
                if ($wps_data == null) {
                    $r = array(
                        "suc" => -1,
                        "msg" => "获取工艺信息失败"
                    );
                    die(json_encode($r));
                }
                $store = new \App\secondary_store();
                $rod_store = $store->where("ss_type",$wps_data->wps_rod)->whereNull("ss_out_date")->get()->toArray();
                $wire_store = $store->where("ss_type",$wps_data->wps_wire)->whereNull("ss_out_date")->get()->toArray();
                $r = array(
                    "suc" => 1,
                    "tsk_id" => $data["id"],
                    "tsk_title" => $data["tsk_title"],
                    "tsk_wire" => $wps_data["wps_wire"],
                    "tsk_rod" => $wps_data["wps_rod"],
                    "rod_store" => $rod_store,
                    "wire_store" => $wire_store,
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
