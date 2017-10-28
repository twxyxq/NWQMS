<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;


require_once "table_model.php";

class exam_sheet extends table_model
{
    //

    function column(){

        $this->item->col("es_code")->type("string")->name("委托单编号")->input("exec");
        $this->item->col("es_method")->type("string")->name("检验方法")->input("exec");
        $this->item->col("es_wj_type")->type("string")->name("焊口类型")->input("exec");
        $this->item->col("es_ild_sys")->type("string")->name("系统")->input("exec");
        $this->item->col("es_demand_date")->type("date")->name("要求完成日期")->input("exec");
        $this->item->col("es_exam_ids")->type("string")->name("检验内容")->input("exec");
        $this->item->col("es_code_specify")->type("integer")->name("编号指定")->def("0")->input("exec");

        $this->item->unique("es_code");

    }


    //委托单列表
    function sheet_list($exam_method=""){
        $this->table_data(array("id","es_code","es_method","es_wj_type","es_ild_sys","name","created_at"),"user");
        if ($exam_method != "") {
            $this->data->where(function($query) use ($exam_method){
                $query->where("es_method",$exam_method);
            });
        }
        $this->data->add_proc("cancel_exam_sheet_procedure", "撤销委托", function($data){
            return $data["es_code"]."委托单撤销流程";    
        },"确定作废该委托单？");
        $this->data->col("es_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/sheet_detail?sheet_id=".$data["id"]."','".$data["es_code"]."委托单')\">".$value."</a>";
        });
        return $this->data->render();
    }

    //可撤销的委托单列表,用于变更
    function sheet_cancel_list(){
        $this->table_data(array("id","es_code","es_method","es_wj_type","es_ild_sys","name","created_at"),"user");
        $this->data->where("exam_sheet.created_by",Auth::user()->id);
        $this->data->add_proc("cancel_exam_sheet_procedure", "撤销委托", function($data){
            return $data["es_code"]."委托单撤销流程";    
        },"确定作废该委托单？");
        $this->data->col("es_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/sheet_detail?sheet_id=".$data["id"]."','".$data["es_code"]."委托单')\">".$value."</a>";
        });
        return $this->data->render();
    }




    //（功能）创建委托单
    function sheet_create($data,$exam_ids){

        DB::transaction(function() use ($data,$exam_ids){

            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
            $this->save_with_exception();

            $exam = new \App\exam();

            foreach ($exam_ids as $id) {
                $exam_collection = $exam->find($id);
                $exam_collection->authorize_user(Auth::user()->id);
                $exam_collection->exam_sheet_id = $this->id;
                $exam_collection->save_with_exception();
            }

        });

    }

     //（功能）检验分组删除
    function exam_sheet_delete($sheet_ids){

        //授权
        $auth = AUTH_EXAM_SHEET_CANCEL;

        //只允许变更一项
        if (!is_array($sheet_ids)) {
            if (!is_integer($sheet_ids)) {
                throw new \Exception("获取ID错误");
            } else {
                $sheet_id = $sheet_ids;
            }
        } else {
            if (sizeof($sheet_ids) == 0) {
                throw new \Exception("获取委托单失败");
            } else if (sizeof($sheet_ids) > 1) {
                throw new \Exception("一次只能作废一个委托单");
            } else {
                $sheet_id = $sheet_ids[0];
            }

        }

        $exam_sheet = $this->find($sheet_id);


        //去除exam的sheet标志
        $exam = \App\exam::where("exam_sheet_id",$sheet_id)->get();

        foreach ($exam as $e) {

            $e->exam_sheet_id = 0;

            $e->authorize_user($auth);
            $e->authorize_exec("exam_sheet_id");
            if (!$e->save()) {
                throw new \Exception($e->msg);
            }

        }

        if (!$this->destroy($sheet_id,$auth,"deleted_at")) {
            throw new \Exception($this->msg);
        }

    }
}
