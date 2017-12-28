<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\wj_model;
use App\wj_base_model;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Crypt;
use view;
use nav;

class panel extends Controller
{
    
    protected $default_page = "common";

    //protected $panel_nav_item = "<li class='panel_nav_item col-xs-6 col-sm-4 col-md-3 col-lg-2'><a href='/#/#0#/#'><span class='glyphicon glyphicon-th' style='display:block;font-size:30px;'></span><span id='#/#0#/#'>#/#1#/#</span></a></li>";


    //panel的默认设置
    function panel_default($pview){
        $this->nav = new nav($this->name,"index");
        if ($this->nav->current_item) {
            $current_module = $this->nav->current_module->title;
        } else {
            $current_module = "模块选择";
        }
        //$pview->html = str_replace("模块", "", $pview->html);
        $pview->info("current_module",$current_module);
        $pview->info("module",$this->nav->module_data($current_module),"<a href='/#/#0#/#'>#/#1#/#</a>");
        $pview->info("panel_nav",$this->nav->module_data());
        return $pview;
    }

    function app_startup(){
        $app = new \App\eps();
        $app = new \App\exam();
        $app = new \App\exam_item();
        $app = new \App\exam_plan();
        $app = new \App\exam_report();
        $app = new \App\exam_sheet();
        $app = new \App\gps();
        $app = new \App\material_sheet();
        $app = new \App\modify_history();
        $app = new \App\pp();
        $app = new \App\procedure();
        $app = new \App\procedure_item();
        $app = new \App\qf_range();
        $app = new \App\qp();
        $app = new \App\qp_proc();
        $app = new \App\qp_proc_model();
        $app = new \App\qualification();
        $app = new \App\secondary_store();
        $app = new \App\setting();
        $app = new \App\tsk();
        $app = new \App\User();
        $app = new \App\validation();
        $app = new \App\validation_plan();
        $app = new \App\wj();
        $app = new \App\wj_base();
        $app = new \App\wj_model();
        $app = new \App\wpq();
        $app = new \App\wps();
    }



    function index(){
        $pview = new view("panel/index");
        return $this->panel_default($pview)->render();
    }

    function user_login(){
        if (isset($_GET["id"])) {
            if (Auth::user()->user_level == 9) {
                Auth::logout();
                Auth::loginUsingId($_GET["id"]);
                return "用户成功切换:".Auth::user()->code.Auth::user()->name;
            } else {
                return "您没有权限";
            }
        } else {
            return "获取id错误";
        }
    }


    function common($page){
        $pview = new view("panel/common");
        $pview->info("panel_nav",$this->nav->current_item->children_array());

        return $pview;
    }

    function change_user_password(){
        $fview = new view("panel/change_user_password");
        return $fview;
    }

    function to_do_list(){
        $pview = new \datatables("layouts/panel_table","procedure@to_do");
        //$pview = $this->panel_default($pview);
        $pview->info("current_nav","<a href=\"/home\">个人工作台</a> -> <a href=\"/panel/to_do_list\">待办流程</a>");
        $pview->title(array("操作","流程类型","焊口数","当前责任人","发起人","发起时间"));
        return $pview;
    }

    function authority(){
        /*
        $users = \App\user::select("id",DB::raw("CONCAT('<a href=\"###\" onclick=\"new_flavr(\\'/panel/user_auth?id=',id,'\\')\">',code,'</a>')"),"name","auth","created_at","user_org")->where("user_level","<=",Auth::user()->user_level)->where(function($query){
                $query->orWhere("user_org",Auth::user()->user_org);
                $query->orWhere("user_org","N/A");
            })->get()->toArray();
        */
        //dd($users);
        $pview = new \datatables("layouts/panel_table","User@user_list");
        //$pview = $this->panel_default($pview);
        $pview->info("current_nav","<a href=\"/home\">个人工作台</a> -> <a href=\"/panel/authority\">人员授权</a>");
        $pview->title(array("操作","账号","姓名","权限","创建时间","分组"));
        return $pview;
    }

