<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\wj_model;
use App\wj_base_model;

use Illuminate\Database\Eloquent\Collection;

//use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

use view;
use nav;

class panel extends Controller
{
    
    protected $default_page = "common";

    protected $panel_nav_item = "<li class='panel_nav_item col-xs-6 col-sm-4 col-md-3 col-lg-2'><a href='/#/#0#/#'><span class='glyphicon glyphicon-th' style='display:block;font-size:30px;'></span><span id='#/#0#/#'>#/#1#/#</span></a></li>";





    function index(){
        $pview = new view("panel/common");
        $this->nav = new nav($this->name,"index");
        if ($this->nav->current_item) {
            $current_module = $this->nav->current_module->title;
        } else {
            $current_module = "模块选择";
        }
        //$pview->html = str_replace("模块", "", $pview->html);
        $pview->info("current_module",$current_module);
        $pview->info("module",$this->nav->module_data($current_module),"<a href='/#/#0#/#'>#/#1#/#</a>");
        $pview->info("panel_nav",$this->nav->module_data(),$this->panel_nav_item);
        return $pview->render();
    }


    function common($page){
        $pview = new view("panel/common");
        $pview->info("panel_nav",$this->nav->current_item->children_array(),$this->panel_nav_item);

        return $pview;
    }

    function create(){
        echo "create";
    }


    function edit(){
        echo "edit";
    }


    function destroy(){
        echo "destroy";
    }


}
