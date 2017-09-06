<?php 

use Illuminate\Support\Facades\DB;

define("PJCODE","1625");

define("ILD", "0,1,2,3,4,5,6,7,8,9");

define("PROJECT", "核岛安装,常规岛安装,BOP安装,核岛土建");

//定义检验比例常量
define("SQL_EXAM_RATE","CONCAT(IF(RT=0,'',CONCAT('RT:',RT,';')),IF(UT=0,'',CONCAT('UT:',UT,';')),IF(PT=0,'',CONCAT('PT:',PT,';')),IF(MT=0,'',CONCAT('MT:',MT,';')))");
//定义焊缝号常量
define("SQL_VCODE","IF(CONCAT(ild,sys,'-',pipeline,'-',vnum)=vcode,vcode,CONCAT(vcode,' [',ild,sys,'-',pipeline,'-',vnum,']'))");
//定义材质常量
//define("SQL_BASE_METAL","CONCAT(ac,IF((at=bt AND ath=bth),'',CONCAT(' Φ',at,'×',ath)),IF(ac=bc,' ',CONCAT('/',bc,' ')),'Φ',bt,'×',bth)");
define("SQL_BASE_C","CONCAT(ac,IF(ac=bc,'',CONCAT('/',bc)))");
define("SQL_BASE_TYPE_STRUCTURE","IF(at=0 AND bt=0,CONCAT('t',ath,'mm'),IF(at=0,CONCAT('t',ath,'mm/Φ',bt,'×',bth),CONCAT('Φ',at,'×',bth,'/t',bth,'mm')))");
define("SQL_BASE_TYPE","IF(at=0 OR bt=0,".SQL_BASE_TYPE_STRUCTURE.",CONCAT('Φ',at,'×',ath,IF(CONCAT(at,ath)=CONCAT(bt,bth),'',CONCAT('/Φ',bt,'×',bth))))");
define("SQL_BASE","IF(ac=bc,CONCAT(ac,' ',".SQL_BASE_TYPE."),IF(at=bt and ath=bth,CONCAT(ac,'/',bc,' ','Φ',at,'×',ath),CONCAT(ac,' ','Φ',at,'×',ath,'/',bc,' ','Φ',bt,'×',bth)))");


define("SQL_ST_BASE_C","IF(st_ac=st_bc,st_ac,CONCAT(st_ac,' ',st_bc))");
define("SQL_ST_BASE_TYPE","IF(st_at='N/A',IF(st_ath=st_bth,CONCAT(st_ath,'mm'),CONCAT(st_ath,'mm/',st_bth,'mm')),CONCAT('Φ',st_at,'×',st_ath,IF(CONCAT(st_at,st_ath)=CONCAT(st_bt,st_bth),'',CONCAT('/Φ',st_bt,'×',st_bth))))");

define("SQL_ST_BASE","CONCAT(".SQL_ST_BASE_C.",' ',".SQL_ST_BASE_TYPE.")");

//define("ILD", array(0,1,2,3,4,5,6,7,8,9));

class model_restrict
{
	public $model;
	public $col;
	public $show;
	public $fn = false;
	public $own_col;

	function __construct($attr_array,$own_col="id"){
		$this->model = $attr_array[0];
		$this->col = $attr_array[1];
		if (sizeof($attr_array) > 2) {
			if (is_object($attr_array[2])) {
				$this->show = $attr_array[1];
				$this->fn = $attr_array[2];
			} else {
				$this->show = $attr_array[2];
				$this->fn = $attr_array[3];
			}
		}
		$this->own_col = $own_col;
	}

	public static function create($attr_array,$own_col="id"){
		$mr = new model_restrict($attr_array,$own_col);
		return $mr;
	}