    function wj_rate_check(){
        $wjs = DB::table("wj")->where("deleted_at","2037-12-31")->where("exam_specify",0)->where("tsk_id",0)->get()->toArray();
        $grade = array();
        $output = array();
        for ($i=0; $i < sizeof($wjs); $i++) { 
            $cal_array = level_and_rate_cal($wjs[$i]["medium"],$wjs[$i]["pressure"],$wjs[$i]["temperature"],$wjs[$i]["pressure_test"],$wjs[$i]["ac"],$wjs[$i]["at"],$wjs[$i]["ath"],$wjs[$i]["bc"],$wjs[$i]["bt"],$wjs[$i]["bth"],$wjs[$i]["jtype"],$grade);
            if ($cal_array["level"] != $wjs[$i]["level"] || $cal_array["RT"] != $wjs[$i]["RT"] || $cal_array["UT"] != $wjs[$i]["UT"] || $cal_array["PT"] != $wjs[$i]["PT"] || $cal_array["MT"] != $wjs[$i]["MT"] || $cal_array["SA"] != $wjs[$i]["SA"] || $cal_array["HB"] != $wjs[$i]["HB"]) {
                $output[] = array_merge(array("<button class=\"btn btn-default btn-small\" onclick=\"dt_alt_info('wj',".$wjs[$i]["id"].")\">变更</button>",$wjs[$i]["id"],$wjs[$i]["vcode"],$wjs[$i]["exam_specify"],$wjs[$i]["level"],$wjs[$i]["RT"],$wjs[$i]["UT"],$wjs[$i]["PT"],$wjs[$i]["MT"],$wjs[$i]["SA"],$wjs[$i]["HB"]),$cal_array);
            }
        }
        $pview = new \datatables("layouts/panel_table",$output);
        $pview->info("current_nav","<a href=\"/home\">个人工作台</a> -> <a href=\"/panel/wj_rate_check\">检验比例检查</a>");
        $pview->title(array("操作","ID","焊口号","指定检验","级别","RT","UT","PT","MT","SA","HB","计算级别","计算RT","计算UT","计算PT","计算MT","计算SA","计算HB"));
        return $pview;
    }

    function wj_rate_check_super(){
        $rate_list = \App\sys_check_list::where("check_name","wj_rate_check")->get();
        if (sizeof($rate_list) == 0) {
            $wj_ids = array();
        } else {
            $wj_ids = multiple_to_array($rate_list[0]->check_ids);
        }
        $wjs = DB::table("wj")->where("deleted_at","2037-12-31")->where("tsk_id",">",0)->where("exam_specify",0)->whereIn("id",$wj_ids)->get()->toArray();
        $grade = array();
        $output = array();
        for ($i=0; $i < sizeof($wjs); $i++) { 
            $cal_array = level_and_rate_cal($wjs[$i]["medium"],$wjs[$i]["pressure"],$wjs[$i]["temperature"],$wjs[$i]["pressure_test"],$wjs[$i]["ac"],$wjs[$i]["at"],$wjs[$i]["ath"],$wjs[$i]["bc"],$wjs[$i]["bt"],$wjs[$i]["bth"],$wjs[$i]["jtype"],$grade);

            $rc = 0;//是否显示记录的标志

            if (isset($_GET["lower"])) {
                if ($cal_array["RT"] > $wjs[$i]["RT"] || $cal_array["UT"] > $wjs[$i]["UT"] || $cal_array["PT"] > $wjs[$i]["PT"] || $cal_array["MT"] > $wjs[$i]["MT"] || $cal_array["SA"] > $wjs[$i]["SA"] || $cal_array["HB"] > $wjs[$i]["HB"]) {
                    $rc = 1;
                }
            } else {
                if ($cal_array["level"] != $wjs[$i]["level"] || $cal_array["RT"] != $wjs[$i]["RT"] || $cal_array["UT"] != $wjs[$i]["UT"] || $cal_array["PT"] != $wjs[$i]["PT"] || $cal_array["MT"] != $wjs[$i]["MT"] || $cal_array["SA"] != $wjs[$i]["SA"] || $cal_array["HB"] != $wjs[$i]["HB"]) {
                    $rc = 1;
                }
            }
            if ($rc == 1) {
                $output[] = array_merge(array(
                    "<button class=\"btn btn-info btn-small\" onclick=\"table_flavr('/wj/wj_detail?id=".$wjs[$i]["id"]."')\">查看</button><button class=\"btn btn-default btn-small\" onclick=\"dt_alt_info('wj',".$wjs[$i]["id"].")\">变更</button>",
                    $wjs[$i]["id"],$wjs[$i]["vcode"],$wjs[$i]["exam_specify"],$wjs[$i]["level"],$wjs[$i]["RT"],$wjs[$i]["UT"],$wjs[$i]["PT"],$wjs[$i]["MT"],$wjs[$i]["SA"],$wjs[$i]["HB"]
                ),$cal_array);
            }
            
        }
        $pview = new \datatables("panel/wj_rate_check_super",$output);
        $pview->info("current_nav","<a href=\"/home\">个人工作台</a> -> <a href=\"/panel/wj_rate_check_super\">检验比例检查(S)</a>");
        $pview->title(array("操作","ID","焊口号","指定检验","级别","RT","UT","PT","MT","SA","HB","计算级别","计算RT","计算UT","计算PT","计算MT","计算SA","计算HB"));
        return $pview;
    }


