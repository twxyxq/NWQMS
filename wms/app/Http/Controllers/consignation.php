<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use datatables;
use view;

class consignation extends Controller
{
   
    //(datatable)手动分组
    function manual_add(){
        $sview = new datatables("consignation/manual_add","wj@wj_no_consignation",$_GET["emethod"]);
        $sview->title(array("操作","类型","焊口号","规格",$_GET["emethod"]."比例","焊工","方法"));
        //$sview->option("info: false");
        $sview->option("length: 5");
        //$sview->option("lengthChange: false");
        $sview->option("lengthMenu: [ 5, 10, 20 ]");
        return $sview;
    }
    //(datatable)额外委托分组
    function manual_addition_add(){
        $sview = new datatables("consignation/manual_add","wj@wj_finished",$_GET["emethod"]);
        $sview->title(array("操作","类型","焊口号","规格",$_GET["emethod"]."比例","焊工","方法"));
        //$sview->option("info: false");
        $sview->option("length: 5");
        //$sview->option("lengthChange: false");
        $sview->option("lengthMenu: [ 5, 10, 20 ]");
        return $sview;
    }

    //(datatable)分组清单
    function group_list(){
        $sview = new datatables("layouts/panel_table","exam_plan@ep_list");
        $sview->title(array("操作","分组名称","方法","类型","系统","焊工","工艺卡","创建人","日期"));
        $sview->order(8,"desc");
        return $sview;
    }

    //(datatable)打印委托单
    function no_sheet(){
        $sview = new datatables("consignation/generate_sheet","exam@exam_no_consignation_sheet",$_GET["emethod"]);
        $sview->title(array("操作","焊缝号","类型","方法","分组名称","系统","焊工","录入人","日期"));
        $sview->order(8,"desc");
        return $sview;
    }

    //(datatable)委托单列表
    function consignation_sheet(){
    	if (isset($_GET["emethod"])) {
    		$emethod = $_GET["emethod"];
    	} else {
    		$emethod = "";
    	}
        $sview = new datatables("layouts/panel_table","exam_sheet@sheet_list",$emethod);
        $sview->title(array("序号","委托单号","检验方法","焊口类型","系统","录入人","日期"));
        $sview->order(6,"desc");
        return $sview;
    }


    //(function)复验支撑函数
    function exam_status($pdata,$samples,&$conclusion){
        $result = DB::table("exam")->where("deleted_at","2037-12-31")->where("exam_plan_id",$pdata["id"])->whereIn("exam_wj_id",$samples)->whereNotNull("exam_input_time");
        $conclusion = "合格";
        $result_size = 0;
        foreach ($result->cursor() as $r) {
            if ($r["exam_conclusion"] == "不合格") {
                $conclusion = "不合格";
            }
            $result_size++;
        }
        if ($result_size < sizeof($samples)) {
           $info_text = "[检验中]";
           if ($conclusion == "不合格") {
               $info_text .= "[不合格]";
           }
        } else {
            $info_text = "[".$conclusion."]";
        }
        return $info_text;
    }

