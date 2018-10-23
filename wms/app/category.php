<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
require_once "table_model.php";


class category extends table_model
{
    //
    function column(){
        $this->item->col("ctg_name")->type("string")->name("活动名称");
        $this->item->col("ctg_mode")->type("string")->name("活动类型");
        $this->item->col("ctg_code")->type("string")->name("活动码")->def("000000");
        $this->item->col("ctg_bank")->type("string")->name("题库");
        $this->item->col("ctg_comment")->type("string")->name("活动备注")->def("");
    }

    function generate_exam($id){
    	$category = $this->find($id);
    	$question = \App\question::whereIn("q_bank",multiple_to_array($category->ctg_bank))->limit(7);
    	return $question;
    }
}