    function wj_exec_cal(){
        if (isset($_POST["plan_cal"]) || isset($_POST["plan_cal_update"])) {
            //设置步进值
            $step = 100;
            //先处理未更新过结果的组
            if (isset($_POST["plan_cal"])) {
                $exam_plans = DB::table("exam_plan")->where("deleted_at","2037-12-31")->whereNull("ep_result_time");
            } else if ($_POST["plan_cal_update"]){
                $exam_plans = DB::table("exam_plan")->leftJoin("exam","exam.exam_plan_id","exam_plan.id")->where("exam_plan.deleted_at","2037-12-31")->whereNotNull("exam.updated_at")->whereRaw("exam_plan.ep_result_time < exam.updated_at");
            }
            if (!isset($_POST["total"])) {
                if (isset($_POST["plan_cal"])) {
                    $total = $exam_plans->count();
                } else if ($_POST["plan_cal_update"]){
                    $total = $exam_plans->count(DB::raw("DISTINCT(exam_plan.id)"));
                }
            } else {
                $total = $_POST["total"];
            }
            $exam_plans->limit($step);
            //循环写入结果
            foreach ($exam_plans->cursor() as $exam_plan) {
                $accept = "";
                $unaccept = "";
                $ep_proc = "";
                $ep_result = "";
                //获取第一次抽批的检测结果
                $all = 0;
                $finish = 0;
                $result = "N/A";
                $exams = DB::table("exam")->where("exam_plan_id",$exam_plan["id"])->whereIn("exam_wj_id",multiple_to_array($exam_plan["ep_wj_samples"]));
                foreach ($exams->cursor() as $exam) {
                    $all++;
                    if ($exam["exam_input_time"] != null) {
                        $finish++;
                        if ($exam["exam_conclusion"] == "不合格") {
                            $result = "不合格";
                            $unaccept .= "{".$exam["id"]."}";
                        } else {
                            $accept .= "{".$exam["id"]."}";
                        }
                    }
                }
                if ($all == $finish) {
                    $proc = "已完成";
                    if ($result == "N/A") {
                        $result = "合格";
                    }
                } else {
                    $proc = $finish."/".$all;
                }
                $ep_wj_samples_proc = $proc;
                $ep_wj_samples_result = $result;

                //更新总结果
                $ep_proc = $proc;
                $ep_result = $result;

                if (strlen($exam_plan["ep_wj_addition_samples"]) > 0) {
                    //获取加倍抽批的检测结果
                    $all = 0;
                    $finish = 0;
                    $result = "N/A";
                    $exams = DB::table("exam")->where("exam_plan_id",$exam_plan["id"])->whereIn("exam_wj_id",multiple_to_array($exam_plan["ep_wj_addition_samples"]));
                    foreach ($exams->cursor() as $exam) {
                        $all++;
                        if ($exam["exam_input_time"] != null) {
                            $finish++;
                            if ($exam["exam_conclusion"] == "不合格") {
                                $result = "不合格";
                                $unaccept .= "{".$exam["id"]."}";
                            } else {
                                $accept .= "{".$exam["id"]."}";
                            }
                        }
                    }
                    if ($all == $finish) {
                        $proc = "已完成";
                        if ($result == "N/A") {
                            $result = "加倍合格";
                        }
                    } else {
                        $proc = $finish."/".$all;
                    }
                    $ep_wj_addition_samples_proc = $proc;
                    $ep_wj_addition_samples_result = $result;
                } else {
                    $ep_wj_addition_samples_proc = "N/A";
                    $ep_wj_addition_samples_result = "N/A";
                }

                //更新总结果
                if ($ep_wj_addition_samples_proc != "N/A") {
                    $ep_proc = $ep_wj_addition_samples_proc;
                    $ep_result = $ep_wj_addition_samples_result;
                }
                
                

                if (strlen($exam_plan["ep_wj_another_samples"]) > 0) {
                    //获取全部抽批的检测结果
                    $all = 0;
                    $finish = 0;
                    $result = "N/A";
                    $exams = DB::table("exam")->where("exam_plan_id",$exam_plan["id"])->whereIn("exam_wj_id",multiple_to_array($exam_plan["ep_wj_another_samples"]));
                    foreach ($exams->cursor() as $exam) {
                        $all++;
                        if ($exam["exam_input_time"] != null) {
                            $finish++;
                            if ($exam["exam_conclusion"] == "不合格") {
                                $result = "不合格";
                                $unaccept .= "{".$exam["id"]."}";
                            } else {
                                $accept .= "{".$exam["id"]."}";
                            }
                        }
                    }
                    if ($all == $finish) {
                        $proc = "已完成";
                        if ($result == "N/A") {
                            $result = "合格";
                        }
                    } else {
                        $proc = $finish."/".$all;
                    }
                    $ep_wj_another_samples_proc = $proc;
                    $ep_wj_another_samples_result = $result;
                } else {
                    $ep_wj_another_samples_proc = "N/A";
                    $ep_wj_another_samples_result = "N/A";
                }

                //更新总结果
                if ($ep_wj_another_samples_proc != "N/A") {
                    $ep_proc = $ep_wj_another_samples_proc;
                    $ep_result = "全检";
                }

                //proc有三种状态：N/A（不需检验）、*/*（完成比例）、已完成
                //result有三种状态：N/A（没有结果）、合格、全检

                DB::table("exam_plan")->where("deleted_at","2037-12-31")->where("id",$exam_plan["id"])->update(array("ep_accept" => $accept, "ep_unaccept" => $unaccept, "ep_wj_samples_proc" => $ep_wj_samples_proc, "ep_wj_addition_samples_proc" => $ep_wj_addition_samples_proc, "ep_wj_another_samples_proc" => $ep_wj_another_samples_proc, "ep_wj_samples_result" => $ep_wj_samples_result, "ep_wj_addition_samples_result" => $ep_wj_addition_samples_result, "ep_wj_another_samples_result" => $ep_wj_another_samples_result, "ep_proc" => $ep_proc, "ep_result" => $ep_result,"ep_result_time" => \Carbon\Carbon::now()));
                //清除相应的焊口的更新状态，以便后续更新
                /*DB::table("wj")->where("deleted_at","2037-12-31")->where(function($query) use ($exam_plan){
                    $query->orWhere("RT_plan","like","%{".$exam_plan["id"]."}%");
                    $query->orWhere("UT_plan","like","%{".$exam_plan["id"]."}%");
                    $query->orWhere("PT_plan","like","%{".$exam_plan["id"]."}%");
                    $query->orWhere("MT_plan","like","%{".$exam_plan["id"]."}%");
                    $query->orWhere("SA_plan","like","%{".$exam_plan["id"]."}%");
                    $query->orWhere("HB_plan","like","%{".$exam_plan["id"]."}%");
                })->update(array("exam_result_time" => null));*/
                DB::table("wj")->where("deleted_at","2037-12-31")->whereIn("id",multiple_to_array($exam_plan["ep_wj_samples"]))->update(array("exam_result_time" => null));
            }

            $r = array(
                "total" => $total,
                "processing" => isset($_POST["processing"])?(intval($_POST["processing"])+$step):$step,
                "suc" => 1
            );
            echo json_encode($r);

        } else if (isset($_POST["wj_cal"])){

            $wj_model = new \App\wj();

            //设置步进值
            $step = 100;
            //先处理未更新过结果的组
            if (isset($_POST["wj_cal"])) {
                $wjs = DB::table("wj")->where("deleted_at","2037-12-31")->whereNotNull("wj_tsk_finish_date")->whereNull("exam_result_time");
            }
            if (!isset($_POST["total"])) {

                //先同步完工日期
                DB::table("wj")->leftJoin("tsk","tsk.id","wj.tsk_id")->where("wj.deleted_at","2037-12-31")->where("tsk.deleted_at","2037-12-31")->whereNotNull("tsk.tsk_finish_date")->whereNull("wj.wj_tsk_finish_date")->update(array("wj_tsk_finish_date" => DB::raw("tsk_finish_date")));

                //计算总数
                $total = $wjs->count();

            } else {
                $total = $_POST["total"];
            }
            $wjs->limit($step);
            //循环写入结果
            foreach ($wjs->cursor() as $wj) {
                $wj_model->update_result($wj["id"]);
            }

            $r = array(
                "total" => $total,
                "processing" => isset($_POST["processing"])?(intval($_POST["processing"])+$step):$step,
                "suc" => 1
            );
            echo json_encode($r);

        } else {
            die("数据错误");
        }
    }