	function is_used($value){
		$class_name = "\\App\\".$this->model;
		$model = new $class_name();
		$collection = $model->where(function($query) use ($value){
			$query->orWhere($this->col,$value);
			$query->orWhere($this->col,"LIKE","%{".$value."}%");
		});
		if ($this->fn) {
			$collection->where($this->fn);
		}
		//print_r($collection->get()->toArray());
		if ($collection->get()->isEmpty()) {
			return false;
		} else {
			return true;
		}
	}
}
/**
* 
*/
/**
* 
*/
class table_item
{
	protected $lock = array();
	protected $unique = false;
	protected $cal = array();
	protected $msg = "";

	function lock(model_restrict $mr){
		$this->lock[] = $mr;
	}
	
	function col($col){
		if (!isset($this->$col)) {
			$this->$col = new table_col();
		}
		return $this->$col;
	}

	function msg(){
		return $this->msg;
	}

	function cal($para,$result,$is_cal = false,$fn = ""){
		if (is_object($is_cal)) {
			$fn = $is_cal;
			$is_cal = 1;//true for "is calculating depend on owner trigger, other value for the depending col"
		}
		$this->cal[] = array($para,$result,$is_cal,$fn);
		if (is_array($para)) {
			foreach ($para as $p) {
				$this->$p->cal_trigger = true;
			}
		} else {
			$this->$para->cal_trigger = true;
		}
		if (is_array($result)) {
			foreach ($result as $r) {
				$this->$r->cal_result = $is_cal;
			}
		} else {
			$this->$result->cal_result = $is_cal;
		}
		//if ($cal_switch != false) {
			//$this->$cal_switch->cal_switch = true;
			//$this->$cal_switch->cal_switch_item = $result;
		//}
	}

	function get_cal(){
		return $this->cal;
	}

	function unique(){
		$this->unique = func_get_args();
	}

	function get_unique(){
		return $this->unique;
	}

	function get_only(){
		foreach ($this as $key => $col) {
			if (is_object($col) && $col->only) {
				return $col->only;
			}
		}
		return false;
	}


	function is_used($value){
		if (sizeof($this->lock) == 0) {
			return false;
		} else {
			foreach ($this->lock as $lock) {
				if ($lock->is_used($value)) {
					return true;
				}
			}
			return false;
		}
	}

	function valid_value($col,$value){
		if (isset($this->$col)) {
			if (is_callable($this->$col->restrict)) {
				$fn = $this->$col->restrict;
				$msg = $fn($value);
				if ($msg === true) {
					return true;
				} else {
					$this->msg = $msg;
					return false;
				}
			} else if (is_array($this->$col->restrict) && sizeof($this->$col->restrict) > 0 && !in_array($value,$this->$col->restrict)) {
				$this->msg = "数据只能为【".array_to_string($this->$col->restrict)."】";
				return false;
			} else {
				if ($this->$col->is_bind($value)) {
					return true;
				} else {
					$this->msg = "数据未设置";
					return false;
				}
			}
		} else {
			$this->msg = "数据错误";
			return false;
		}
	}

	function items(){
		$r_array = array();
		foreach ($this as $key => $value) {
			$r_array[] = $key;
		}
		return $r_array;
	}

	function item_titles(){
		$r_array = array();
		foreach ($this as $key => $value) {
			$r_array[] = $this->key->name;
		}
		return $r_array;
	}

}


class table_col
{
	public $name = "N/A";
	public $type;
	public $type_para = array();
	public $def = false;//"null" or other value
	public $restrict = array();
	public $input = "init";
	public $bind = array();
	public $bind_addition = array();//附加数据
	public $multiple = false;
	public $only = false;
	//*************************************
	public $history = true;
	//true for all history in this column,
	//false for no history
	//function for where condition
	//*************************************
	//size setting
	//false for default
	public $size = false;
	//*************************************
	//calculate setting
	public $cal_trigger = false;
	public $cal_result = false;
	public $cal_switch = false;
	public $cal_switch_item = "";
	//tip setting
	public $tip = false;
	
	function __construct()
	{
		# code...
	}

