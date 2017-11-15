<?php 


class view{
	public $view = "";
	public $info = array();
	public $html = "";
	public $tag = array();

	public $para = array();

	function __construct($v,$para=array()){
		$this->view = $v;
		$this->para = $para;
		//$this->load_html($para);
	}

	function view($view){
		$this->view = $view;
		$this->load_html();
		return $this;
	}

	function info($target,$msg){
		$this->para[$target] = $msg;
	}

	function render(){
		return view($this->view,$this->para)->render();
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
		if (!is_array($view_para) || (isset($view_para[0]) && is_array($view_para[0])) || (sizeof($view_para) == 0 && sizeof(func_get_args()) == 2)) {
			$para = $method;
			$method = $view_para;
			$view_para = array();
		}
		//如果method是数组，则为预先载入数据的静态表格,有output直接输入
		if (is_array($method)) {
			//output直接输入excel
			if(isset($_GET["output"])){
				$n = 1;
				Excel::create('Filename', function($excel) use ($method,$n) {
					$excel->sheet('Sheetname', function($sheet) use ($method,$n) {
						foreach ($method as $value) {$value[0] = $n++;
							$sheet->appendRow($value);
						}
					});
				})->export('xls');
				exit(1);
			} else {
				$view_para = array_merge($view_para,array("dataset" => $method));
			}
		}
		parent::__construct($v,$view_para);
		if (!is_array($method)) {
			$model_method = explode("@",$method);
			$this->info("datatables_url","/console/datatables?model=".$model_method[0]."&method=".$model_method[1]."&para=".$para,"","js");
		}
	}

	function title($title){
		$this->info("datatables_th",$title);
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
	/*
	function table_auto_index($auto_index){
		$this->table_data .= ",auto_index : \"".$auto_index."\"";
		$this->info("datatables.auto_index_set","auto_index = 1;","","js");
		return $this;
	}
	*/
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
		$this->info("datatables_data",substr($this->table_data,1),"","js");
		$this->info("datatables_setting",$this->table_setting,"","js");
		return parent::render();
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
		if (!is_array($order)) {
			$order = func_get_args();
		}
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