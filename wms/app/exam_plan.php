<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use model_restrict;


require_once "table_model.php";

class exam_plan extends table_model
{
    //

    function column(){

        $this->item->col("ep_code")->type("string")->name("分组名称")->input("exec");
        $this->item->col("ep_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("ep_wj_type")->type("string")->name("焊口类型")->input("exec");
        $this->item->col("ep_ild_sys")->type("string")->name("系统")->input("exec");
        $this->item->col("ep_pp")->type("string")->name("焊工")->input("exec");
        $this->item->col("ep_wps")->type("string")->name("工艺")->input("exec");

        $this->item->col("ep_wj_ids")->type("string")->name("焊口")->input("exec");
        $this->item->col("ep_wj_samples")->type("string")->name("抽样焊口")->input("exec");
        $this->item->col("ep_wj_addition_samples")->type("string")->name("加倍抽样焊口")->input("exec")->def("null");
        $this->item->col("ep_wj_another_samples")->type("string")->name("再次抽样焊口")->input("exec")->def("null");
        $this->item->col("ep_wj_count")->type("integer")->name("焊口数量")->input("exec")->def("0");
        $this->item->col("ep_wj_samples_count")->type("integer")->name("抽样数")->input("exec")->def("0");
        $this->item->col("ep_wj_all_samples_count")->type("integer")->name("全部抽样数")->input("exec")->def("0");
        $this->item->col("ep_weight")->type("decimal")->name("权重")->input("exec")->def("0");



    }


    //（功能）取得分组检验结果,用find或者where到的结果查询
    function get_exam_result(){
        $result = new \stdClass();

        $name_array = array("samples","addition_samples","another_samples");
        //抽样焊口及结果
        foreach ($name_array as $name) {
            if ($this->{"ep_wj_".$name} != null || strlen($this->{"ep_wj_".$name}) > 0) {
                $result->$name = multiple_to_array($this->{"ep_wj_".$name});
                $result->{$name."_exam"} = \App\exam::where("exam_plan_id",$this->id)->whereIn("exam_wj_id",$result->$name)->get();
            }
        }
        return $result;
    }

    function get_and_check_result(){
        $result = $this->get_exam_result();
        $name_array = array("samples","addition_samples","another_samples");
        foreach ($name_array as $name) {
            if (isset($result->{$name."_exam"})) {
                $result->{$name."_process"} = "已完成";
                $result->{$name."_result"} = "N/A";
                foreach ($result->{$name."_exam"} as $exam_result) {
                    if ($exam_result->exam_input_time == null) {
                        $result->{$name."_process"} = "正在检验";
                    } else if ($exam_result->exam_conclusion == "不合格" || $result->{$name."_result"} == "N/A") {
                        $result->{$name."_result"} = $exam_result->exam_conclusion;
                    }
                }
                //总结论
                $result->process = $result->{$name."_process"};
                $result->result = $result->{$name."_result"};
            }
        }
        return $result;
    }

    //（功能）执行加倍复验
    function addition_examination($id){
        $data = $this->find($id);
        $data->authorize_user("weld_syn");
        if ($data->valid_updating($data)) {
            $wjs = multiple_to_array($data->ep_wj_ids);
            $samples = multiple_to_array($data->ep_wj_samples);
            if (sizeof($wjs) <= sizeof($samples)){
                $this->msg = "已全部检验，不需要加倍";
                return false;
            } else {
                //抽取数量
                $count = sizeof($samples)*2;
                //待抽取的数组
                $to_draw = array_diff($wjs,$samples);

                //获得抽批焊口
                if (sizeof($to_draw) <= $count) {
                    $draw = $to_draw;
                } else {
                    //抽取key
                    $draw_keys = array_rand($to_draw,$count);
                    $draw = array();
                    if (is_array($draw_keys)) {
                        foreach ($draw_keys as $key) {
                            $draw[] = $to_draw[$key];
                        }
                    } else {
                        $draw[] = $to_draw[$draw_keys];
                    }
                }

                //写入数据库
                DB::transaction(function() use ($draw,$samples,$data) {
                    $data->ep_wj_addition_samples = array_to_multiple($draw);
                    $data->ep_wj_all_samples_count = sizeof($draw) + sizeof($samples);
                    if (!$data->save()) {
                        die("检验分组写入失败");
                    }
                    //写入exam
                    $exam = new \App\exam();
                    $exam->new_exam($draw,$data->ep_method,$data->id);
                    //写入wj
                    $wj = new \App\wj();
                    $wj->add_exam($draw,$draw,$data);
                });
                

            }

            return true;

        } else {
            $this->msg = $data->msg;
            return false;
        }

    }

    //（功能）执行全部复验
    function another_examination($id){
        $data = $this->find($id);
        $data->authorize_user("weld_syn");
        if ($data->valid_updating($data)) {
            $wjs = multiple_to_array($data->ep_wj_ids);
            $samples = multiple_to_array($data->ep_wj_samples);
            if ($data->ep_wj_addition_samples == null || sizeof($data->ep_wj_addition_samples) == 0) {
                $this->msg = "请先加倍检验";
                return false;
            }
            $addition_samples = multiple_to_array($data->ep_wj_addition_samples);
            if (sizeof($wjs) <= sizeof($samples) + sizeof($addition_samples)){
                $this->msg = "已全部检验，不需要再次检验";
                return false;
            } else {
                
                $draw = array_diff($wjs,$samples,$addition_samples);

                //写入数据库
                DB::transaction(function() use ($draw,$samples,$addition_samples,$data) {
                    $data->ep_wj_another_samples = array_to_multiple($draw);
                    $data->ep_wj_all_samples_count = sizeof($draw) + sizeof($samples) + sizeof($addition_samples);
                    if (!$data->save()) {
                        die("检验分组写入失败");
                    }
                    //写入exam
                    $exam = new \App\exam();
                    $exam->new_exam($draw,$data->ep_method,$data->id);
                    //写入wj
                    $wj = new \App\wj();
                    $wj->add_exam($draw,$draw,$data);
                });
                

            }

            return true;

        } else {
            $this->msg = $data->msg;
            return false;
        }
    }


    //（功能）检验分组删除
    function exam_plan_delete($plan_ids){

        //授权
        $auth = AUTH_EXAM_PLAN_CANCEL;

        //只允许变更一项
        if (!is_array($plan_ids)) {
            if (!is_integer($plan_ids)) {
                throw new \Exception("获取ID错误");
            } else {
                $plan_id = $plan_ids;
            }
        } else {
            if (sizeof($plan_ids) == 0) {
                throw new \Exception("获取分组失败");
            } else if (sizeof($plan_ids) > 1) {
                throw new \Exception("一次只能作废一个分组");
            } else {
                $plan_id = $plan_ids[0];
            }

        }

        $exam_plan = $this->find($plan_id);


        //删除exam
        $exam = \App\exam::where("exam_plan_id",$plan_id)->get();

        foreach ($exam as $e) {

            if ($e->exam_sheet_id > 0) {
                throw new \Exception("已经生成委托单，请先作废委托单");
            }

            if (!$e->destroy($e->id,$auth,"deleted_at")) {
                throw new \Exception($e->msg);
            }
        }

        //修改wj
        $wj_method_plan = $exam_plan->ep_method."_plan";
        $wj_method_weight = $exam_plan->ep_method."_weight";

        $wjs = \App\wj::where($wj_method_plan,"like","%{".$plan_id."}%")->get();

        foreach ($wjs as $wj) {

            //处理wj_plan
            $wj_plan = $wj->$wj_method_plan;
            $wj_plan_array = multiple_to_array($wj_plan);
            $wj_plan_array = array_merge(array_diff($wj_plan_array, array($plan_id)));
            $wj->$wj_method_plan = array_to_multiple($wj_plan_array);

            //处理wj_weight
            if (sizeof($wj_plan_array) == 0) {
                $wj->$wj_method_weight = 0;
            } else {
                $plans = $this->whereIn("id",$wj_plan_array);
                $weight = 0;
                foreach ($plans as $plan) {
                    if ($weight < $plan->ep_weight) {
                        $weight = $plan->ep_weight;
                    }
                }
                $wj->$wj_method_weight = $weight;
            }

            $wj->authorize_user($auth);
            $wj->authorize_exec($exam_plan->ep_method."_plan",$exam_plan->ep_method."_weight");
            if (!$wj->save()) {
                throw new \Exception($wj->msg);
                
            }
        }

        if (!$this->destroy($plan_id,$auth,"deleted_at")) {
            throw new \Exception($this->msg);
        }

    }



   
    //分组清单
    function ep_list(){
        $this->table_data(array("id","ep_code","ep_method","ep_wj_type","ep_ild_sys","ep_pp","ep_wps","name","created_at"),"user");
        $this->data->col("ep_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/group_detail?id=".$data["id"]."')\">".$value."</a>";

        });
        $this->data->add_proc("cancel_exam_plan_procedure", "撤销", function($data){
            return $data["ep_code"]."作废流程";    
        },"确定作废该检验组？");
        return $this->data->render();
    }

    //需要复检的分组
    function ep_need_addition(){
        $this->table_data(array("id","ep_code","ep_method","ep_wj_type","ep_ild_sys","ep_pp","ep_wps","name","created_at"),"user");
        $this->data->whereRaw("ep_wj_count <> ep_wj_all_samples_count");
        return $this->data->render();
    }

    //可撤销清单，用于变更（只显示自己的）
    function ep_cancel_list(){
        $this->table_data(array("id","ep_code","ep_method","ep_wj_type","ep_ild_sys","ep_pp","ep_wps","name","created_at"),"user");
        $this->data->where("exam_plan.created_by",Auth::user()->id);
        $this->data->col("ep_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/group_detail?id=".$data["id"]."')\">".$value."</a>";

        });
        $this->data->add_proc("cancel_exam_plan_procedure", "撤销", function($data){
            return $data["ep_code"]."作废流程";    
        },"确定作废该检验组？");
        return $this->data->render();
    }
}