	//建议的中文名
	function name($name){
		$this->name = $name;
		return $this;
	}

	function only($value){
		$this->only = $value;
		return $this;
	}


	function type($type){
		$this->type = $type;
		$this->type_para = func_get_args();
		//删除第一个字段名称
		array_shift($this->type_para);
		return $this;
	}


	function def($def){
		$this->def = $def;
		return $this;
	}

	function restrict($res){
		if (is_callable($res)) {
			$this->restrict = $res;
		} else {
			$res = is_array($res) ? $res : func_get_args();
			$this->restrict = array_merge($this->restrict,$res);
		}
		return $this;
	}

	function input($status="init"){
		$this->input = $status;//"init","cal","exec"
		return $this;
	}

	function size($size=false){
		$this->size = $size;
		return $this;
	}

	function tip($tip){
		if (substr($tip,0,1) != "<") {
			$tip = "<span style='position:absolute;bottom:3px;right:5px;'>".$tip."</span>";
		}
		$this->tip = $tip;
		return $this;
	}

	function bind($model,$col,$show="",$fn=""){
		if ($show == "") {
			$show = $col;
		}
		if (is_object($show)) {
			$this->bind = array($model,$col,$col,$show);
		} else if ($fn != "") {
			$this->bind = array($model,$col,$show,$fn);
		} else {
			$this->bind = array($model,$col,$show);
		}
		return $this;
	}

	function bind_addition($addition){
		if (is_array($addition)) {
			$this->bind_addition = $addition;
		} else {
			$this->bind_addition = func_get_args();
		}
		return $this;
	}

	function is_bind($value){
		if (sizeof($this->bind) == 0) {
			return true;
		} else if (sizeof($this->bind_addition) > 0 && in_array($value,$this->bind_addition)) {
			return true;
		} else {
			$class_name = "\\App\\".$this->bind[0];
			$bind_model = new $class_name();
			if ($this->multiple === false) {
				$result = $bind_model->where($this->bind[1],$value);
				if (isset($this->bind[3])) {
					$result->where($this->bind[3]);
				}
				if ($result->get()->isEmpty()) {
					return false;
				} else {
					return true;
				}
			} else {
				$value = multiple_to_array($value);
				$result = $bind_model->whereIn($this->bind[1],$value);
				if (isset($this->bind[3])) {
					$result->where($this->bind[3]);
				}
				if (sizeof($result->get()) != sizeof($value)) {
					return false;
				} else {
					return true;
				}
			}
			
		}
	}

	function history($history=true){
		$this->history = $history;
		return $this;
	}

	function multiple($num=1){
		$this->multiple = $num;
		return $this;
	}

}


/**
* 
*/
class depend_map
{
	const bind = array(
			array(
				array('setting','setting_r0','basemetal'), 
				array('setting','setting_name','$query->where("setting_type","basetype");'))
		);
	const refer = array(
			array(
				array('setting','setting_r0','basemetal'), 
				array('setting','setting_name','$query->where("setting_type","basetype");'))
		);

	static function have_depended($id,$table,$col){
		for ($i=0; $i < sizeof(self::bind); $i++) { 
			//if (array_key_exists($table, self::bind[$i]) && self::bind[$i][$table] == $col) {
				//return true;
			//}
			//if (self::bind[$i][0][0] == $table && self::bind[$i][0][1] == $col) {
				# code...
			//}
		}
		return false;
	}

	//static function 

