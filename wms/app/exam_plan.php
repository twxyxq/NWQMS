<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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



   
    //分组清单
    function ep_list(){
        $this->table_data(array("id","ep_code","ep_method","ep_wj_type","ep_ild_sys","ep_pp","ep_wps","name","created_at"),"user");
        $this->data->col("ep_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/group_detail?id=".$data["id"]."')\">".$value."</a>";

        });
        return $this->data->render();
    }

    //需要复检的分组
    function ep_need_addition(){
        $this->table_data(array("id","ep_code","ep_method","ep_wj_type","ep_ild_sys","ep_pp","ep_wps","name","created_at"),"user");
        $this->data->whereRaw("ep_wj_count <> ep_wj_all_samples_count");
        return $this->data->render();
    }
}
