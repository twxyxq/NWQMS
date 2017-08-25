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
        $this->data->col("es_code",function($value,$data){
            return "<a href=\"###\" onclick=\"new_flavr('/consignation/sheet_detail?sheet_id=".$data["id"]."','".$data["es_code"]."委托单')\">".$value."</a>";
        });
        return $this->data->render();
    }




    //执行函数
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
}
