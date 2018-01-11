<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use \Carbon\Carbon;
use datatables;
use view;

class console extends Controller
{
    //console特殊设置，GET方式，如果在数组中的，只执行整个方法，否则按照controller的方式显示页面
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
            return $model->{$_GET["method"]}($_GET["para"]);
        } else {
            return $model->{$_GET["method"]}();
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
                            } else if ($model_array[$class_name]->item->{$value["col"]}->def !== false && in_array($model_array[$class_name]->item->{$value["col"]}->type,array("integer","date","datetime"))) {
                                $value["value"] = $model_array[$class_name]->item->{$value["col"]}->def;
                            }
                        }
                        if (substr($value["id"],0,1) == "{" && substr($value["id"],-1) == "}") {
                            $ids = multiple_to_array($value["id"]);
                            foreach ($ids as $id) {
                                //echo $value["value"];
                                $c = $model_array[$class_name]->find($id);
                                $c->{$value["col"]} = $value["value"];
                                $c->save();
                            }
                        } else {
                            $c = $model_array[$class_name]->find($value["id"]);
                            $c->{$value["col"]} = $value["value"];
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
                try{
                    $count = $model_ajax->destroy($_POST["id"]);
                } catch (\Exception $e) {
                    $r = array(
                            "suc" => -1,
                            "msg" => $e->getMessage()
                        );
                    die(json_encode($r));
                }
        		if ($count) {
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
                    if (!in_array($key,array("_auth","_alt","_token","_method","model","insert"))) {
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

                //获取model
                $collection = $model_ajax->onlySoftDeletes()->find($_POST["for_id"]);

                //更新model键值
                foreach ($_POST as $key => $value) {
                    if (!in_array($key,array("_auth","_alt","_token","_method","model","update","for_id"))) {
                        if ($value == "null") {
                            $value = null;
                        }
                        $collection->$key = $value;
                    }
                }

                //存在_auth则执行该方法
                if (isset($_POST["_auth"]) && method_exists($collection,$_POST["_auth"])) {
                    $collection->{$_POST["_auth"]}();
                }

                //判断进行变更还是修改,没有_alt则进行更新
                if (!isset($_POST["_alt"])) {
                    if ($collection->save()) {
                        $r = array(
                                "suc" => 1,
                                "msg" => $collection->msg===false?"修改成功":$collection->msg
                            );
                        echo json_encode($r);
                    } else {
                        $r = array(
                                "suc" => -1,
                                "msg" => $collection->msg===false?"修改失败":$collection->msg
                            );
                        echo json_encode($r);
                    }

                //判断进行变更还是修改,有_alt则进行变更
                } else {
                    $collection->authorize_user(Auth::user()->id);//先进行授权
                    if ($collection->valid_updating()) {
                        $r = array(
                                "suc" => -1,
                                "msg" => "该数据仍可以修改，无需变更"
                            );
                        echo json_encode($r);
                    } else {
                        //获取dirty值
                        $dirty = $collection->getDirty();
                        //如没有修改则提示
                        if(sizeof($dirty) == 0){
                            $r = array(
                                "suc" => -2,
                                "msg" => "没有任何修改"
                            );
                            die(json_encode($r));
                        }

                        //判断更改后的值是否有效，无效则提示错误
                        if (!$model_ajax->valid_value($collection)) {
                            $r = array(
                                "suc" => -3,
                                "msg" => $model_ajax->msg
                            );
                            die(json_encode($r));
                        }



                        //获取更改的键
                        $dirty_keys = array_keys($dirty);

                        //获取原始数据,original_final为有更改的原始数据
                        $original = $collection->getOriginal();
                        $original_final = array();
                        $name = array();
                        foreach ($original as $key => $value) {
                            if (in_array($key,$dirty_keys)) {
                                $original_final[$key] = $original[$key];
                                $name[$key] = $collection->item->$key->name;
                            }
                        }

                        //返回确认界面
                        $html = "请确认您的数据:";
                        foreach ($dirty as $key => $value) {
                            $html .= "<br>".$name[$key].":<del>".$original_final[$key]."</del> ".$dirty[$key];
                        }
                        $r = array(
                                "suc" => 1,
                                "msg" => $html,
                                "model" => $collection->get_table(),
                                "id" => $_POST["for_id"],
                                "dirty" => $dirty,
                                "original" => $original_final,
                                "name" => $name
                            );
                        echo json_encode($r);
                    }
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

            //建立main_model,main_model用于获取模型信息
            $class_name = "\App\\".$_POST["model"];
            $main_model = new $class_name();

            //利用main_model获取信息
            $main_model->parent_scope = false;//暂时关闭全局SCOPE
            if ($_POST["type"] && strlen($_POST["type"]) > 0) {
                //如果存在type的方法，则执行，用于通过方法进行分类的model，如setting
                if (method_exists($main_model, $_POST["type"])) {
                    $main_model->{$_POST["type"]}();
                } else if(sizeof($addition_where = explode("#",$_POST["type"])) <= 1){
                    $main_model->parent($_POST["type"]);
                }
            }
            //如果show==0
            if (strlen($show) == 0) {
                $bind = $main_model->item->{$_POST["col"]}->bind;
                $bind_addition = $main_model->item->{$_POST["col"]}->bind_addition;
                $history = $main_model->item->{$_POST["col"]}->history;
            } else {
                $bind = array();
                $history = "";
            }
            


            //获取值
            if (sizeof($bind) >= 3 || $history !== false) {

                if (strlen($show) > 0) {//当显示与数值不一致时执行,用于元素bind字段绑定
                    $model = $main_model;
                    $value_col = $_POST["col"];
                    if ($show == "1") {
                        $where_col = DB::raw($value_col);
                        $select = array($value_col);
                    } else {
                        $where_col = DB::raw($show);
                        $select = array(DB::raw($show),DB::raw($value_col));
                    }
                } else if (sizeof($bind) >= 3) {//当读取model的bind时
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
                    $model->parent_scope = true;//重新启用全局SCOPE
                    if ($_POST["type"] && strlen($_POST["type"]) > 0) {
                        //如果存在type的方法，则执行，用于通过方法进行分类的model，如setting
                        if (method_exists($model, $_POST["type"])) {
                            $model->{$_POST["type"]}();
                        } else if(sizeof($addition_where = explode("#",$_POST["type"])) <= 1){
                            $model->parent($_POST["type"]);
                        }
                    }
                    //if ($_POST["type"] && strlen($_POST["type"]) > 0) {
                        //$model->parent($_POST["type"]);
                    //}
                    $value_col = $_POST["col"];
                    $where_col = $_POST["col"];
                }

                $where_col = DB::raw($where_col);

                $collection = $model->select($select)->where($where_col,"<>","")->WhereNotNull($where_col);

                if ($_POST["type"] && strlen($_POST["type"]) > 0) {
                    if (!method_exists($model, $_POST["type"])) {
                        $addition_where = explode("#",$_POST["type"]);
                        if (sizeof($addition_where) > 1) {
                            if (isset($addition_where[2])) {
                                $collection->where($addition_where[0],$addition_where[1],$addition_where[2]);
                            } else {
                                $collection->where($addition_where[0],$addition_where[1]);
                            }
                        }
                    }
                }
                //echo $model->item->get_only();
                if (sizeof($bind) > 3) {
                    //$collection->withoutGlobalScopes();
                    //$collection->bootSoftDeletes();
                    $collection->where($bind[3]);
                }
                if (is_object($history)) {
                    $collection->where($history);
                }
                if (strlen($value) > 0){
                    $value = str_replace("/",",",str_replace("{","",str_replace("}","",str_replace("}{",",",$value))));
                    $collection->whereIn($value_col,string_to_array($value));
                }
                if (strlen($search) > 0) {
                    $words = preg_split("/[-.,;!\s']\s*/", $search);
                    foreach ($words as $word => $value) {
                        $collection->where($where_col,"LIKE","%".$value."%");
                    }   
                }
                if ($group == 1) {
                    //foreach ($select as $value) {
                        //$collection->groupby($value);
                    //}
                    $collection->groupby($value_col);
                }
                //if (get_parent_class($model) == "App\\table_model" && $model->table_version && $current !== false) {
                    //$collection->where($model->get_table().".current_version",$current);
                //}
                $data = $collection->limit($limit)->get()->toArray();
                
                //将addition数据载入(前提是没有指定值，指定值则表示唯一匹配，用于add_from_val)
                if (strlen($value) == 0 && isset($bind_addition)) {
                    foreach ($bind_addition as $key => $ba) {
                        if (sizeof($select) == 2) {
                            if (!is_numeric($key)) {
                                $addition_data = array($key,$ba);
                            } else {
                                $addition_data = array($ba,$ba);
                            }
                        } else {
                            $addition_data = array($ba);
                        }
                        array_unshift($data,$addition_data);
                    }
                //没找到匹配项的指定值，从bind_addition中载入
                } else if (strlen($value) > 0 && isset($bind_addition) && sizeof($data) == 0){
                    foreach ($bind_addition as $key => $ba) {
                        if ($ba == $value) {
                            $data[] = array($key,$ba);
                            break;
                        }
                    }
                    
                }
                
                if (sizeof($data) == 0) {
                    $r = array(
                        "suc" => -1,
                        "msg" => "数据为空",
                        "data" => $data
                    );
                } else {
                    $r = array(
                        "suc" => 1,
                        "msg" => "获取数据成功",
                        "data" => $data
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

    function dt_add(){
        $class_name = "\\App\\".$_GET["model"];
        $model = new $class_name();

        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new view("layouts/page_detail");
        //$sview->title(array("操作","名称","备注","条件","录入人","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    

    function dt_edit(){

        $class_name = "\\App\\".$_GET["model"];
        $model = new $class_name();

        $ids = multiple_to_array($_GET["id"]);

        if (sizeof($ids) > 1) {
            $title_array = $model->titles_init("操作");
            $title_amount = sizeof($title_array);
            $sview = new datatables("layouts/table_select",["width" => $title_amount<7?"100%":(strval($title_amount*105)."px")],$_GET["model"]."@edit_detail_list",$_GET["id"]);
            $sview->title($title_array);
            return $sview;
        } else {
            if (isset($_GET["para"]) && $_GET["para"] != "") {
                if (method_exists($model,$_GET["para"])) {
                    $model->{$_GET["para"]}();
                } else if ($model->default_method !== false) {
                    $use_method = $model->default_method;
                    $model->$use_method($_GET["para"]);
                }
                
            }
            $collection = $model->onlySoftDeletes()->find($ids[0]);

            //如果模型中有dt_edit方法，则运行，用于授权等操作
            if (isset($_GET["auth"]) && method_exists($model,$_GET["auth"])) {
                $collection->{$_GET["auth"]}();
            }

            $input_view = new view("form/ajax_form",["model" => $model,"collection" => $collection]);
            $sview = new view("layouts/page_detail");
            //$sview->title(array("操作","名称","备注","条件","录入人","时间"));
            $sview->info("panel_body",$input_view->render());
            return $sview;
        }
        
    }

    function dt_alt_info(){

        $class_name = "\\App\\".$_GET["model"];
        $model = new $class_name();

        $ids = multiple_to_array($_GET["id"]);

        if (sizeof($ids) > 1) {

            //流程信息
            if (isset($_GET["proc_id"])) {
                $info = \App\procedure\procedure::load($_GET["proc_id"]);
                $page_info = $info->pd_info();
            } else {
                $page_info = null;
            }

            $title_array = $model->titles_init("操作");
            $title_amount = sizeof($title_array);
            $sview = new datatables("layouts/table_select",["width" => $title_amount<7?"100%":(strval($title_amount*105)."px"),"page_info" => $page_info],$_GET["model"]."@edit_detail_list",$_GET["id"]);
            $sview->title($title_array);
            return $sview;
        } else {

            $id = $ids[0];

            if (isset($_GET["para"]) && $_GET["para"] != "") {
                if (method_exists($model,$_GET["para"])) {
                    $model->{$_GET["para"]}();
                } else if ($model->default_method !== false) {
                    $use_method = $model->default_method;
                    $model->$use_method($_GET["para"]);
                }
                
            }
            $collection = $model->onlySoftDeletes()->find($id);
            if (strlen($collection->procedure) == 0) {
                $proc_info = null;
            } else {
                $proc = \App\procedure::find($collection->procedure);
                $proc_info = json_decode($proc->pd_info);
            }
            $input_view = new view("form/ajax_form",["model" => $model,"collection" => $collection, "alt" => 1, "proc_info" => $proc_info]);
            $sview = new view("panel/alt_info_detail");
            //$sview->title(array("操作","名称","备注","条件","录入人","时间"));
            $sview->info("panel_body",$input_view->render());
            return $sview;
        }
    
    }

    function dt_version_update(){
        $input_view = new view("form/proc_form");
        $sview = new view("layouts/page_detail");
        //$sview->title(array("操作","名称","备注","条件","录入人","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //显示procedure的审批页面
    function view_procedure(){
        $proc = \App\procedure\procedure::load($_GET["proc_id"]);
        $input_view = new view("form/proc_form",["proc" => $proc]);
        $sview = new view("layouts/page_detail");
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //显示procedure的详情页面
    function procedure_info(){
        if (!isset($_GET["pd_class"]) || !isset($_GET["proc_id"])) {
            return "信息错误";
        } else {
            $pd_name = "\\App\\procedure\\".$_GET["pd_class"];
            $procedure = new $pd_name($_GET["proc_id"]);
            if ($procedure->view_page === false) {
                return "没有详细信息";
            } else if ($procedure->view_page === true) {
                return $procedure->view_page();
            } else if (method_exists($this,$procedure->view_page)) {
                return $this->{$procedure->view_page}();
            }
        }
    }
    //流程创建
    function procedure_create(){
        if (isset($_POST["model"]) && isset($_POST["id"])) {
            if (!isset($_POST["proc"])) {
                //未传递流程名称的流程，兼容老代码
                
                //有cancel则启动cancel流程
                if (isset($_POST["cancel"])) {
                    $proc = new \App\procedure\cancel_procedure("",$_POST["model"],$_POST["id"]);
                } else {
                    $proc = new \App\procedure\status_avail_procedure("",$_POST["model"],$_POST["id"]);
                }

            } else {

                $proc_name = "\\App\\procedure\\".$_POST["proc"];

                $proc = new $proc_name("",$_POST["model"],$_POST["id"]);

            }


            if (isset($_POST["pd_name"])) {
                $proc->name($_POST["pd_name"]);
            }
            
                
            
            //} catch(\Exception $e){
            if (!$proc->create_proc()) {
                $r = array(
                    "suc" => -1,
                    "msg" => "流程创建失败。".$proc->msg,
                    "error" => $proc->msg
                );
                die(json_encode($r));
            }
            $r = array(
                "suc" => 1,
                "msg" => "流程创建成功",
                "proc_id" => $proc->proc_id,
                "id" => $_POST["id"]
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
            $proc = \App\procedure\procedure::load($proc_id);
            try {
                $proc->pass_proc($comment,$owner);
            } catch (\Exception $e) {
                $r = array(
                    "suc" => -1,
                    "msg" => $e->getMessage()
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
                $proc = \App\procedure\procedure::load($_POST["proc_id"]);
                $proc->rollback_proc($comment);
            } catch(\Exception $e){
                $r = array(
                    "suc" => -1,
                    "msg" => isset($e->errorInfo[2])?$e->errorInfo[2]:$e
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


    function model_cal(){

        //获得需要计算的公式
        $class_name = "\\App\\".$_POST["model"];
        $model = new $class_name();
        $cal = $model->item->get_cal();

        //计算结果数组
        $result = array();
        //对每个计算公式进行计算
        foreach ($cal as $c) {
            //预先设置计算结果为FALSE
            $result_cal = false;
            //判断是否需要计算
            if (isset($c[2]) && ($c[2] === true || (is_callable($c[2]) && $c[2]($_POST)))) {
                //$c为单个公式，$c[0]为参数，如果为文本则是单一参数，为数组则是多参数，如果参数有改变，则需要计算，如条件1、条件2
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
                "suc" => 1,
                "msg" => "无需计算"
            );
            die(json_encode($r));
        }
    }

    function model_valid(){
        $class_name = "\\App\\".$_POST["model"];
        $model = new $class_name();
        if (isset($_POST["valid_data"])) {
            foreach ($_POST["valid_data"] as $key => $value) {
                $valid = $model->item->valid_value($value["col"],$value["value"]);
                if ($valid === true) {
                    $_POST["valid_data"][$key]["valid"] = true;
                } else {
                    $_POST["valid_data"][$key]["valid"] = $model->item->msg();
                }
            }
            $r = array(
                "suc" => 1,
                "msg" => "获取数据成功",
                "valid_data" => $_POST["valid_data"]
            );
                    die(json_encode($r));
        } else if (isset($_POST["valid_col"])) {
            $valid = $model->item->valid_value($_POST["valid_col"],$_POST["valid_value"]);
            if ($valid === true) {
                $r = array(
                    "suc" => 1,
                    "msg" => "验证成功"
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => $model->item->msg(),
                    "origin" => ""
                );
                die(json_encode($r));
            }
        } else {
            $r = array(
                "suc" => -1,
                "msg" => "数据错误"
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
                    "msg" => "写入失败,".$data->msg
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

    //（POST）模型的值验证
    function value_valid(){
        if (isset($_POST["model"]) && isset($_POST["col"]) && isset($_POST["value"])) {
            $model_name = "\\App\\".$_POST["model"];
            $model = new $model_name();

        } else {
            die("数据错误");
        }
    }

    //（POST）变更确认
    function alt_confirm(){
        if (valid_post("model","id","dirty","original")) {
            try{
                if (isset($_POST["pressure_test"])) {
                    $proc = new \App\procedure\alt_pressure_test_procedure("",$_POST["model"],$_POST["id"]);
                    $pd_class = "alt_pressure_test_procedure";
                } else if(isset($_POST["exam_specify"])){
                    $proc = new \App\procedure\alt_exam_specify_procedure("",$_POST["model"],$_POST["id"]);
                    $pd_class = "alt_exam_specify_procedure";
                } else {
                    $proc = new \App\procedure\alt_procedure("",$_POST["model"],$_POST["id"]);
                    $pd_class = "alt_procedure";
                }
                if (isset($_POST["pressure_test"])) {
                    if ($_POST["dirty"]["pressure_test"] == 1) {
                        $proc->name("水压变更流程（无转有）");
                    } else {
                        $proc->name("水压变更流程（有转无）");
                    }
                    
                } else if(isset($_POST["exam_specify"])){
                    $proc->name("指定检验比例流程");
                } else if (is_array($_POST["id"])) {
                    $proc->name("批量变更流程");
                } else {
                    $proc->name("单个信息变更流程");
                }
                $proc->info(json_encode(array("dirty" => $_POST["dirty"], "original" => $_POST["original"])));
                if (!$proc->create_proc()) {
                    $r = array(
                        "suc" => -1,
                        "msg" => $proc->msg
                    );
                    echo(json_encode($r));
                } else {
                    $r = array(
                        "suc" => 1,
                        "msg" => "流程创建成功",
                        "proc_id" => $proc->proc_id,
                        "pd_class" => $pd_class
                    );
                    echo(json_encode($r));
                }
            } catch(\Exception $e){
                $r = array(
                    "suc" => -1,
                    "msg" => "流程创建失败",
                    "error" => $e
                );
                die(json_encode($r));
            }
            
        } else {
            die("数据错误");
        }
    }

    

}