	static function get_bind_value($s_array,$search="",$limit=10,$group=1){
		for ($i=0; $i < sizeof(self::bind); $i++) { 
			if (self::bind[$i][0] == $s_array) {
				$class_name = "App\\".self::bind[$i][0][0];
				$obj = new $class_name();
				$collection = $obj->select([self::bind[$i][1][1]])->where(function($query) use ($i){eval(self::bind[$i][1][2]);});
				if ($search != "") {
					$words = preg_split("/[-.,;!\s']\s*/", $search);
					foreach ($words as $word => $value) {
						$collection->where(self::bind[$i][1][1],"LIKE","%".$value."%");
					}	
				}
				if ($group == 1) {
					$collection->groupby(self::bind[$i][1][1]);
				}
				$r = $collection->get($limit);
				if ($r->isEmpty()) {
					return false;
				} else {
					return $r->toArray();
				}
			}
		}
	}

	static function is_bind_value($s_array,$value){
		for ($i=0; $i < sizeof(self::bind); $i++) { 
			if (self::bind[$i][0] == $s_array) {
				$class_name = "App\\".self::bind[$i][0][0];
				$obj = new $class_name();
				$collection = $obj->select([self::bind[$i][1][1]])->where(function($query) use ($i){eval(self::bind[$i][1][2]);});
				$collection->where(self::bind[$i][1][1],$value);
				if ($collection->get()->isEmpty()) {
					return false;
				} else {
					return true;
				}
			}
		}
		return true;
	}
}


/**
* 表格数据，用于datatable表格
*/
class table_data
{
	public $datatables = array(
		"draw" => 0,
		"recordsTotal" => 0,
		"recordsFiltered" => 0,
		"data" => array()
	);
	public $model;
	public $groupby = false;
	public $collection;
	public $collection_total;
	public $collection_filted;
	public $item;
	public $select_item;
	public $output_item;
	public $sort_item;
	public $index = true;
	public $sExt;
	public $global_fn = false;
	public $fn = array();
	public $without = array();
	public $withoutOnly = array();
	public $withoutAll = false;

	public $indexColumn = "id";

	public $special_all = "";
	public $special = array();
	
	function __construct($item,$model,$join="")
	{
		$this->model = $model;
		//***********************************************************
		//item console
		$pure_item = Array();//用于键值
		$sort_item = Array();//用于排序
		for ($i=0; $i < sizeof($item); $i++) {
			if (strpos($item[$i]," as ") > 0) {
				$pure_item[] = explode(" as ",$item[$i])[1];//键值使用as后的字段
				$sort_item[] = explode(" as ",$item[$i])[0];//排序使用纯构造字段
			} else {
				$pure_item[] = $item[$i];
				$sort_item[] = $item[$i];
			}



			if (sql_item_fn($item[$i]) || strpos($item[$i],"\"") === 0 || strpos($item[$i],"'") === 0) {
					$sort_item[$i] = DB::raw($sort_item[$i]);//排序使用纯构造字段
					$item[$i] = DB::raw($item[$i]);
			} else {
				$dot_pos = strpos($item[$i], ".");
				if ($dot_pos == false) {
					if (in_array($item[$i], $model->default_col)) {
						$item[$i] = $model->get_table().".".$item[$i];
						$sort_item[$i] = $model->get_table().".".$sort_item[$i];
					}
				} else {
					$pure_item[$i] = substr($pure_item[$i],$dot_pos+1);
				}
			}
		}

		$this->item = $pure_item;//item for use

		$this->output_item = $item;//item for output

		$this->sort_item = $sort_item;//item for sql query

		$this->select_item = $item;//item for sql query
		foreach ($this->model->default_col as $col) {
			$this->select_item[] = $model->get_table().".".$col;
		}
		
		
		//print_r($pure_item);
		//print_r($item);
		//**********************************************************

		//建立collection
		$this->collection = DB::table($model->get_table());
		//dd($this->collection);
		//$this->collection = $model->select($this->select_item);
		
		//表连接
		if ($join != "") {
			if (!is_array($join)) {
				$model->$join($this->collection);
			} else {
				for ($i=0; $i < sizeof($join); $i++) {
					$model->$join[$i]($this->collection);
				}
			}
		}
		//***********************************************************
		
		//print_r($collection);

		//$sIndexColumn = "id";

		
	}

