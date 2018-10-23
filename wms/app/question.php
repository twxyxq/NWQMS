<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
require_once "table_model.php";


class question extends table_model
{
    //
    function column(){
        $this->item->col("q_bank")->type("string")->name("所属题库");
        $this->item->col("q_title")->type("string")->name("题目");
        $this->item->col("q_A")->type("string")->name("A");
        $this->item->col("q_B")->type("string")->name("B");
        $this->item->col("q_C")->type("string")->name("C");
        $this->item->col("q_D")->type("string")->name("D");
        $this->item->col("q_key")->type("string")->name("答案")->restrict("A","B","C","D");
        $this->item->col("q_comment")->type("string")->name("备注");
    }
}