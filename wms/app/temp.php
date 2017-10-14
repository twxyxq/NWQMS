<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;


require_once "table_model.php";

class temp extends table_model
{
    //

    function column(){
        $this->item->col("temp")->type("string")->name("temp");
    }



}
