<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
                        $where_col = $_POST["col"];
                        $select = array($value_col);
                    } else {
                        $where_col = $show;
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
                    "msg" => "流程创建失败"
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
                    "msg" => "操作失败"
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

}
