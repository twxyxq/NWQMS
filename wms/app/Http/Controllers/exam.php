<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use datatables;
use view;

class exam extends Controller
{
   
	//检验任务列表
    function tsk_list(){
        $model = new \App\exam();
        $sview = new datatables("layouts/panel_table","exam@exam_list",$_GET["emethod"]);
        $sview->title(array("操作","焊口号","方法","比例","委托单","焊工","委托人","委托日期"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //工艺模板设置
    function eps_setting(){
    	return $this->e_setting("eps",array("关联报告","仅工艺卡"),30,"工艺卡结构","“字段名”代表工艺卡显示的参数名称；“有效性”为(null)或者“作废”，该字段无效；“已有值”代表该字段已有录入值，如无，则可删除该字段，选“关联报告”则自动代入报告");
    }


    //结果格式设置
    function record_setting(){
        return $this->e_setting("exam_item",array("有效"),20,"结果格式","“字段名”代表检验结果字段名称；“有效性”为(null)或者“作废”，该字段无效；“已有值”代表该字段已有录入值，如无，则可删除该字段，选“关联报告”则自动代入报告");
    }

    //检验额外参数格式设置
    function exam_setting(){
        return $this->e_setting("exam",array("有效"),20,"额外参数格式","“字段名”代表额外参数名称；“有效性”为(null)或者“作废”，该字段无效；“已有值”代表该字段已有录入值，如无，则可删除该字段，选“关联报告”则自动代入报告");
    }


    function e_setting($model_name,$status=array(),$limit=0,$title="",$introduction=""){

    	$ei_model = array("");
    	$ei_status = array("");

    	$model_text = "\\App\\".$model_name;
    	$col_name = $model_name."_method";

    	if (isset($_GET["method"])) {
    		$ei_model = $model_text::where($col_name,$_GET["method"]."_MODEL")->get();
    		if (sizeof($ei_model) == 0) {
    			$ei = new $model_text();
    			$ei->$col_name = $_GET["method"]."_MODEL";
    			$ei->save();
    			$ei_model = $model_text::where($model_name."_method",$_GET["method"]."_MODEL")->get();
    		}
    		$ei_status = $model_text::where($col_name,$_GET["method"]."_STATUS")->get();
    		if (sizeof($ei_status) == 0) {
    			$ei = new $model_text();
    			$ei->$col_name = $_GET["method"]."_STATUS";
    			$ei->save();
    			$ei_status = $model_text::where($model_name."_method",$_GET["method"]."_STATUS")->get();
    		}
    	}


    	$sview = new view("exam/e_setting",["e_model" => $ei_model[0], "e_status" => $ei_status[0], "model_name" => $model_name, "status" => $status, "title" => $title, "introduction" => $introduction, "limit" => $limit]);
        //$sview->title(array("操作","焊口号","方法","比例","委托单","焊工","委托人","委托日期"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //工艺录入
    function eps(){
        $model = new \App\eps();
        $model->method_select($_GET["emethod"]);
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","eps@eps_list",$_GET["emethod"]);
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }


    //结果草稿
    function draft(){
        $model = new \App\exam();
        $model->method_select($_GET["emethod"]);
        $sview = new datatables("layouts/panel_table","exam@exam_draft",$_GET["emethod"]);
        $sview->title($model->titles_init(array("操作","焊口","委托单")));
        return $sview;
    }
    //检验结果
    function record(){
        $model = new \App\exam();
        $model->method_select($_GET["emethod"]);
        $sview = new datatables("layouts/panel_table","exam@exam_record",$_GET["emethod"]);
        $sview->title($model->titles_init(array("操作","焊口","委托单","检验报告"),array("结果","结论","时间")));
        return $sview;
    }
    //报告出版
    function report_create(){
        $model = new \App\exam();
        $model->method_select($_GET["emethod"]);
        $sview = new datatables("exam/report_create","exam@exam_report_create",$_GET["emethod"]);
        $sview->title($model->titles_init(array("操作","焊口","委托单","工艺")));
        return $sview;
    }
    //检验报告
    function report(){
        $model = new \App\exam_report();
        //$model->method_select($_GET["emethod"]);
        $sview = new datatables("layouts/panel_table","exam_report@report_list",$_GET["emethod"]);
        $sview->title($model->titles_init(array("操作")));
        return $sview;
    }

    //报告详情
    function report_detail(){

        $report = new \stdClass();
        


        $info = array();

        $result = array();

        $wj = array();

        //获取已生成的报告
        if (isset($_GET["report_id"])) {
           
            $report = \App\exam_report::find($_GET["report_id"]);

            $exam_group = \App\exam::where("exam_report_id",$_GET["report_id"])->get();

            $exam = $exam_group[0];

            $exam_sheet = \App\exam_sheet::find($exam->exam_sheet_id);

            $report->es_code = $exam_sheet->es_code;
            $report->ild_sys = $exam_sheet->es_ild_sys;


        } else if (isset($_GET["exam_id"])) {

            $exam_ids = explode(",",$_GET["exam_id"]);

            if (sizeof($exam_ids) > 1) {
                $exam_group = \App\exam::find($exam_ids);
                $exam = $exam_group[0];
            } else {
                $exam = \App\exam::find($_GET["exam_id"]);
                $exam_group = array($exam);
            }

            //出版报告权限控制，无权限不能进入页面
            if (!$exam->valid_updating()) {
                //return "您没有权限出版报告";
            }

            $report->exam_report_method = $exam->exam_method;

            if ($exam->exam_report_id > 0) {
                die("报告已出");
            }

            $exam_sheet = \App\exam_sheet::find($exam->exam_sheet_id);

            $report->es_code = $exam_sheet->es_code;
            $report->ild_sys = $exam_sheet->es_ild_sys;

            //报告出版时使用
            if (isset($_GET["report_create"])) {
                $report->create = 1;//报告出版标识
                $report->exam_report_date = \Carbon\Carbon::now()->toDateString();
                //生成报告号
                $report->exam_report_code = $exam_sheet->es_code;
                if(sizeof(\App\exam::whereNotIn("id",$exam_ids)->where("exam_sheet_id",$exam_sheet->id)->get()) > 0){
                    $report->exam_report_code .= "-".\App\exam_report::select(DB::raw("IF(MAX(RIGHT(exam_report_code,2)),LPAD(MAX(RIGHT(exam_report_code,2))+1,2,0),'01') as code"))->where("exam_report_code","like",$exam_sheet->es_code."-%")->where("exam_report_method",$report->exam_report_method)->get()[0]->code;
                }
                //加入INPUT
                $report->exam_report_code = "<input type=\"hidden\" name=\"exam_report_code\" value=\"".$report->exam_report_code."\">".$report->exam_report_code;
                $report->exam_report_date = "<input type=\"hidden\" name=\"exam_report_date\" value=\"".$report->exam_report_date."\">".$report->exam_report_date;

            }


            if ($exam->exam_input_time == null || isset($_GET["edit"])) {
                $report->exam_input = 1;//结果录入标识，用于显示输入框等信息
            }
            

        } else if (isset($_GET["method"])) {

            $report->exam_report_method = $_GET["method"];
            
        }



        if (isset($_GET["report_id"]) || isset($_GET["exam_id"])) {

            if ($exam->exam_eps_id > 0) {
                $eps = \App\eps::find($exam->exam_eps_id);
            }

           
            //获取结果模板
            $exam_item_model = \App\exam_item::where("exam_item_method",$report->exam_report_method."_MODEL")->get();
            $exam_item_status = \App\exam_item::where("exam_item_method",$report->exam_report_method."_STATUS")->get();
            $result_model = array();
            $result_col = array();
            for ($i=0; $i < 20; $i++) {
                $index = "exam_item_info_".$i;
                if ($exam_item_status[0]->$index == "有效") {
                    $result_model[] = $exam_item_model[0]->$index;
                    $result_col[] = $index;
                }
            }
            $result_col[] = "exam_item_conclusion";
           
            //构建result
            if($report->exam_report_method != "RT"){
                array_unshift($result_model,"规格");
            }
            array_unshift($result_model,"序号","焊口编号","焊工代号");
            $result_model[] = "结论";
            $result[] = $result_model;
            //检验结果信息(array)
            foreach ($exam_group as $exam_single) {
                $exam_item = \App\exam_item::select($result_col)->where("exam_item_exam_id",$exam_single->id)->get()->toArray();
                 //焊口及任务信息
                $wj = \App\wj::select(DB::raw("*"),DB::raw(SQL_BASE_C." as wj_c"),DB::raw(SQL_BASE_TYPE." as wj_type"))->where("id",$exam_single->exam_wj_id)->get()[0];
                $tsk = \App\tsk::find($wj->tsk_id);
                for ($i = 0; $i < sizeof($exam_item); $i++) {
                    $temp_array = array_values($exam_item[$i]);
                    if($report->exam_report_method != "RT"){
                        array_unshift($temp_array,$wj->wj_type);
                    }
                    array_unshift($temp_array,$i+1,$wj->vcode,$tsk->tsk_pp_show);
                    $result[] = $temp_array;
                }
            }
            //获取最后一个焊口的信息，按照正常流程，所有焊口的这些信息是一样的
            $report->c = $wj->wj_c;
            $report->type = $wj->wj_type;
            $report->jtype = $wj->jtype;
            $report->weld_method = $tsk->tsk_wmethod;

            //当处于录入状态时，result显示为点击录入
            if (isset($report->exam_input)) {
                foreach ($result as $r_key => $r) {
                    foreach ($r as $r_item_key => $r_item) {
                        $result[$r_key][$r_item_key] = "<a href=\"###\" onclick=\"parent.new_flavr('/exam/exam_item_detail?exam_id=".$_GET["exam_id"]."','','',function(){location.reload();})\" style=\"color:green\">".$r_item."</a>";;
                    }
                }
            }
        }



        $eps_model = \App\eps::where("eps_method",$report->exam_report_method."_MODEL")->get();
        $eps_status = \App\eps::where("eps_method",$report->exam_report_method."_STATUS")->get();


        for ($i=0; $i < 30; $i++) {
            $index = "eps_info_".$i; 
            if ($eps_status[0]->$index == "关联报告") {
                $data = isset($eps->$index)?$eps->$index:(isset($report->exam_input)?"请选择工艺卡":"");
                $info[] = array($eps_model[0]->$index,isset($report->exam_input)?"<a href=\"###\" onclick=\"new_flavr('/exam/eps_select?exam_id=".$exam->id."','',{
                        success   : { text: '确认', style: 'success',
                                    action: function(){
                                        return eps_select();
                                    }},
                        close   : { text: '关闭' }
                    },function(){
                        location.reload();
                    })\">".$data."</a>":$data);
            }
        }

        $exam_model = \App\exam::where("exam_method",$report->exam_report_method."_MODEL")->get();
        $exam_status = \App\exam::where("exam_method",$report->exam_report_method."_STATUS")->get();

        for ($i=0; $i < 20; $i++) {
            $index = "exam_info_".$i; 
            if ($exam_status[0]->$index == "有效") {
                $data = isset($exam->$index)?$exam->$index:"";
                if (isset($report->exam_input)) {
                    $data = "<a href=\"###\" onclick=\"new_flavr('/console/dt_edit?auth=para_input&model=exam&id=".$_GET["exam_id"]."&para=".$report->exam_report_method."','','',function(){location.reload();})\" style=\"color:blue\">".(strlen($data)>0?$data:"请输入")."</a>";
                }

                $info[] = array($exam_model[0]->$index,$data);
            }
        }
        
        $sheet_view = new view("sheet/report_sheet",["report" => $report, "info" => $info, "result" => $result, "wj" => $wj]);

        $sview = new view("exam/report_detail",["sheet" => $sheet_view->render()]);
        return $sview;

    }

    //单个焊口结果录入
    function exam_item_detail(){
        $model = new \App\exam_item();
        $model->id_select($_GET["exam_id"]);
        $input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/page_table_detail","exam_item@exam_item_list",$_GET["exam_id"]);
        $sview->title($model->titles_init("操作",array("创建者","时间")));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //工艺卡选择
    function eps_select(){
        $exam = \App\exam::find($_GET["exam_id"]);
        $eps = new \App\eps();
        $eps->method_select($exam->exam_method);
        $sview = new datatables("layouts/table_select",["no_output" => 1],"eps@eps_select",$exam->exam_method);
        $sview->title($eps->titles_init(7,"选择"));
        $sview->option("searching: false");
        $sview->option("info: false");
        $sview->option("length: 5");
        $sview->option("lengthChange: false");
        return $sview;
    }

    //工艺卡选择写人(POST)
    function eps_select_post(){
        if (isset($_POST["exam_id"]) && isset($_POST["exam_eps_id"])) {
            $exam = \App\exam::find($_POST["exam_id"]);
            if(!$exam->select_eps($_POST["exam_eps_id"])){
                die($exam->msg);
            } else {
                $r = array(
                    "suc" => 1,
                    "msg" => $exam->msg
                );
                die(json_encode($r));
            }
        } else {
            echo "数据错误";
        }
    }


    //(POST)工艺卡结构修改
    function e_structure(){
    	if (isset($_POST["model_name"]) && isset($_POST["limit"]) && isset($_POST["model_id"]) && isset($_POST["status_id"])) {
    		$e_name = "\\App\\".$_POST["model_name"];
    		$e = new $e_name();
    		$e->authorize_user(Auth::user()->id);//用户授权（目前允许所有人修改）
    		$e_model = $e->onlySoftDeletes()->find($_POST["model_id"]);
    		$e_status = $e->onlySoftDeletes()->find($_POST["status_id"]);
    		for ($i=0; $i < $_POST["limit"]; $i++) {
    			$index = $_POST["model_name"]."_info_".$i;
    			$e_status->$index = $_POST["status_info_".$i];
    			$e_model->$index = $_POST["model_info_".$i];
    		}
    		if ($e_status->save() && $e_model->save()) {
    			$r = array(
                    "suc" => 1,
                    "msg" => "操作成功"
                );
                die(json_encode($r));
    		} else {
    			$r = array(
                    "suc" => -1,
                    "msg" => "操作失败"
                );
                die(json_encode($r));
    		}

    	} else {
    		echo "数据错误";
    	}
    }

    //(POST)结果确认
    function exam_confirm_post(){
        if (isset($_POST["exam_id"])) {
            $exam = new \App\exam();
            if ($exam->exam_confirm($_POST["exam_id"])) {
                $r = array(
                    "suc" => 1,
                    "msg" => "操作成功"
                );
                die(json_encode($r));
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => $exam->msg
                );
                die(json_encode($r));
            }
            
        } else {
            echo "数据错误";
        }
    }


    //(POST)报告出版
    function report_create_post(){
        if (isset($_POST["exam_ids"])) {
            $exam_model = new \App\exam();
            //确认焊口存在，提取检测方法
            $method_collection = $exam_model->select(DB::raw("DISTINCT(exam_method) as emethod"))->whereIn("id",$_POST["exam_ids"])->get();
            if (sizeof($method_collection) == 0){
                die("没有找到焊口");
            } else if (sizeof($method_collection) > 1){
                die("检验方法不一致");
            }
            //方法确认，已选择额外参数模板
            $exam_model->method_select($method_collection[0]->emethod);
            //检测是否可以一起出版
            $collection = \App\exam::select(DB::raw("DISTINCT(CONCAT(exam_eps_id,exam_sheet_id,".array_to_string($exam_model->items_init())."))"))->whereIn("id",$_POST["exam_ids"])->get();
            if (sizeof($collection) > 1) {
                die("工艺卡、委托单、额外数据不一致，不能一起出版");
            }
            //获取委托单信息，生成报告号
            //$exam_sheet = \App\exam_sheet::find($collection->exam_sheet_id);

            //$exam_report = new \App\exam_report();
            //$exam_report->
            $r = array(
                "suc" => 1,
                "msg" => "检测通过"
            );
            die(json_encode($r));
            
        } else {
            echo "数据错误";
        }
    }


    //(POST)报告出版
    function report_confirm_post(){
        if (isset($_POST["exam_id_text"]) && isset($_POST["exam_report_code"]) && isset($_POST["exam_report_date"])) {
            $exam_ids = explode(",",$_POST["exam_id_text"]);
            if (sizeof(\App\exam_report::where("exam_report_code",$_POST["exam_report_code"])->get()) > 0) {
                $r = array(
                    "suc" => 2,
                    "msg" => "报告已存在，请重新刷新页面"
                );
                die(json_encode($r));
            } else {

                $exam_model = new \App\exam();
                //确认焊口存在，提取检测方法
                $method_collection = $exam_model->select(DB::raw("DISTINCT(exam_method) as emethod"))->whereIn("id",$exam_ids)->get();
                if (sizeof($method_collection) == 0){
                    die("没有找到焊口");
                } else if (sizeof($method_collection) > 1){
                    die("检验方法不一致");
                }
                //方法确认，已选择额外参数模板
                $exam_model->method_select($method_collection[0]->emethod);
                //检测是否可以一起出版
                $collection = \App\exam::select(DB::raw("DISTINCT(CONCAT(exam_eps_id,exam_sheet_id,".array_to_string($exam_model->items_init())."))"))->whereIn("id",$exam_ids)->get();
                if (sizeof($collection) > 1) {
                    die("工艺卡、委托单、额外数据不一致，不能一起出版");
                }

                $exam_report = new \App\exam_report();
                $exam_report->exam_report_method = $method_collection[0]->emethod;
                $exam_report->exam_report_code = $_POST["exam_report_code"];
                $exam_report->exam_report_date = $_POST["exam_report_date"];
                $exam_report->exam_report_exam_ids = array_to_multiple($exam_ids);

                DB::transaction(function() use ($exam_report,$exam_model,$exam_ids) {

                    if (!$exam_report->save()) {
                        die($exam_report->msg);
                    }

                    foreach ($exam_ids as $id) {
                        
                        $exam = $exam_model->find($id);

                        $exam->exam_report_id = $exam_report->id;

                        if (!$exam->save()) {
                            die($exam->msg);
                        }

                    }

                    

                });

                $r = array(
                    "suc" => 1,
                    "msg" => "报告已生成",
                    "report_id" => $exam_report->id
                );
                die(json_encode($r));
            }
        } else {
            echo "数据错误";
        }
    }



}
