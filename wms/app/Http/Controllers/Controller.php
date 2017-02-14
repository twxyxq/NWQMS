<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;

use datatables;
use nav;
use view;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $name;
    public $model;
    public $model_name = "";
    public $nav;
    protected $default_page = false;

    function __construct(){
        $this->middleware('auth');
    	$classname = explode("\\", get_class($this));
    	$this->name = $classname[sizeof($classname)-1];
        if ($this->model_name != "") {
            if ($this->model_name == "auto") {
                $model_class = "App\\".$this->name;
                $this->model_name = $model_class;
            } else {
                $model_class = "App\\".$this->model_name;
            }
            $this->model = new $model_class();
        } else {
            $this->model = null;
        }
    }

    function nopage($page){
        $pview = new view("panel/nopage");
        return $pview;
    }


    function method($method){
        $m = explode("::",$method);
        return $m[sizeof($m)-1];
    }

    function show($page){
        //load nav
        $this->nav = new nav($this->name,$page);
        //**********************************************
        //load page
        if (method_exists($this,$page)) {
            $pview =  $this->$page();
        } else if ($this->default_page !== false) {
            $fn = $this->default_page;
            $pview = $this->$fn($page);
        } else {
            $pview = $this->nopage($page);
        }
        //**********************************************
        if ($this->nav->current_item) {
            //get module title
            $current_module = $this->nav->current_module->title;


            //$pview->info("top_nav",$this->nav->secondary_data(" active"),"<li class='dropdown#/#2#/#''><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>#/#1#/#</a><ul class='dropdown-menu' role='menu'><li id='module_title'><!--dp_#/#0#/#--></li></ul></li>");
            $pview->info("top_nav",$this->nav->current_module->children_array(),"<li id='#/#0#/#' class='dropdown''><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>#/#1#/#</a><ul class='dropdown-menu' role='menu'><!--dp_#/#0#/#--></ul></li>");

            for ($i=0; $i < sizeof($this->nav->current_module->item); $i++) { 
                 $pview->info("dp_".$this->nav->current_module->item[$i]->tag,$this->nav->current_module->item[$i]->children_array(),"<li id='#/#0#/#'><a href='/#/#0#/#'>#/#1#/#</a></li>");
            }
            $parents = $this->nav->current_item->parents();
            $addition_script = "<script>";
            for ($i=1; $i < min(sizeof($parents),3); $i++) { 
                $addition_script .= "$(\"#".str_replace("/","\\\/",$parents[$i]->tag)."\").addClass(\"active\");";
            }
            $addition_script .= "</script>";
            $pview->info("addition_script",$addition_script);
            $current_nav = "";
            for ($i=0; $i < sizeof($parents); $i++) { 
                $current_nav .= " -> <a href='/".$parents[$i]->tag."'>".$parents[$i]->title."</a>";
            }
            $current_nav = substr($current_nav, 4);
            $pview->info("current_nav",$current_nav);
        } else {
            $current_module = "模块";
        }
        $pview->info("current_module",$current_module);
        $pview->info("module",$this->nav->module_data($current_module),"<a href='/#/#0#/#'>#/#1#/#</a>");
        return $pview->render();
    }

    function table($method,$para=""){
    	$table_view = new datatables("/console/datatables?model=".$this->model_name."&method=".$method."&para=".$para);
        $class_name = "App\\".$this->model_name;
        $class_object = new $class_name();
        if ($para == "") {
            $table_view->title($class_object->$method("title"));
        } else {
            $table_view->title($class_object->$method($para,"title"));
        }
    	return $table_view;
    }

    function store($type){
    	//if (isset($_POST["excelinput"])) {
          // $file = Input::file('excelfile');
           //$this->import($file);
    	//} else {
            $this->$type();
    	//}
    }

    function update($type){
        $this->$type();
    }
}