	function __call($method, $parameters){
		if (method_exists($this->collection, $method)) {
			return call_user_func_array([$this->collection, $method], $parameters);
		} else {
			return call_user_func_array([$this->model, "scope".$method], [$this->collection,$this->model]);
		}
        
    }

	function multi_console($txt){
		return str_replace("{","",str_replace("}","",str_replace("}{", ",", $txt)));
	}

	function join($fn){
		$this->collection->$fn();
	}

	
	function indexNotIn($where){
		$this->collection->whereNotIn($this->model->get_table().".".$this->indexColumn,$where);
	}

	function groupBy($gb){
		$this->groupby = $gb;
	}

	function withoutGlobalScopes(){
		$this->withoutAll = true;
	}
	
	
	function index($index=true){
		if ($this->index === true || $this->index === false || $index === true || $index === false){
			$this->index = $index;
		} else if (is_array($this->index)) {
			$this->index[] = $index;
		} else {
			$this->index = array($this->index,$index);
		}
		
	}
	function global_fn($fn){
		if ($this->global_fn === true || $this->global_fn === false || $fn === true || $fn === false){
			$this->global_fn = $fn;
		} else if (is_array($this->global_fn)) {
			$this->global_fn[] = $fn;
		} else {
			$this->global_fn = array($this->global_fn,$fn);
		}
	}
	function col($col,$fn){//fn use value and raw_data as parameters : function($value,$raw_data){}
		if (is_array($col)) {
			foreach ($col as $c) {
				$this->fn[$c] = $fn;
			}
		} else {
			$this->fn[$col] = $fn;
		}
	}
	function without($scope){
		$this->without[] = $scope;
	}
	function onlySoftDeletes(){
		$this->withoutOnly[] = "softdeleted";
	}

	function add_del($button=true){
		$this->index(function($data,$model) use($button){
			if ($model->valid_deleting($data)) {
				if ($button === true) {
					return "<a class=\"btn btn-danger btn-small\" href=\"###\" onclick=\"dt_delete('".$model->get_table()."',".$data["id"].")\">删除</a>";
				} else {
					return "[<a href=\"###\" onclick=\"dt_delete('".$model->get_table()."',".$data["id"].")\">删除</a>]";
				}
			}
		});
	}

	function add_model(){
		$this->index(function($data,$model){
			return "<a class=\"btn btn-primary btn-small\" href=\"###\" onclick=\"dt_model(".$data["id"].")\">模版</a>";
		});
	}

	function add_button($title,$js_fn,$fn=""){
		if ($fn == "") {
			$fn = function(){return "";};
		} else if (!is_callable($fn)) {
			$fn = function() use ($fn){return $fn;};
		}
		$this->index(function($data,$model) use ($title,$js_fn,$fn){
			//获得参数文本
			$para_string = $fn($data,$model);
			//整理后的文本
			$para = "";

			//当返回值为false的时候，不显示按钮
			if ($para_string == false){
				return "";
			} else if(!is_array($para_string) && substr($para_string,0,1) == "'") {
				return substr($para_string,1,strlen($para_string)-2);
			} else {
				//根据参数文本格式创建整理文本
				if (is_array($para_string)) {
					foreach ($para_string as $value) {
						$para .= ",".$this->deal_js_para($value);
					}
					$para = substr($para,1);
				} else {
					$para = $this->deal_js_para($para_string);
				}

				return "<a for=\"".$data["id"]."\" class=\"btn btn-default btn-small\" href=\"###\" onclick=\"".$js_fn."(".$para.")\">".$title."</a>";
			}
		});
	}

	//js参数处理，对add_button提供支持
	function deal_js_para($para){
		if ((substr($para,0,1) == "{" && !is_numeric(multiple_to_array($para)[0])) || substr($para,0,9) == "function(") {
			return $para;
		} else {
			return "'".$para."'";
		}
	}

