<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\wj_model;
use App\wj_base_model;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Input;

use datatables;
use view;

class wj extends Controller
{
   
    //焊缝清单
    function wj_list(){
        $model = new \App\wj();
        $sview = new datatables("layouts/panel_table",["width" => "3000px"],"wj@wj_list");
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        $sview->order(34,"desc");
        return $sview;
    }
    //焊缝执行情况清单
    function wj_exec_list(){
        $model = new \App\wj();
        $sview = new datatables("layouts/panel_table",["width" => "2000px"],"wj@wj_exec_list");
        $sview->title(array("操作","焊口号","规格","任务日期","焊工","完成日期","RT分组","UT分组","PT分组","MT分组","SA分组","HB分组"));
        return $sview;
    }

    function wj_list_data(){
        echo $_GET["db"];
    }

    function wj_del(){
        $ee = new wj_base_model();
        $ee->find(6)->delete();
    }

    function excel_input(){
        $sview = new view("wj/excel_input");
        return $sview;
    }

    function store(){
        if (isset($_POST["excelinput"])) {
                $file = Input::file('excelfile');
            //$new_file = public_path('uploads/excel/'.date("y-m-d-H-i-s").'.xls');
            //move_uploaded_file(base_path(substr($file->getRealPath(),1)),$new_file);
            //$file = $request->file('excelfile');
                $file = $file->move(public_path("uploads/excel"),date("y-m-d-H-i-s")."-".Auth::user()->id.".xls");
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                $objPHPExcel = $objReader->load($file); 
                $sheet = $objPHPExcel->getSheet(0); 
                $highestRow = $sheet->getHighestRow();           //取得总行数 
                $highestColumn = $sheet->getHighestColumn(); //取得总列数

                if ($highestRow < 3) {
                    die("<script>alert('行数不足');location.href='/wj/excel_input'</script>");
                }

                //插入的item数组
                $wj_base = new \App\wj_base();
                $insert_array = $wj_base->items_init();
                array_shift($insert_array);

                $objWorksheet = $objPHPExcel->getActiveSheet();
                //$highestRow = $objWorksheet->getHighestRow(); location.href='excelinput.php';
                if($objWorksheet->getCellByColumnAndRow(sizeof($insert_array), 2)->getValue() != "END"){
                    die("<script>alert('使用模板不正确！请重新导入！(".$objWorksheet->getCellByColumnAndRow(sizeof($insert_array), 2)->getValue().")');location.href='/wj/excel_input'</script>");
                }
                echo '总行数'.$highestRow;
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
                echo '总列数'.$highestColumn." ".$highestColumnIndex;

                $time = \Carbon\Carbon::now();

                $data = array();

                for ($row = 3; $row <= $highestRow; $row++){
                    $item = array();
                    for ($col = 0; $col < sizeof($insert_array); $col++){
                        $cell = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        if (strlen($cell) > 0){
                            ${$insert_array[$col]} = $cell;
                        } else {
                            if ($row == 3){
                                ${$insert_array[$col]} = "";
                            }
                            if ($insert_array[$col] == "vcode") {
                                $vcode = $ild.$sys."-".$pipeline."-".$vnum;
                            }
                            if ($insert_array[$col] == "bc") {
                                $bc= $ac;
                            }
                            if ($insert_array[$col] == "bt") {
                                $bt= $at;
                            }
                            if ($insert_array[$col] == "bth") {
                                $bth= $ath;
                            }
                        }
                        $item[$insert_array[$col]] = ${$insert_array[$col]};
                    }
                    $item["title"] = $time->toDateTimeString()."-".Auth::user()->code.Auth::user()->name;
                    $item["created_by"] = Auth::user()->id;
                    $item["created_at"] = $time;
                    $data[] = $item;
                }

                if ($num = DB::table("wj_base")->insert($data)) {
                    die("<script>alert('导入成功（".$num."）');location.href='/wj/import_check'</script>");
                } else {
                    die("<script>alert('导入失败（".$num."）');location.href='/wj/excel_input'</script>");
                }

            //$file->getClientOriginalName();
            //$data = Excel::load($file, function($reader) {

                //$reader->dump();

            //})->get();
            //dd($data);
        }
        //print_r($data);
    }

