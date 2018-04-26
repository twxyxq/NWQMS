<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;


class ai_lock_auth extends table_model
{
    //

    function column(){
        $this->item->col("ala_lock_id")->type("integer")->name("锁ID")->bind("ai_lock","id","ai_lock_name");
        $this->item->col("ala_auth_users")->type("string")->name("授权用户")->bind("User","id","name")->multiple()->size(7);
        $this->item->col("ala_comment")->type("string")->name("备注")->def("null");


    	$this->item->unique("ala_lock_id");

        $this->status_control();

        $this->version_control();
    }


    function ai_lock($builder){
        $builder->LeftJoin('ai_lock','ai_lock.id',$this->get_table().".ala_lock_id");
        return $builder;
    }


    function ai_lock_auth_del(){
        $this->table_data(array("id","ai_lock_name","ala_auth_users","ala_comment","name","created_at","ala_lock_id"),array("ai_lock","user"));//特别注意，data中必须包含唯一列，不然无法检测出升版状态
        $this->data->col("ala_lock_id",function($value,$raw_data){
            if (\Carbon\Carbon::now()->subWeek()->gt(\Carbon\Carbon::parse($raw_data["created_at"]))) {
                return "失效";
            } else if ($raw_data["status"] != $this->status_avail) {
                return "未生效";
            } else if ($raw_data["current_version"] != 1) {
                return "失效";
            } else {
                return "生效中";
            }
        });
        $this->data->add_del();
        $this->data->add_edit();
        $this->data->add_version_update();
        
        $this->data->add_status_proc();
        $this->data->without("avail");
        $this->data->without("current_version");
        //$this->data->where("status","<>",$this->status_avail);
        return $this->data->render();
    }


    function ai_lock_auth_list(){
        $this->table_data(array("id","ai_lock_name","ala_auth_users","ala_comment","name","created_at","ala_lock_id"),array("ai_lock","user"));//特别注意，data中必须包含唯一列，不然无法检测出升版状态
        $this->data->col("ala_lock_id",function($value,$raw_data){
            if (\Carbon\Carbon::now()->subWeek()->gt(\Carbon\Carbon::parse($raw_data["created_at"]))) {
                return "失效";
            } else if ($raw_data["status"] != $this->status_avail) {
                return "未生效";
            } else if ($raw_data["current_version"] != 1) {
                return "失效";
            } else {
                return "生效中";
            }
        });
        $this->data->add_version_update();
        //$this->data->without("current_version");
        //$this->data->where("status","<>",$this->status_avail);
        return $this->data->render();
    }

}