    function user_auth(){
        if (isset($_GET["id"])) {
            $pview = new \view("panel/user_auth",["id" => $_GET["id"]]);
            return $pview;
        } else {
            return "用户信息错误";
        }
    }

    function create(){
        echo "create";
    }


    function edit(){
        echo "edit";
    }


    function destroy(){
        echo "destroy";
    }

    //（POST）用户权限修改
    function user_auth_post(){
        if (isset($_POST["id"]) && isset($_POST["auth"])) {
            $user = \App\user::find($_POST["id"]);
            $user->auth = $_POST["auth"];
            if ((Auth::user()->user_level > $user->user_level || Auth::user()->user_level == 9) && $user->save()) {
                $r = array(
                        "suc" => 1,
                        "msg" => "操作成功"
                    );
                die(json_encode($r));
            } else {
                die("写入失败");
            }
        } else {
            die("数据错误");
        }
    }

    //（POST）用户密码修改
    function change_password_exec(){
        if (valid_post("old","new","confirm")) {
            $user = \App\User::find(Auth::user()->id);
            if (\Hash::check($_POST["old"], $user->password)) {
                if($_POST["new"] == $_POST["confirm"]){
                    $user->password = bcrypt($_POST["new"]);
                    if ($user->save()) {
                        $r = array(
                            "suc" => 1,
                            "msg" => "操作成功"
                        );
                        die(json_encode($r));
                    } else {
                        die("写入失败");
                    }
                } else {
                    die("两次输入新密码不一致");
                }
            } else {
                die("原密码错误");
            }
        } else {
            die("数据错误");
        }
    }

