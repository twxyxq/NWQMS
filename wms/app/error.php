<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;


require_once "table_model.php";

class error extends table_model
{
    //

    function column(){
        $this->item->col("error")->type("mediumText")->name("error");
    }



}
