<?php 


class view{
	public $view = "";
	public $info = array();
	public $html = "";
	public $tag = array();

	function __construct($v,$para=array()){
		$this->view = $v;
		$this->load_html($para);
	}

	function view($view){
		$this->view = $view;
		$this->load_html();
		return $this;
	}


	function load_html($para=array()){
		//if (!file_exists(__DIR__."/../resources/views/".$this->view.".blade.php")) {
			//$this->html = "您访问的页面不存在";
		//} else {
			//$this->html = file_get_contents(__DIR__."/../resources/views/".$this->view.".blade.php");	
		//}
		if (sizeof($para) > 0) {
			$this->html = view($this->view,$para)->render();
		} else {
			$this->html = view($this->view)->render();
		}
		
		//$this->nav("111");
	}

	function tag($tag){
		$this->tag = $tag;
		return $this; 
	}

	function info($target,$msg,$tag="",$type="php"){
		$rep_item = "";
		$tag_before = "";
		$tag_after = "";
		if ($tag == "") {
			$tag_info = 0;
		} else if (strpos($tag,"#/#0#/#")) {
			$tag_info = 2;
		} else {
			$tag_info = 1;
		}
		//echo strpos("#/#0#/#",$tag);
		if ($tag_info == 1) {
			$tag_sep = explode(" ",$tag);
			if (sizeof($tag_sep) > 1) {
				$tag_before = "<".$tag_sep[0]." ".$tag_sep[1].">";
			} else {
				$tag_before = "<".$tag_sep[0].">";
			}
			$tag_after = "</".$tag_sep[0].">";
		}
		if (is_array($msg)){
			if (isset($msg[0]) && is_array($msg[0])) {
				for ($i=0; $i < sizeof($msg); $i++) {
					if ($tag_info == 1) {
						$tag_before_tmp = $tag_before;
						for ($j=1; $j < sizeof($tag_sep); $j++) { 
							$tag_before_tmp = str_replace("#/#".$j."#/#",$msg[$i][$j],$tag_before_tmp);
						}
						$rep_item .= $tag_before_tmp.$msg[$i][0].$tag_after;
					} else if($tag_info == 2){
						$tag_model = $tag;
						for ($j=0; $j < sizeof($msg[0]); $j++) { 
							$tag_model = str_replace("#/#".$j."#/#",$msg[$i][$j],$tag_model);
						}
						$rep_item .= $tag_model;
					} else {
						$rep_item .= $msg[$i][0];
					}
				}
				//$rep_item .= $tag_before.$msg[$i].$tag_after;
			} else {
				for ($i=0; $i < sizeof($msg); $i++) { 
					$rep_item .= $tag_before.$msg[$i].$tag_after;
				}
			}
		} else {
			$rep_item = $tag_before.$msg.$tag_after;
		}
		switch ($type) {
		 	case "php":
		 		$rep_before = "<!--";
		 		$rep_after = "-->";
		 		break;

		 	case "js":
		 		$rep_before = "//";
		 		$rep_after = "//";
		 		break;
		 	
		 	default:
		 		$rep_before = "<!--";
		 		$rep_after = "-->";
		 		break;
		}
		$this->html = str_replace($rep_before.$target.$rep_after, $rep_item, $this->html);
		return $this;
	}

	function top($content){
		$this->html = $content.$this->html;
		return $this;
	}

	function bottom($content){
		$this->html .= $content;
		return $this;
	}


	function render(){
		return $this->html;
	}


	function match($r_array){
		$key = array_keys($r_array);
		$value = array_values($r_array);
		//print_r($key);
		//print_r($r_array);
		for ($i=0; $i < sizeof($r_array); $i++) { 
			$this->info($key[$i],$value[$i]);
		}
	}

}


class datatables extends view{
	public $table_data = "";
	public $table_setting = "";
	public $table_where = "";
	public $isset_where = 0;
	public $table_link = "";
	public $column_width = array(array(),array());

	function __construct($v,$view_para=array(),$method="",$para=""){
		//使第二个参数可省略	
		if (!is_array($view_para)) {
			$para = $method;
			$method = $view_para;
			$view_para = array();
		}
		parent::__construct($v,$view_para);
		$model_method = explode("@",$method);
		$this->info("datatables.url","/console/datatables?model=".$model_method[0]."&method=".$model_method[1]."&para=".$para,"","js");
	}

	function title($title){
		$this->info("datatables.th",$title,"th");
	}

	function db($db){
		$this->table_data .= ",db : \"".$db."\"";
		return $this;
	}

	function index($index){
		$this->table_data .= ",index : \"".$index."\"";
		return $this;
	}

	function item($item_array){
		$item = array_to_text($item_array,"#/#");
		$this->table_data .= ",item : \"".$item."\"";
		return $this;
	}

	function add_where($where){
		if ($this->table_where == "") {
			$this->table_where = $where;
		} else {
			$this->table_where .= " AND (".$where.")";
		}
		return $this;
	}