	function add_version_update(){
		$this->index(function($data,$model){
			if (!$model->is_version_updating($data) && $model->valid_version_updating($data)) {
				return "<a class=\"btn btn-info btn-small\" href=\"###\" onclick=\"version_update('".$model->get_table()."',".$data["id"].");\">升版</a>";
			} else if ($model->is_version_updating($data)) {
				return "<span style='color:grey'>升版中</span>";
			} else if ($data["current_version"] != 1) {
				return "<span style='color:grey'>旧版</span>";
			}
		});
	}

	function add_edit($para=""){
		$this->index(function($data,$model) use ($para){
			if ($model->valid_deleting($data)) {
				return "<a class=\"btn btn-warning btn-small\" href=\"###\" onclick=\"dt_edit('".$model->get_table()."',".$data["id"].",'".$para."')\">编辑</a>";
			}
		});
	}

	function add_status_proc($para=""){
		$this->index(function($data,$model) use ($para){
			if ($model->valid_status_check($data)) {
				return "<a class=\"btn btn-warning btn-small\" href=\"###\" onclick=\"dt_status_proc('".$data["procedure"]."','".$model->get_table()."',".$data["id"].",'".$para."')\">审核</a>";
			}
			return "";
		});
	}

	//获得collection数据
	function select_groupBy_handle(){
		if (isset($_GET["title"])) {
			$this->collection->select($this->output_item);
		} else {
			$this->collection->select($this->select_item);
		}
		if ($this->groupby !== false) {
			$this->collection->groupBy($this->groupby);
		}
	}

