<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;



class procedure_item extends table_model
{
    //

    function column(){
        $this->item->col("pd_id")->type("integer")->name("流程ID");
        $this->item->col("pdi_title")->type("string")->name("节点");
        $this->item->col("pdi_action")->type("integer")->name("处理方式")->def(0);
        $this->item->col("pdi_comment")->type("string")->name("流程文本")->def("");

        $this->item->unique("pd_id");
    }

    function procedure_list(){
        $this->table_data(array("id","pd_name","pd_exec","pd_rollback","pd_approve","created_at"));
        $this->data->add_del();
        return $this->data->render();
    }
}