    function manual_input(){
        $wj = new \App\wj();

        $sview = new view("wj/manual_input",["wj" => $wj]);
        return $sview;
    }

    //焊口单个录入
    function wj_single_add(){
        $model = new \App\wj();
        $input_view = new view("form/ajax_form",["model" => $model, "lock" => array("wj_type" => "管道")]);
        $sview = new datatables("wj/wj_single_add",["width" => "3000px"],"wj@wj_add");
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //结构焊缝录入
    function wj_single_structure_add(){
        $model = new \App\wj();
        $model->item->ac->size(3);
        $model->item->at->size(3);
        $model->item->ath->size(3);
        $model->item->bc->size(3);
        $model->item->bt->size(3);
        $model->item->bth->size(3);
        $input_view = new view("form/ajax_form",["model" => $model, "lock" => array("wj_type" => "结构","exam_specify" => 1,"temperature" => 0, "pressure" => 0, "pressure_test" => 0, "upstream" => "N/A", "downstream" => "N/A", "medium" => "N/A"), "hidden" => array("pressure","temperature","medium","upstream","downstream","","pressure_test","level","exam_specify","exam_specify_reason")]);
        $sview = new datatables("wj/wj_single_structure_add",["width" => "3000px"],"wj@wj_structure_add");
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //单个录入审核
    function manual_check(){
        $model = new \App\wj();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("wj/manual_check","wj@manual_check");
        $sview->title($model->titles_init("操作",array("录入人","时间")));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //审核清单
    function check_list(){
        $model = new \App\wj();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","procedure@wj");
        $sview->title(array("操作","流程类型","焊口数","当前责任人","发起人","发起时间"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //审核完成清单
    function checked_list(){
        $model = new \App\wj();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","procedure@wj_checked");
        $sview->title(array("操作","流程类型","焊口数","发起人","发起时间","完成时间"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //审核完成清单
    function import_check(){
        $model = new \App\wj_base();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("layouts/panel_table","wj_base@wj_base_list");
        $sview->title(array("序号","名称","数量","状态"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }
    //导入清单审核
    function wj_base_check(){
        $model = new \App\wj_base();
        //$input_view = new view("form/ajax_form",["model" => $model]);
        $sview = new datatables("wj/wj_base_check",["width" => "2300px"],"wj_base@wj_base_item",$_GET["group"]);
        $sview->title($model->titles_init(array("验证","备注")));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //焊口详情
    function wj_detail(){
        $sview = new view("wj/wj_detail",["id" => $_GET["id"]]);
        return $sview;
    }

    //焊口返修
    function wj_r(){
        $model = new \App\wj();
        $sview = new datatables("layouts/panel_table",["width" => "3000px"],"wj@wj_r");
        $sview->title($model->titles_init(array("操作","检验结果")));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //返修清单
    function wj_r_list(){
        $model = new \App\wj();
        $sview = new datatables("layouts/panel_table",["width" => "3000px"],"wj@wj_r_list");
        $sview->title($model->titles_init("操作","检验结果"));
        //$sview->info("panel_body",$input_view->render());
        return $sview;
    }

    //焊口返修详情
    function wj_r_detail(){
        $model = new \App\wj();
        if (isset($_GET["para"]) && $_GET["para"] != "") {
            if (method_exists($model,$_GET["para"])) {
                $model->{$_GET["para"]}();
            } else if ($model->default_method !== false) {
                $use_method = $model->default_method;
                $model->$use_method($_GET["para"]);
            }
            
        }
        $collection = $model->onlySoftDeletes()->find($_GET["id"]);
        $input_view = new view("form/ajax_form",["model" => $model,"collection" => $collection]);
        $sview = new view("layouts/page_detail");
        //$sview->title(array("操作","名称","备注","条件","录入人","时间"));
        $sview->info("panel_body",$input_view->render());
        return $sview;
    }


    //(POST)清单审核提交
    function table_input_submit(){
        if (isset($_POST["data"])) {
            # code...
        } else {
            die("数据错误");
        }

    }
    //(POST)清单审核提交
    function wj_base_submit(){
        if (isset($_POST["group"])) {

            $model = new \App\wj_base();
            $wj_base = $model->where("title",$_POST["group"]);
            $wj_total = $wj_base->count();
            $wj_valid = $wj_base->where("valid","验证通过")->count();
            if ($wj_total == $wj_valid) {
                $wj_base_unique = DB::select("SELECT COUNT(*) as amount,ild,sys,pipeline,vnum FROM wj_base WHERE title = '".$_POST["group"]."' AND deleted_at = '2037-12-31' GROUP BY ild,sys,pipeline,vnum");
                if (sizeof($wj_base_unique) == $wj_total) {
                    $user_id = Auth::user()->id;
                    $time = \Carbon\Carbon::now();
                    $wj_all = DB::select('select id as wj_base_id, `wj_type`, `project`, `drawing`, `ild`, `sys`, `pipeline`, `vnum`, `vcode`, `jtype`, `at`, `ath`, `upstream`, `ac`, `bt`, `bth`, `downstream`, `bc`, `temperature`, `pressure`, `ft`, `qid`, `medium`, `pressure_test` from `wj_base` where `title` = "'.$_POST["group"].'" and `valid` = "验证通过" and `wj_base`.`deleted_at` = "2037-12-31"');
                    $grade = array();//避免grade重复计算
                    $check_unique = \App\wj::select("ild","sys","pipeline","vnum")->withoutGlobalScope("avail");//检验重复
                    foreach ($wj_all as $key => $value) {
                        $check_unique->orWhere(function($query) use($value){
                            $query->where("ild",$value["ild"]);
                            $query->where("sys",$value["sys"]);
                            $query->where("pipeline",$value["pipeline"]);
                            $query->where("vnum",$value["vnum"]);
                        });
                        $wj_all[$key]["created_by"] = $user_id;
                        $wj_all[$key]["created_at"] = $time;
                        $wj_all[$key] = array_merge($wj_all[$key],level_and_rate_cal($wj_all[$key]["medium"], $wj_all[$key]["pressure"], $wj_all[$key]["temperature"], $wj_all[$key]["pressure_test"], $wj_all[$key]["ac"], $wj_all[$key]["at"], $wj_all[$key]["ath"], $wj_all[$key]["bc"], $wj_all[$key]["bt"], $wj_all[$key]["bth"], $wj_all[$key]["jtype"],$grade));
                    }
                    if ($check_unique->count() == 0) {
                        DB::transaction(function() use ($wj_base,$wj_all,$user_id,$time){

                            if (!DB::table("wj")->insert($wj_all)) {
                                die("数据写入失败");
                            }

                            $ids = DB::table("wj")->select("id")->where("created_by",$user_id)->where("created_at",$time)->get();
                            $insert_ids = array();
                            foreach ($ids as $id) {
                                $insert_ids[] = $id["id"];
                            }
                            $proc = new \App\procedure\status_avail_procedure("","wj",$insert_ids);
                            $proc->name("焊口批量生效流程");
                            $proc->create_proc();

                            if (!$wj_base->update(["check_procedure" => $proc->proc_id,"updated_at" => $time, "notice" => "", "check_p" => $user_id])) {
                                die("写入完成标志失败");
                            }
                            
                            $r = array(
                                "suc" => 1,
                                "msg" => "流程创建成功",
                                "proc_id" => $proc->proc_id,
                                "ids" => array_to_multiple($insert_ids)
                            );
                            echo(json_encode($r));
                        });
                    } else {
                        $exist_item = $check_unique->get()->toArray();
                        DB::table("wj_base")->where("title",$_POST["group"])->where("deleted_at","2037-12-31")->update(["notice" => ""]);
                        $update_notice = DB::table("wj_base")->where("title",$_POST["group"])->where(function($query) use ($exist_item){
                            foreach ($exist_item as $key => $value) {
                                $query->orWhere(function($query) use ($value){
                                    $query->where("ild",$value["ild"]);
                                    $query->where("sys",$value["sys"]);
                                    $query->where("pipeline",$value["pipeline"]);
                                    $query->where("vnum",$value["vnum"]);
                                });
                            }
                        });
                        $update_notice->where("deleted_at","2037-12-31")->update(["notice" => "已存在"]);
                        $r = array(
                            "suc" => -1,
                            "msg" => "部分焊口已经存在"
                        );
                        die(json_encode($r));
                    }
                } else {
                    DB::table("wj_base")->where("title",$_POST["group"])->where("deleted_at","2037-12-31")->update(["notice" => ""]);
                    $update_notice = DB::table("wj_base")->where("title",$_POST["group"])->where(function($query) use ($wj_base_unique){
                        foreach ($wj_base_unique as $key => $value) {
                            if ($value["amount"] > 1) {
                                $query->orWhere(function($query) use ($value){
                                    $query->where("ild",$value["ild"]);
                                    $query->where("sys",$value["sys"]);
                                    $query->where("pipeline",$value["pipeline"]);
                                    $query->where("vnum",$value["vnum"]);
                                });
                            }
                        }
                    });
                    $update_notice->where("deleted_at","2037-12-31")->update(["notice" => "重复"]);
                    $r = array(
                        "suc" => -2,
                        "msg" => "录入的焊口有重复"
                    );
                    die(json_encode($r));
                }
            } else {
                $r = array(
                    "suc" => -1,
                    "msg" => "数据未通过验证！请检查数据。<br>（请逐页检查，没有查看过的数据默认为未验证）"
                );
                die(json_encode($r));
            }
        } else {
            die("数据错误");
        }
    }


    //(POST)返修执行
    function wj_r_exec(){
        if ($_POST["id"]) {

            $id = $_POST["id"];

            //利用$wj_new插入新返修焊口，用$wj修改原焊口
            $wj_new = new \App\wj();
            $wj = $wj_new->find($id);

            if ($wj->R_id > 0 || sizeof(\App\wj::where("R_src",$wj->id)->get()) > 0) {
                die("该焊口已经返修");
            } else if ($wj->R > 1) {
                die("该焊口已经返修超过两次，不允许再次返修");
            } else {
                //判断是否符合返修条件
                $exam = \App\exam::where("exam_wj_id",$id)->where("exam_conclusion","不合格")->count();
                if ($exam == 0) {
                    die("该焊口未出现不合格");
                }

                foreach ($wj_new->items_init() as $item) {
                    $wj_new->$item = $wj->$item;
                }
                $wj_new->R = $wj->R+1;
                $wj_new->R_src = $id;
                $wj_new->vnum .= "R".$wj_new->R;
                $wj_new->vcode .= "R".$wj_new->R;

                DB::transaction(function() use ($wj,$wj_new,$id){
                    if (!$wj_new->save()) {
                        die($wj_new->msg);
                    }
                    $r_id = $wj_new->id;
                    //写入原焊口(先授权)
                    $wj->authorize_user(Auth::user()->id);
                    $wj->authorize_exec("R_id");
                    $wj->R_id = $r_id;
                    if (!$wj->save()) {
                        die("写人返修标志错误:".$wj->msg);
                    }
                    //if (!DB::table("wj")->where("id",$id)->update(["R_id" => $r_id])) {
                        //die("写人返修标志错误");
                    //}
                    $proc = new \App\procedure\status_avail_procedure("","wj",$r_id,false,$wj_new->vcode."焊口生效流程");
                    $proc->name("返修流程（".$wj_new->vcode."）");
                    $proc->create_proc();

                    $r = array(
                        "suc" => 1,
                        "msg" => "流程创建成功",
                        "proc_id" => $proc->proc_id,
                        "wj_id" => $wj_new->id
                    );
                    echo(json_encode($r));
                });
                
            }


        } else {
            die("数据错误");
        }
    }

}
