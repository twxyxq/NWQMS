<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Auth;


require_once "table_model.php";

class procedure extends table_model
{
    //
    public $table_version = false;

    function column(){
        $this->item->col("pd_name")->type("string")->name("流程名称");
        $this->item->col("pd_class")->type("string")->name("流程类");
        $this->item->col("pd_executed")->type("string")->name("执行状态")->restrict("CANC","PROC","EXEC");
        $this->item->col("pd_model")->type("string")->name("关联模型");
        $this->item->col("pd_ids")->type("mediumText")->name("关联ID");
        $this->item->col("pd_info")->type("mediumText")->name("信息");

        //$this->item->unique("pd_name");
    }

    function procedure_item($builder){
        $builder->leftJoin('procedure_item','procedure_item.pd_id',$this->get_table().".id");
        $builder->leftJoin('users as u1','u1.id',$this->get_table().".created_by");
        $builder->leftJoin('users as u2','u2.id',"procedure_item.owner");
        return $builder;
    }

    function procedure_list(){
        $this->table_data(array("id","pd_name","pd_exec","pd_rollback","pd_approve","created_at"));
        $this->data->add_del();
        return $this->data->render();
    }
    //待办流程
    function to_do(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"CONCAT(u2.code,u2.name) as name2","CONCAT(u1.code,u1.name) as name1","created_at","u2.id as current_id","pd_model","pd_ids","pd_class","pd_name"),"procedure_item");
        $this->data->where("procedure_item.current_version",1);
        $this->data->where("u2.id",Auth::user()->id);
        $this->data->add_button("审核","dt_proc",function($data){
            if ($data["current_id"] == Auth::user()->id) {
                return array(explode("\\",$data["pd_class"])[2],$data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
            }
            return "";
        });
        return $this->data->render();
    }
    //焊口信息变更审核
    function alt_data_check(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"CONCAT(u2.code,u2.name) as name2","CONCAT(u1.code,u1.name) as name1","created_at","u2.id as current_id","pd_model","pd_ids","pd_class","pd_name"),"procedure_item");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\alt_procedure");
        $this->data->where("procedure_item.current_version",1);
        $this->data->col("pd_name",function($value,$data){
            return "<a href=\"###\" onclick=\"dt_proc('".explode("\\",$data["pd_class"])[2]."','".$data["id"]."','".$data["pd_model"]."','".$data["pd_ids"]."')\">".$value."</a>";
        });
        $this->data->add_button("审核","dt_proc",function($data){
            if ($data["current_id"] == Auth::user()->id) {
                return array(explode("\\",$data["pd_class"])[2],$data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
            }
            return "";
        });
        return $this->data->render();
    }
    //焊口信息变更审核完成清单
    function alt_data_list(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"name","procedure.created_at","procedure.updated_at","pd_model","pd_ids","pd_class"),"user");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\alt_procedure");
        $this->data->whereNotNull("procedure.updated_at");
        $this->data->col("pd_name",function($value,$data){
            return "<a href=\"###\" onclick=\"dt_proc('".explode("\\",$data["pd_class"])[2]."','".$data["id"]."','".$data["pd_model"]."','".$data["pd_ids"]."')\">".$value."</a>";
        });
        return $this->data->render();
    }
    //作废待审核
    function wj_cancel_check(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"CONCAT(u2.code,u2.name) as name2","CONCAT(u1.code,u1.name) as name1","created_at","u2.id as current_id","pd_model","pd_ids","pd_class","pd_name"),"procedure_item");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\cancel_procedure");
        $this->data->where("procedure_item.current_version",1);
        $this->data->col("pd_name",function($value,$data){
            return "<a href=\"###\" onclick=\"dt_proc('".explode("\\",$data["pd_class"])[2]."','".$data["id"]."','".$data["pd_model"]."','".$data["pd_ids"]."')\">".$value."</a>";
        });
        $this->data->add_button("审核","dt_proc",function($data){
            if ($data["current_id"] == Auth::user()->id) {
                return array(explode("\\",$data["pd_class"])[2],$data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
            }
            return "";
        });
        return $this->data->render();
    }
    //作废审核完成清单
    function wj_cancel_list(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"name","procedure.created_at","procedure.updated_at","pd_model","pd_ids","pd_class"),"user");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\cancel_procedure");
        $this->data->whereNotNull("procedure.updated_at");
        $this->data->col("pd_name",function($value,$data){
            return "<a href=\"###\" onclick=\"dt_proc('".explode("\\",$data["pd_class"])[2]."','".$data["id"]."','".$data["pd_model"]."','".$data["pd_ids"]."')\">".$value."</a>";
        });
        return $this->data->render();
    }

    //焊口生效流程
    function wj(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"CONCAT(u2.code,u2.name) as name2","CONCAT(u1.code,u1.name) as name1","created_at","u2.id as current_id","pd_ids","pd_model","pd_name"),"procedure_item");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\status_avail_procedure");
        $this->data->where("procedure_item.current_version",1);
        $this->data->add_button("审核","dt_status_proc",function($data){
            if ($data["current_id"] == Auth::user()->id) {
                return array($data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
            }
            return "";
        });
        return $this->data->render();
    }
    //已经审核完成的焊口流程
    function wj_checked(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"name","procedure.created_at","procedure.updated_at","pd_model","pd_ids","pd_name"),"user");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\status_avail_procedure");
        $this->data->whereNotNull("procedure.updated_at");
        $this->data->add_button("查看","dt_status_proc",function($data){
            return array($data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
        });
        return $this->data->render();
    }
    //水压变更审核
    function alt_pressure_test_check(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"CONCAT(u2.code,u2.name) as name2","CONCAT(u1.code,u1.name) as name1","created_at","u2.id as current_id","pd_model","pd_ids","pd_class","pd_name"),"procedure_item");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\alt_pressure_test_procedure");
        $this->data->where("procedure_item.current_version",1);
        $this->data->col("pd_name",function($value,$data){
            return "<a href=\"###\" onclick=\"dt_proc('".explode("\\",$data["pd_class"])[2]."','".$data["id"]."','".$data["pd_model"]."','".$data["pd_ids"]."')\">".$value."</a>";
        });
        $this->data->add_button("审核","dt_proc",function($data){
            if ($data["current_id"] == Auth::user()->id) {
                return array(explode("\\",$data["pd_class"])[2],$data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
            }
            return "";
        });
        return $this->data->render();
    }
    //已经审核完成的水压变更清单
    function alt_pressure_test_list(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"name","procedure.created_at","procedure.updated_at","pd_model","pd_ids","pd_name"),"user");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\alt_pressure_test_procedure");
        $this->data->whereNotNull("procedure.updated_at");
        $this->data->add_button("查看","dt_alt_pressure_test_proc",function($data){
            return array($data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
        });
        return $this->data->render();
    }
    //水压变更审核
    function alt_exam_specify_check(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"CONCAT(u2.code,u2.name) as name2","CONCAT(u1.code,u1.name) as name1","created_at","u2.id as current_id","pd_model","pd_ids","pd_class","pd_name"),"procedure_item");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\alt_exam_specify_procedure");
        $this->data->where("procedure_item.current_version",1);
        $this->data->col("pd_name",function($value,$data){
            return "<a href=\"###\" onclick=\"dt_proc('".explode("\\",$data["pd_class"])[2]."','".$data["id"]."','".$data["pd_model"]."','".$data["pd_ids"]."')\">".$value."</a>";
        });
        $this->data->add_button("审核","dt_proc",function($data){
            if ($data["current_id"] == Auth::user()->id) {
                return array(explode("\\",$data["pd_class"])[2],$data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
            }
            return "";
        });
        return $this->data->render();
    }
    //已经审核完成的水压变更清单
    function alt_exam_specify_list(){
        $this->table_data(array("id","pd_name",DB::raw("CHAR_LENGTH(pd_ids)-CHAR_LENGTH(replace(pd_ids,'{',''))"),"name","procedure.created_at","procedure.updated_at","pd_model","pd_ids","pd_name"),"user");
        $this->data->where("pd_model","wj");
        $this->data->where("pd_class","App\procedure\alt_exam_specify_procedure");
        $this->data->whereNotNull("procedure.updated_at");
        $this->data->add_button("查看","dt_alt_exam_specify_proc",function($data){
            return array($data["id"],$data["pd_model"],$data["pd_ids"],"",$data["pd_name"]);
        });
        return $this->data->render();
    }
}

