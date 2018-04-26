<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Blueprint;
use model_restrict;



class work_report extends table_model
{
    //

    function column(){

    	$this->item->col("wr_type")->type("string")->name("类别")->restrict("总体","核岛土建","核岛安装","常规岛");
        $this->item->col("wr_title")->type("string")->name("标题")->history(false);
        $this->item->col("wr_content")->type("mediumText")->name("内容")->textarea(3)->history(false);
    	$this->item->col("wr_level")->type("string")->name("重要性")->restrict("-","普通","中","高","极高");



    }



    function my_report(){
        $this->table_data(array("id","wr_type","wr_title","wr_content","wr_level"));
        $this->data->where("work_report.created_by",Auth::user()->id);
        $this->data->add_del();
        $this->data->add_edit();
        return $this->data->render();
    }

}