	function render(){

		//如果不是导出，就显示datatables数据
		if (!isset($_GET["output"])) {
			$this->datatables["draw"] = $_GET["draw"];
		}
		
		//应用globalscope
		if ($this->withoutAll !== true) {
			if (sizeof($this->withoutOnly) > 0) {
				foreach ($this->model->getGlobalScopes() as $key => $scope) {
					if (in_array($key,$this->withoutOnly)) {
						$scope($this->collection);
					}
				}
			} else {
				foreach ($this->model->getGlobalScopes() as $key => $scope) {
					if (!in_array($key,$this->without)) {
						$scope($this->collection);
					}
				}
			}
		}


		//额外的筛选条件
		if (isset($_GET["indexNotIn"])) {
			$this->indexNotIn(string_to_array($_GET["indexNotIn"]));
		}
		/*
		if (isset($_GET["like"])) {
			$like = explode("#",$_GET["like"]);
			foreach ($like as $l) {
				$ll = explode(",",$l);
				if (sizeof($ll) == 2) {
					$this->where($ll[0],"like",$ll[1]);
				} else if (sizeof($ll) > 2) {
					$this->where(function($query) use ($ll){
						for ($i=1; $i < sizeof($ll); $i++) { 
							$query->orWhere($ll[0],"like",$ll[$i]);
						}
					});
				}
				
			}
			
		}
		if (isset($_GET["equal"])) {
			$equal = explode("#",$_GET["equal"]);
			foreach ($equal as $l) {
				$ll = explode(",",$l);
				$this->where($ll[0],$ll[1]);
			}
		}*/


		//导出所有
		if (isset($_GET["output"]) && isset($_GET["all"])){
			
			$this->select_groupBy_handle();

			Excel::create('Filename', function($excel) {

			    $excel->sheet('Sheetname', function($sheet) {

			    	if (isset($_GET["title"])) {
			    		$sheet->appendRow(multiple_to_array($_GET["title"]));
			    	}
			        
			        foreach ($this->collection->cursor() as $value) {
			        	$sheet->appendRow($value);
			        }

			    });

			})->export('xls');
		}

		$this->datatables["recordsTotal"] = $this->collection->count($this->groupby===false?"*":DB::raw("DISTINCT(".$this->groupby.")"));

		
		if (isSET($_GET["ext"]) && $_GET["ext"] != ""){
			$this->sExt = string_to_array($_GET["ext"]);
		} else {
			$this->sExt = array();
		}
		
		if (isset($_GET["search"]["value"]) && $_GET["search"]["value"] != "" )
		{
			$this->collection->where(function($query){
				$sSearch_words = str_replace("%","\\%",$_GET["search"]["value"]);//消除%的影响
				$words = explode(",", $sSearch_words);
				for ($j=0; $j<count($words); $j++) {
				    for ($k=1; $k<count($this->item); $k++) {
					    if(!in_array($k, $this->sExt)){
							$sSearch_item = $this->item[$k];
							if(substr($this->sort_item[$k],0,5) != "COUNT" && substr($this->sort_item[$k],0,8) != "TRUNCATE" && substr($this->sort_item[$k],0,3) != "SUM" && substr($this->sort_item[$k],0,5) != "ROUND"){
								if (strpos($sSearch_item,"DISTINCT")){
									$sSearch_item = str_replace("DISTINCT","",$sSearch_item);
									//$sSearch_item = str_replace(")","",$sSearch_item);
								}
								if (substr($sSearch_item,0,12) == "GROUP_CONCAT"){
									$sSearch_item = str_replace("GROUP_","",$sSearch_item);
									//$sSearch_item = str_replace(")","",$sSearch_item);
								} 
								if (chinesechar($words[$j]) > 0){
									$query->orWhere($this->sort_item[$k],"LIKE BINARY","%".$words[$j]."%");
								} else {
									$query->orWhere($this->sort_item[$k],"LIKE","%".$words[$j]."%");
								}
							}
						}
				    }
				}
			});
			
		}
		for ( $j=1 ; $j<count($this->item) ; $j++ )
		{
			if (isset($_GET["columns"][$j]["search"]["value"]) && $_GET["columns"][$j]["searchable"] == "true" && $_GET["columns"][$j]["search"]["value"] != "" ){
				if(!in_array($j, $this->sExt)){
					if(substr($this->sort_item[$j],0,5) == "COUNT" || substr($this->sort_item[$j],0,8) == "TRUNCATE" || substr($this->sort_item[$j],0,3) == "SUM" || substr($this->sort_item[$j],0,5) == "ROUND"){
						$count_word = preg_split("/[-,;!\s']\s*/", $_GET["columns"][$j]["search"]["value"]);
						$this->collection->having($this->sort_item[$j],">=",floatval($count_word[0]));
						if (sizeof($count_word) > 1 && $count_word[1] != ""){
							$this->collection->having($this->sort_item[$j],"<=",floatval($count_word[1]));
						}
					} else {
						$sSearch_item = $this->item[$j];
						$sSearch_word = str_replace("%","\\%",$_GET["columns"][$j]["search"]["value"]);//消除%的影响
						if (strpos($sSearch_item,"DISTINCT")){
							$sSearch_item = str_replace("DISTINCT","",$sSearch_item);
						}
						if (substr($sSearch_item,0,12) == "GROUP_CONCAT"){
							$sSearch_item = str_replace("GROUP_","",$sSearch_item);
							//$sSearch_item = str_replace(")","",$sSearch_item);
						}
						//利用“,”分割，可以多项筛选
						$count_word = explode(",",$sSearch_word);
						foreach ($count_word as $w) {
							if (chinesechar($_GET["columns"][$j]["search"]["value"]) > 0){
								$this->collection->where($this->sort_item[$j],"LIKE BINARY","%".$sSearch_word."%");
							} else {
								$this->collection->where($this->sort_item[$j],"LIKE","%".$w."%");
							}
						}
							
						//}
					}
				}
			}
		}


		//导出筛选
		if (isset($_GET["output"]) && isset($_GET["filter"])){
			
			$this->select_groupBy_handle();

			Excel::create('Filename', function($excel) {

			    $excel->sheet('Sheetname', function($sheet) {

			        if (isset($_GET["title"])) {
			    		$sheet->appendRow(multiple_to_array($_GET["title"]));
			    	}

			        foreach ($this->collection->cursor() as $value) {
			        	$sheet->appendRow($value);
			        }

			    });

			})->export('xls');
		}
		
		$this->datatables["recordsFiltered"] = $this->collection->count($this->groupby===false?"*":DB::raw("DISTINCT(".$this->groupby.")"));

		if ( isset( $_GET['start'] ) && $_GET['length'] != '-1' ){
			$this->collection->offset($_GET["start"]);
			$this->collection->limit($_GET["length"]);
		}
		if (isset($_GET["order"])){
			$this->collection->orderBy($this->sort_item[$_GET["order"][0]["column"]],$_GET["order"][0]["dir"]);
		}

		//导出当前视图
		if (isset($_GET["output"]) && isset($_GET["view"])){
			
			$this->select_groupBy_handle();

			Excel::create('Filename', function($excel) {

			    $excel->sheet('Sheetname', function($sheet) {

			        if (isset($_GET["title"])) {
			    		$sheet->appendRow(multiple_to_array($_GET["title"]));
			    	}

			        foreach ($this->collection->cursor() as $value) {
			        	$sheet->appendRow($value);
			        }

			    });

			})->export('xls');
		}

		//应用groupBy
		$this->select_groupBy_handle();

		$data = $this->collection->get()->toArray();
		for ($i=0; $i < sizeof($data); $i++) {
			//raw_data para
			$raw_data = $data[$i];
			//**********************************
			//global fn excute
			if ($this->global_fn !== false) {
				foreach ($data[$i] as $key => $value) {
					if (is_array($this->global_fn)) {
						for ($m=0; $m < sizeof($this->global_fn); $m++) { 
							$fn = $this->global_fn[$k];
							$data[$i][$key] = $fn($value,$key);
						}
					} else {
						$fn = $this->global_fn;
						$data[$i][$key] = $fn($value,$key);
					}
				}
			}
			//get fn-key and excute
			$keys = array_keys($this->fn);
			for ($j=0; $j < sizeof($this->fn); $j++) {
				$fn = $this->fn[$keys[$j]];
				$data[$i][$keys[$j]] = $fn($data[$i][$keys[$j]],$raw_data);//use value and raw_data as parameters
			}
			//***********************************
			//index fn excution
			$row = array_values($data[$i]);
			if ($this->index !== false) {
				if ($this->index !== true) {
					if (is_array($this->index)) {
						$row[0] = "";
						for ($k=0; $k < sizeof($this->index); $k++) { 
							$fn = $this->index[$k];
							$row[0] .= $fn($raw_data,$this->model);
						}
					} else {
						$fn = $this->index;
						$row[0] = $fn($raw_data,$this->model);
					}
					if (strlen($row[0]) == 0) {
						$row[0] = $i+1+$_GET["start"];
					}
				} else {
					$row[0] = $i+1+$_GET["start"];
				}
			}
			//************************************
			//wrapper and replace
			for ($m=1; $m < sizeof($this->item); $m++) {
				if (substr($row[$m],0,1) == "{" && substr($row[$m],-1) == "}") {
					$row[$m] = str_replace("{","",str_replace("}","",str_replace("}{","/",$row[$m])));
				}
				if (is_callable($this->special_all)) {
					$special_fn = $this->special_all;
					$special_all = $special_fn($raw_data);
				} else {
					$special_all = $this->special_all;
				}
				$row[$m] = "<span id='".$this->item[$m]."_".$raw_data["id"]."' ".$special_all." ".(isset($this->special[$this->item[$m]."_".$raw_data["id"]])?$this->special[$this->item[$m]."_".$raw_data["id"]]:"").">".$row[$m]."</span>";
			}

			$this->datatables["data"][] = $row;
		}
		return json_encode($this->datatables);
	}
}