    //(POST)重置密码
    function reset_pwd(){
        if (isset($_POST["id"])) {
            $user = \App\User::find($_POST["id"]);
            if (isset($_POST["ch"]) || strlen($user->default_key) == 0) {
                $user->default_key = substr(md5(time().rand(0,9)),0,6);
            }
            $user->password = bcrypt($user->default_key);
            if ((Auth::user()->user_level <= $user->user_level && Auth::user()->user_level != 9) || !$user->save()) {
                $r = array(
                        "suc" => -1,
                        "msg" => "修改失败"
                    );
                echo json_encode($r);
            } else {
                $r = array(
                        "suc" => 1,
                        "msg" => "修改成功"
                    );
                echo json_encode($r);
            }
        } else {
            die("数据错误");
        }
    }

    //(POST)检验比例检查
    function wj_rate_check_super_post(){
        if (isset($_POST["max"]) && isset($_POST["min"])) {
            $wjs = DB::table("wj")->where("deleted_at","2037-12-31")->where("tsk_id",">",0)->where("exam_specify",0)->where("id",">=",isset($_POST["min"])?intval($_POST["min"]):0)->where("id","<=",isset($_POST["max"])?intval($_POST["max"]):5000)->get()->toArray();
            $ids = "";
            $grade = array();
            $output = array();
            for ($i=0; $i < sizeof($wjs); $i++) { 
                $cal_array = level_and_rate_cal($wjs[$i]["medium"],$wjs[$i]["pressure"],$wjs[$i]["temperature"],$wjs[$i]["pressure_test"],$wjs[$i]["ac"],$wjs[$i]["at"],$wjs[$i]["ath"],$wjs[$i]["bc"],$wjs[$i]["bt"],$wjs[$i]["bth"],$wjs[$i]["jtype"],$grade);
                if ($cal_array["level"] != $wjs[$i]["level"] || $cal_array["RT"] != $wjs[$i]["RT"] || $cal_array["UT"] != $wjs[$i]["UT"] || $cal_array["PT"] != $wjs[$i]["PT"] || $cal_array["MT"] != $wjs[$i]["MT"] || $cal_array["SA"] != $wjs[$i]["SA"] || $cal_array["HB"] != $wjs[$i]["HB"]) {
                    $ids .= "{".$wjs[$i]["id"]."}";
                }
            }
            $sys_check_list = new \App\sys_check_list();
            $collection = $sys_check_list->where("check_name","wj_rate_check")->get();
            if (sizeof($collection) == 0) {
                $rate_list = $sys_check_list;
                $rate_list->check_name = "wj_rate_check";
            } else {
                $rate_list = $collection[0];
            }
            if (isset($_POST["init"])) {
                $rate_list->check_ids = $ids;
            } else {
                $rate_list->check_ids .= $ids;
            }
            if ($rate_list->save()) {
                $r = array("suc" => 1, "msg" => "成功", "current" => $_POST["max"]);
                echo json_encode($r);
            } else {
                $r = array("suc" => -1, "msg" => "失败");
                echo json_encode($r);
            }
        } else {
            die("数据错误");
        }
    }

    function level_and_org_post(){
        if (valid_post("id","level","org")) {
            $user = \App\user::find($_POST["id"]);
            if (((Auth::user()->user_level > $user->user_level && Auth::user()->user_level > $_POST["level"]) || Auth::user()->user_level == 9)) {
                $user->user_level = $_POST["level"];
                $user->user_org = $_POST["org"];
                if ($user->save()) {
                    $r = array(
                            "suc" => 1,
                            "msg" => "操作成功"
                        );
                    die(json_encode($r));
                } else {
                    die("写入失败");
                }
            } else {
                die("您没有权限");
            }
        } else {
            die("数据错误");
        }
    }

}
