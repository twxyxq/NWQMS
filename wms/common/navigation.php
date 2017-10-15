<?php


use Illuminate\Support\Facades\Auth;
/**
* 
*/
class nav
{
	public $module = array();
	public $current_controller;
	public $current_method;
	public $current_item = false;
	public $current_module = false;
	
	function __construct($controller,$method)
	{
		$app_system = env("APP_SYSTEM","CI");

		require_once("navigation/".$app_system.".php");


		$this->current_controller = $controller;
		$this->current_method = $method;
		$this->current_item = $this->current_item();
		$this->current_module = $this->current_module();
		//dd($this->current_item());
	}


	function add_module(nav_item $nav_item){
		$this->module[$nav_item->tag] = $nav_item;
	}

	function current_item(){
		$add_on = $_SERVER["QUERY_STRING"];
		if (strlen($add_on) > 0) {
			$add_on = "?".$add_on;
		}
		$return = false;
		foreach ($this->module as $value) {
			$this->item_find($value,function($item) use ($add_on){
				if ($item->controller == $this->current_controller."@".$this->current_method.$add_on) {
					return true;
				}
			},$return);
		}
		//在无法搜索到当前节点时，去掉addon后再次搜索
		if ($return === false && strlen($add_on) > 0) {
			foreach ($this->module as $value) {
				$this->item_find($value,function($item){
					if ($item->controller == $this->current_controller."@".$this->current_method) {
						return true;
					}
				},$return);
			}
		}
		return $return;
	}

	function item_find($item,$fn,&$return){
		//print_r($item);
		if ($fn($item)) {
			$return = $item;
		}
		foreach ($item->item as $value) {
			$this->item_find($value,$fn,$return);
		}
	}

	function current_module(){
		if ($this->current_item == false) {
			return false;
		} else {
			return $this->current_item->parents()[0];
		}
	}

	function module_data($except=false){
		$data = array();
		foreach ($this->module as $value) {
			if (($except === false || ($value->tag != $except && $value->title != $except)) && $value->is_auth()) {
				$data[] = array($value->tag,$value->title,$value->iron);
			}		
		}
		return $data;
	}

	function secondary_data($current_txt=false){
		$data = array();
		if (sizeof($this->current_item->parents()) > 1) {
			foreach ($this->current_module->item as $item) {
				$current_idt = "";
				if ($current_txt !== false && $item->tag == $this->current_item->parents()[1]->tag) {
					$current_idt = $current_txt;
				}
				$data[] = array($item->tag,$item->title,$current_idt);
			}	
		}
		return $data;
	}

	function nav_data(){
		return $this->current_module->childrens_array();

	}
}


/**
* 
*/
class nav_item
{
	public $title;
	public $tag;
	public $item = array();
	public $parent = false;
	public $controller;
	public $auth = array();
	public $iron = "";
	public $msg;
	
	function __construct($tag,$title,$controller="",$auth=array(),$iron="")
	{
		//设置为可省略controller参数
		if (is_array($controller)) {
			$iron = $auth;
			$auth = $controller;
			$controller = "";
		}
		$this->tag = $tag;
		$this->title = $title;
		$this->auth = $auth;
		$this->iron = $iron;
		if ($controller == "") {
			$this->controller = str_replace("/","@",$tag);
		} else if (substr($this->controller,-1) == "@") {
			$this->controller = $controller.$tag;
		} else {
			$this->controller = $controller;
		}
	}

	public static function create($tag,$title,$controller="",$children_array=array(),$auth=array(),$iron=""){
		//可变参数设置
		if (!is_string($controller) || ($controller != "" && strpos($controller,"@") == false)) {
			$iron = $auth;
			$auth = $children_array;
			$children_array = $controller;
			$controller = "";
		}
		if (sizeof($children_array) > 0 && !$children_array[0] instanceof nav_item) {	
			$iron = $auth;
			$auth = $children_array;
			$children_array = array();
		}
		if (is_string($auth)) {
			$iron = $auth;
			$auth = array();
		}
		if (!is_string($iron)) {
			$iron = "";
		}
		$item = new nav_item($tag,$title,$controller,$auth,$iron);
		for ($i=0; $i < sizeof($children_array); $i++) { 
			$item->child($children_array[$i]);
		}
		return $item;
	}

	function child(nav_item $child){
		$child->parent = $this;
		$this->item[] = $child;
	}

	function children_array(){
		$r_array = array();
		for ($i=0; $i < sizeof($this->item); $i++) { 
			if ($this->item[$i]->is_auth()) {
				$r_array[] = array($this->item[$i]->tag,$this->item[$i]->title,$this->item[$i]->iron);
			}
		}
		return $r_array;
	}

	function childrens_array(){
		$r_array = array();
		for ($i=0; $i < sizeof($this->item); $i++) { 
			if ($this->item[$i]->is_auth()) {
				$r_array[] = array($this->item[$i]->tag,$this->item[$i]->title,$this->item[$i]->children_array());
			}
		}
		return $r_array;
	}

	function parents(){
		$parents = array($this);
		$item = $this;
		while ($item->parent !== false) {
			$item = $item->parent;
			array_unshift($parents,$item);
		}
		return $parents;
	}

	function is_auth(){
		if (sizeof($this->auth) == 0) {
			$this->msg = "无须验证";
			return true;
		} else if (in_array(Auth::user()->id,$this->auth)) {
			$this->msg = "授权用户";
			return true;
		} else {
			foreach (multiple_to_array(Auth::user()->auth) as $auth) {
				if (in_array($auth,$this->auth)) {
					$this->msg = "授权组";
					return true;
				}
			}
		}
		$this->msg = "您没有授权";
		return false;
	}
}