    //(datatable)复验
    function consignation_addition(){

        //$plan_data = \App\exam_plan::whereRaw("ep_wj_all_samples_count < ep_wj_count")->limit(100);
        $plan_data = DB::table("exam_plan")->whereRaw("ep_wj_all_samples_count < ep_wj_count")->where("deleted_at","2037-12-31");

        if(isset($_GET["emethod"])){
            $plan_data->where("ep_method",$_GET["emethod"]);
        }


        //$plan_data = $plan_data->get();


        $data = array();

        foreach ($plan_data->cursor() as $pdata) {
            $t_array = array();
            $t_array[] = $pdata["id"];
            $t_array[] = "<a href=\"###\" onclick=\"new_flavr('/consignation/group_detail?id=".$pdata["id"]."')\">".$pdata["ep_code"]."</a>";
            //总数
            $wj_ids = multiple_to_array($pdata["ep_wj_ids"]);
            $t_array[] = sizeof($wj_ids);
            //首抽
            $wj_samples = multiple_to_array($pdata["ep_wj_samples"]);
            $t_array[] = sizeof($wj_samples);
            $t_array[] = $this->exam_status($pdata,$wj_samples,$conclusion);
            //加倍抽样
            $wj_addition_samples = multiple_to_array($pdata["ep_wj_addition_samples"]);
            if (sizeof($wj_addition_samples) == 0) {
                if ($conclusion == "不合格" && sizeof($wj_samples) < sizeof($wj_ids)) {
                    $t_array[] = "<a class=\"btn btn-warning btn-small\" onclick=\"new_flavr('/consignation/group_detail?id=".$pdata["id"]."')\">加倍</a>";
                    $t_array[] = "";
                } else {
                    $t_array[] = "N/A";
                    $t_array[] = "N/A";
                }
                //重新设为合格
                $conclusion = "合格";
            } else {
                $t_array[] = sizeof($wj_addition_samples);
                $t_array[] = $this->exam_status($pdata,$wj_addition_samples,$conclusion);
            }
            
            //全部抽样
            $wj_another_samples = multiple_to_array($pdata["ep_wj_another_samples"]);
            if (sizeof($wj_another_samples) == 0) {
                if ($conclusion == "不合格" && sizeof($wj_samples)+sizeof($wj_addition_samples) < sizeof($wj_ids)) {
                    $t_array[] = "<a class=\"btn btn-warning btn-small\" onclick=\"new_flavr('/consignation/group_detail?id=".$pdata["id"]."')\">全抽</a>";
                    $t_array[] = "";
                } else {
                    $t_array[] = "N/A";
                    $t_array[] = "N/A";
                }
            } else {
                $t_array[] = sizeof($wj_another_samples);
                $t_array[] = $this->exam_status($pdata,$wj_another_samples,$conclusion);
            }


            if(!isset($_GET["unaccpet"]) || $t_array[4] == "[不合格]"){
                $data[] = $t_array;
            }

            
        }

        $sview = new datatables("layouts/panel_table",$data);
        $sview->title(array("操作","分组名称","焊口数","首抽数","检验情况","加倍数","加倍情况","全抽数","全抽情况"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //（页面）分组详情
    function group_detail($del = false){

        if (isset($_GET["id"])) {
            
            if ($del === false) {
                $exam_plan = \App\exam_plan::find($_GET["id"]);
            } else {
                $exam_plan = \App\exam_plan::withoutGlobalScopes(["softdeleted"])->find($_GET["id"]);
            }
            
            //获取焊口清单
            if ($exam_plan->ep_wj_ids != null || strlen($exam_plan->ep_wj_ids) > 0) {
                $wj_ids = multiple_to_array($exam_plan->ep_wj_ids);
                $wjs = \App\wj::withoutGlobalScopes(["softdeleted"])->find($wj_ids);
            }

            //抽样焊口及结果
            $exam_result = $exam_plan->get_exam_result();


            //**************************************************************
            //构造表格数据
            //将构造数据存储在exam_plan和wjs里面
            $conclusion = "合格";
            $exam_plan->status = "检验完成";
            //首抽数据
            foreach ($wjs as $key => $wj) {
                if (in_array($wj->id,$exam_result->samples)) {
                    foreach ($exam_result->samples_exam as $exam) {
                        if ($exam->exam_wj_id == $wj->id) {
                            if ($exam->exam_input_time != null) {
                                $wjs[$key]->samples = $exam->exam_conclusion;
                                if ($exam->exam_conclusion == "不合格") {
                                    $conclusion = "不合格";
                                }
                            } else {
                                $wjs[$key]->samples = "正在检验";
                                $exam_plan->status = "正在检验";
                            }
                        }
                    }
                    $wjs[$key]->addition_samples = "N/A";
                    $wjs[$key]->another_samples = "N/A";
                } else {
                    $wjs[$key]->samples = "/";
                }
            }
            //加倍数据
            if ($conclusion == "不合格" && sizeof($wj_ids) > sizeof($exam_result->samples) && !isset($exam_result->addition_samples)) {
                $exam_plan->status = "不合格，需复验";
                foreach ($wjs as $key => $wj) {
                    if ($wjs[$key]->addition_samples != "N/A") {
                        $wjs[$key]->addition_samples = "<button class=\"btn btn-warning btn-small\" onclick=\"addition_examination()\">加倍复验</button>";
                    }
                }
            } else if ($conclusion != "不合格" && $exam_plan->status == "正在检验"){
                foreach ($wjs as $key => $wj) {
                    $wjs[$key]->addition_samples = "";
                    $wjs[$key]->another_samples = "";
                }
            } else if (isset($exam_result->addition_samples)) {
                //重置结论
                $conclusion = "合格";
                foreach ($wjs as $key => $wj) {
                    if (in_array($wj->id,$exam_result->addition_samples)) {
                        foreach ($exam_result->addition_samples_exam as $exam) {
                            if ($exam->exam_wj_id == $wj->id) {
                                if ($exam->exam_input_time != null) {
                                    $wjs[$key]->addition_samples = $exam->exam_conclusion;
                                    if ($exam->exam_conclusion == "不合格") {
                                        $conclusion = "不合格";
                                    }
                                } else {
                                    $wjs[$key]->addition_samples = "正在检验";
                                    $exam_plan->status = "正在检验";
                                }
                            }
                        }
                        $wjs[$key]->another_samples = "N/A";
                    } else {
                        $wjs[$key]->addition_samples = "/";
                    }
                }
                //全检数据
                if ($conclusion == "不合格" && sizeof($wj_ids) > sizeof($exam_result->samples)+sizeof($exam_result->addition_samples) && !isset($exam_result->another_samples)) {
                    $exam_plan->status = "不合格，需复验";
                    foreach ($wjs as $key => $wj) {
                        if ($wjs[$key]->another_samples != "N/A") {
                            $wjs[$key]->another_samples = "<button class=\"btn btn-warning btn-small\" onclick=\"another_examination()\">全部复验</button>";
                        }
                    }
                } else if ($conclusion != "不合格" && $exam_plan->status == "正在检验"){
                    foreach ($wjs as $key => $wj) {
                        $wjs[$key]->another_samples = "";
                    }
                } else if (isset($exam_result->another_samples)) {
                    foreach ($wjs as $key => $wj) {
                        if (in_array($wj->id,$exam_result->another_samples)) {
                            foreach ($exam_result->another_samples_exam as $exam) {
                                if ($exam->exam_wj_id == $wj->id) {
                                    if ($exam->exam_input_time != null) {
                                        $wjs[$key]->another_samples = $exam->exam_conclusion;
                                        if ($exam->exam_conclusion == "不合格") {
                                            $conclusion = "不合格";
                                        }
                                    } else {
                                        $wjs[$key]->another_samples = "正在检验";
                                        $exam_plan->status = "正在检验";
                                    }
                                }
                            }
                        } else {
                            $wjs[$key]->another_samples = "/";
                        }
                    }
                }
            }

            $sheet_view = new view("sheet/group_sheet",["exam_plan" => $exam_plan,"wjs" => $wjs]);

            $sview = new view("consignation/group_detail",["sheet" => $sheet_view->render()]);
            return $sview;

        } else {
            return "数据错误";
        }
        
    }


    //（页面）委托单详情，兼顾生成、打印和浏览
    function sheet_detail($del = false){

        //通过ID获取委托单详情
        if (isset($_GET["sheet_id"])) {

            if ($del === false) {
                $info = \App\exam_sheet::find($_GET["sheet_id"]);
            } else {
                $info = \App\exam_sheet::withoutGlobalScopes(["softdeleted"])->find($_GET["sheet_id"]);
            }
            

            $wjs = \App\exam::select("exam.id","vcode",DB::raw(SQL_BASE." as base"),"jtype","tsk_id","ild","sys","wj_type","exam_plan_id")->leftjoin("wj","wj.id","exam.exam_wj_id")->where("exam.exam_sheet_id",$_GET["sheet_id"])->get();
        
        //通过编号获取委托单详情
        } else if (isset($_GET["sheet_code"])) {

            $info = \App\exam_sheet::where("es_code",$_GET["sheet_code"])->get()[0];

            $wjs = \App\exam::select("exam.id","vcode",DB::raw(SQL_BASE." as base"),"jtype","tsk_id","ild","sys","wj_type","exam_plan_id")->leftjoin("wj","wj.id","exam.exam_wj_id")->where("exam.exam_sheet_id",$info->id)->get();

        //生成委托单
        } else if (isset($_GET["ids"]) && isset($_GET["exam_method"])) {

            $wjs = \App\exam::select("exam.id","vcode",DB::raw(SQL_BASE." as base"),"jtype","tsk_id","ild","sys","wj_type","exam_plan_id")->leftjoin("wj","wj.id","exam.exam_wj_id")->whereIN("exam.id",explode("/",$_GET["ids"]))->get();

            $info = new \stdClass();

            $info->es_method = $_GET["exam_method"];
            $info->created_by = Auth::user()->id;
            $info->created_at = \Carbon\Carbon::now();
            $info->es_demand_date = "<input type=\"text\" name=\"es_demand_date\" class=\"form_date form-control input-sm\" data-date-format=\"yyyy-mm-dd\" startdate=\"".\Carbon\Carbon::now()->toDateString()."\" readonly=\"true\">";

            $ild = "";
            $sys = "";
            $wj_type = "";
            $data_valid = 1;
            $sheet_valid = 1;
            foreach ($wjs as $wj) {
                if (($wj->ild != $ild || $wj->sys != $sys || $wj->wj_type != $wj_type) && ($ild != "" || $sys != "" || $wj_type != "")) {
                    $data_valid = 0;
                }
                if ($wj->exam_sheet_id > 0) {
                    $sheet_valid = 0;
                }
                $ild = $wj->ild;
                $sys = $wj->sys;
                $wj_type = $wj->wj_type;
            }
            if ($data_valid == 0) {
                return "焊口数据错误,类型、机组、系统不统一";
            } else if ($sheet_valid == 0){
                return "部分焊口已打印，生成失败";
            } else {

                $es_code_num = \App\exam_sheet::select(DB::raw("IF(MAX(RIGHT(es_code,3)),LPAD(MAX(RIGHT(es_code,3))+1,3,0),'001') as num"))->where("es_method",$_GET["exam_method"])->where("es_ild_sys",$ild.$sys)->where("es_code_specify",0)->get()[0]->num;


                $info->es_code = "<input type=\"text\" name=\"es_code\" value=\"".$ild."CI-".$sys."-".$_GET["exam_method"]."-".$es_code_num."\" class=\"form-control input-sm\" style=\"width:230px;display:inline-block;\" readonly=\"readonly\"> <input type=\"checkbox\" name=\"es_code_specify\" value=\"1\" onclick=\"change_specify()\">自定编号";
                $info->es_ild_sys = $ild.$sys;
                $info->es_wj_type = $wj_type;
            }

        } else {
            return "获取焊口失败";
        }

        $sheet_view = new view("sheet/consignation_sheet",["info" => $info,"wjs" => $wjs]);

        $sview = new view("consignation/sheet_detail",["sheet" => $sheet_view->render()]);
        return $sview;


       
    }

     //（页面）委托单详情，兼顾生成、打印和浏览
    function sheet_modify($del = false){

        //通过ID获取委托单详情
        if (isset($_GET["sheet_id"])) {

            if ($del === false) {
                $info = \App\exam_sheet::find($_GET["sheet_id"]);
            } else {
                $info = \App\exam_sheet::withoutGlobalScopes(["softdeleted"])->find($_GET["sheet_id"]);
            }
            
            $wjs = \App\exam::select("exam.id","vcode",DB::raw(SQL_BASE." as base"),"jtype","tsk_id","ild","sys","wj_type","exam_plan_id")->leftjoin("wj","wj.id","exam.exam_wj_id")->where("exam.exam_sheet_id",$_GET["sheet_id"])->get();
        }

        $sview = new view("consignation/sheet_modify",["info" => $info,"wjs" => $wjs]);
        
        return $sview;

    }

    //（POST），生成委托单
    function generate_sheet(){
        if(valid_post("es_code","es_demand_date","es_ild_sys","es_wj_type","es_exam_ids_text","es_method","es_code_specify")){

            $wjs = \App\exam::where("exam_sheet_id",0)->whereIN("id",explode("/",$_POST["es_exam_ids_text"]))->get();

            if (sizeof($wjs) != sizeof(explode("/",$_POST["es_exam_ids_text"]))) {
                die("部分焊口已打印，生成失败");
            } else {

                $exam_sheet = new \App\exam_sheet();

                $es_code_num = $exam_sheet->select(DB::raw("IF(MAX(RIGHT(es_code,3)),LPAD(MAX(RIGHT(es_code,3))+1,3,0),'001') as num"))->where("es_method",$_POST["es_method"])->where("es_ild_sys",$_POST["es_ild_sys"])->where("es_code_specify",0)->get()[0]->num;

                //如果编号与POST过来的不一致，需要重新生成，保证可见即所得
                if ($_POST["es_code"] != substr($_POST["es_ild_sys"],0,1)."CI-".substr($_POST["es_ild_sys"],1)."-".$_POST["es_method"]."-".$es_code_num) {
                    die("委托编号已改变,需刷新页面重新生成"); 
                }

                //重复验证，如果编号重复，而且已经存在的变化为指定编号，则修改原来存在的编号为非指定（该步骤需要授权）
                $duplicate_vaild = $exam_sheet->where("es_code",$_POST["es_code"])->where("es_code_specify",1)->get();
                
                if(sizeof($duplicate_vaild) > 0){
                    $duplicate_change = $exam_sheet->find($duplicate_vaild[0]->id);
                    $duplicate_change->es_code_specify = 0;
                    $duplicate_change->authorize_user(Auth::user()->id);//用户授权
                    $duplicate_change->save();
                    $r = array(
                        "suc" => 2,
                        "msg" => "系统中存在指定编号与该编号重复，已修改该编号为非指定，页面会刷新，请重新生成委托。"
                    );
                    die(json_encode($r));
                } else {
                    $data = filt_array_by_key($_POST,array("es_code","es_demand_date","es_ild_sys","es_wj_type","es_method","es_code_specify"));
                    $ids_array = explode("/",$_POST["es_exam_ids_text"]);
                    $data["es_exam_ids"] = array_to_multiple($ids_array);

                    try {
                        $exam_sheet->sheet_create($data,$ids_array);
                    } catch (\Exception $e) {
                        die("生成失败:".$e->getMessage());
                    }

                    $r = array(
                        "suc" => 1,
                        "msg" => "生成成功，可点击打印",
                        "exam_id" => $exam_sheet->id
                    );
                    die(json_encode($r));
                    //get_post($exam_sheet,array("es_code","es_demand_date","es_ild_sys","es_wj_type","es_method","es_code_specify"));
                    //$exam_sheet->es_exam_ids = array_to_multiple(explode("/",$_POST["es_exam_ids_text"]));
                    
                }



            }

        } else {
            echo "数据错误";
        }
    }

    //(POST)分组添加
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

                //将抽取的焊口写入exam
                foreach ($samples_array as $id) {
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

    function addition_examination(){
        if (isset($_POST["ep_id"])) {
            $exam_plan = new \App\exam_plan();
            if ($exam_plan->addition_examination($_POST["ep_id"])){
                $r = array(
                    "suc" => 1,
                    "msg" => "加倍复验提交成功"
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => $exam_plan->msg
                );
                die(json_encode($r));
            }
        } else {
            die("数据错误");
        }
        

    }
    function another_examination(){
        if (isset($_POST["ep_id"])) {
            $exam_plan = new \App\exam_plan();
            if ($exam_plan->another_examination($_POST["ep_id"])){
                $r = array(
                    "suc" => 1,
                    "msg" => "全部复验提交成功"
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => $exam_plan->msg
                );
                die(json_encode($r));
            }
        } else {
            die("数据错误");
        }
        

    }




}