	function table_where($where=""){
		if ($this->isset_where == 0) {
			if ($where == "") {
					$this->table_data .= ",where : \"".$this->table_where."\"";
			} else {
				$this->table_data .= ",where : \"".$where."\"";
				$this->isset_where = 1;
			}
		}
		return $this;
	}

	function table_auto_index($auto_index){
		$this->table_data .= ",auto_index : \"".$auto_index."\"";
		$this->info("datatables.auto_index_set","auto_index = 1;","","js");
		return $this;
	}

	function table_group($group){
		$this->table_data .= ",group : \"".$group."\"";
		return $this;
	}

	function table_ext($ext){
		$this->table_data .= ",ext : \"".$ext."\"";
		return $this;
	}


	function table_having($having){
		$this->table_data .= ",having : \"".$having."\"";
		return $this;
	}

	function render(){
		$this->table_where();
		$this->table_link();
		$this->table_width();
		$this->info("datatables.data",substr($this->table_data,1),"","js");
		$this->info("datatables.setting",$this->table_setting,"","js");
		return $this->html;
	}

	function add_link($item,$exec="",$link="###"){
		if ($this->table_link == "") {
			$this->table_link = $item."//".$exec."//".$link;
		} else {
			$this->table_link .= "#/#".$item."//".$exec."//".$link;
		}
		return $this;
	}

	function table_link(){
		if (strlen($this->table_link) > 0) {
			$this->table_data .= ",link : \"".$this->table_link."\"";
		}
		
	}


	function order($order=array(0,"asc")){
		$this->table_setting .= ",order : [[".$order[0].",'".$order[1]."'],]";
		return $this;
	}

	function width($width,$target){
		if (in_array($target,$this->column_width[0])) {
			$index = array_search($target,$this->column_width[0]);
			array_splice($this->column_width[1],$index,1,$width);
		} else {
			$this->column_width[0][] = $target;
			$this->column_width[1][] = $width;
		}
		return $this;
	}

	function table_width(){
		$column_width = "";
		for ($i=0; $i < sizeof($this->column_width[0]); $i++) { 
			$column_width .= ",{'width':'".$this->column_width[1][$i]."', 'targets':".$this->column_width[0][$i]."}";
		}
		$this->table_setting .= ",columnDefs : [".substr($column_width,1)."]";
	}

	function option($option){
		$this->table_setting .= ",".$option;
		return $this;
	}

}
/*
class view{
	public $view = "";
	public $info = array();
	public $top = "";
	public $bottom = "";
	public $nav = "";
	public $sidebar = "";
	public $html = "";
	public $nav_level1 = array();
	public $nav_level2 = array();
	public $nav_level3 = array();

	function __construct($v){
		$this->view = $v;
	}

	function view($view){
		$this->view = $view;
	}

	function nav_level($num,$nav_array){
		$nlevel = "nav_level".$num;
		$this->$nlevel = $nav_array;
	}

	function render(){
		if (!file_exists(__DIR__."/../view/".$this->view.".view.html")) {
			$this->html = "您访问的页面不存在";
		} else {
			$this->html = file_get_contents(__DIR__."/../view/".$this->view.".view.html");	
		}
		$this->insert_info("html",$this->info);
		return preg_replace("<<!--sidebar-->>",$this->sidebar,preg_replace("<<!--nav-->>",$this->nav,$this->top)).$this->html.$this->bottom;
	}

	function top($top_view){
		$this->top = file_get_contents(__DIR__."/../view/".$top_view.".view.html");
		return $this;
	}

	function bottom($bottom_view){
		$this->bottom = file_get_contents(__DIR__."/../view/".$bottom_view.".view.html");
		return $this;
	}

	function nav($nav_view){
		$this->nav = file_get_contents(__DIR__."/../view/".$nav_view.".view.html");
		$nav_item = "";
		for ($i=0; $i < sizeof($this->nav_level1); $i++) { 
			$nav_item .= "<li><a href=\"index.php?c=".$this->nav_level1[$i][1]."\">".$this->nav_level1[$i][0]."</a></li>";
		}
		$this->insert_info("nav",array(array("nav",$nav_item)));
		return $this;
	}

	function sidebar($sidebar_view){
		$this->sidebar = file_get_contents(__DIR__."/../view/".$sidebar_view.".view.html");
		$nav_item = "";
		for ($i=0; $i < sizeof($this->nav_level2); $i++) { 
			$nav_item .= "<li><a href=\"index.php?c=".$this->nav_level2[$i][1]."\">".$this->nav_level2[$i][0]."</a></li>";
		}
		$this->insert_info("sidebar",array(array("sidebar",$nav_item)));
		return $this;
	}

	function info($target,$msg){
		array_push($this->info, array($target,$msg));
	}

	function insert_info($base_html,$insert_array){
		for($i = 0; $i < sizeof($insert_array); $i++){
			$this->$base_html = preg_replace("{{{".$insert_array[$i][0]."}}}", $insert_array[$i][1], $this->$base_html);
		}
	}
}
*/


/**
* 
*/
class model_view
{

	public $model = "";
	
	function __construct($model)
	{
		$this->model = $model;
	}

	function input(){

	}
}