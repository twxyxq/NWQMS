<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
require_once "table_model.php";


class bank extends table_model
{
    //
    function column(){
        $this->item->col("bank_name")->type("string")->name("题库名称");
        $this->item->col("bank_mode")->type("string")->name("题库类型");
        $this->item->col("bank_bank")->type("string")->name("包含题库");
        $this->item->col("bank_comment")->type("string")->name("题库备注");
    }
}