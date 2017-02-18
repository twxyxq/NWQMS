<?php 

use Illuminate\Support\Facades\DB;


define("ILD", "0,1,2,3,4,5,6,7,8,9");

define("PROJECT", "核岛安装,常规岛安装,BOP安装,核岛土建");

//定义检验比例常量
define("SQL_EXAM_RATE","CONCAT(IF(RT='N/A','',CONCAT('RT:',RT,';')),IF(UT='N/A','',CONCAT('UT:',UT,';')),IF(PT='N/A','',CONCAT('PT:',PT,';')),IF(MT='N/A','',CONCAT('MT:',MT,';')))");
//定义焊缝号常量
define("SQL_VCODE","IF(CONCAT(ild,sys,'-',pipeline,'-',vnum) = vcode,vcode,CONCAT(vcode,' [',ild,sys,pipeline,'-',vnum,']'))");
//定义材质常量
//define("SQL_BASE_METAL","CONCAT(ac,IF((at=bt AND ath=bth),'',CONCAT(' Φ',at,'×',ath)),IF(ac=bc,' ',CONCAT('/',bc,' ')),'Φ',bt,'×',bth)");
define("SQL_BASE_C","CONCAT(ac,IF(ac=bc,'',CONCAT('/',bc)))");
define("SQL_BASE_TYPE","CONCAT('Φ',at,'×',ath,IF(CONCAT(at,ath)=CONCAT(bt,bth),'',CONCAT('/Φ',bt,'×',bth)))");
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

	function lock(model_restrict $mr){
		$this->lock[] = $mr;
	}
	
	function col($col){
		$this->$col = new table_col();
		return $this->$col;
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
			if (sizeof($this->$col->restrict) > 0 && !in_array($value,$this->$col->restrict)) {
				return false;
			} else {
				if ($this->$col->is_bind($value)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
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
	public $def = false;//"null" or other value
	public $restrict = array();
	public $input = "init";
	public $bind = array();
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
	public $cal_fn = false;
	public $cal_para = false;
	
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
		array_shift($this->type_para);
		return $this;
	}


	function def($def){
		$this->def = $def;
		return $this;
	}

	function restrict($res){
		$res = is_array($res) ? $res : func_get_args();
		$this->restrict = array_merge($this->restrict,$res);
		return $this;
	}

	function cal($para,$fn,$force=true){
		if ($force === true) {
			$this->input = "cal";
		}
		$this->cal_fn = $fn;
		$this->cal_para = $para;
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

	function is_bind($value){
		if (sizeof($this->bind) == 0) {
			return true;
		} else {
			$class_name = "\\App\\".$this->bind[0];
			$bind_model = new $class_name();
			if ($this->multiple === false) {
				$result = $bind_model->where($this->bind[1],$value)->get();
			} else {
				$result = $bind_model->whereIn($this->bind[1],string_to_array(str_replace("}","",str_replace("{","",str_replace("}{",",",$value)))))->get();
			}
			if ($result->isEmpty()) {
				return false;
			} else {
				return true;
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
* 
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
	public $collection;
	public $collection_total;
	public $collection_filted;
	public $item;
	public $select_item;
	public $index = true;
	public $sExt;
	public $global_fn = false;
	public $fn = array();
	public $without = array();

	public $indexColumn = "id";
	
	function __construct($item,$model,$join="")
	{
		$this->model = $model;
		//***********************************************************
		//item console
		$pure_item = Array();
		for ($i=0; $i < sizeof($item); $i++) {
			if (sql_item_fn($item[$i]) || strpos($item[$i],"\"") === 0 || strpos($item[$i],"'") === 0) {
				if (strpos($item[$i]," as ") > 0) {
					$pure_item[] = explode(" as ",$item[$i])[1];
					$item[$i] = DB::raw(explode(" as ",$item[$i])[0]);
				} else {
					$pure_item[] = $item[$i];
					$item[$i] = DB::raw($item[$i]);
				}
			} else {
				$dot_pos = strpos($item[$i], ".");
				if ($dot_pos == false) {
					$pure_item[] = $item[$i];
					if (in_array($item[$i], $model->default_col)) {
						$item[$i] = $model->get_table().".".$item[$i];
					}
				} else {
					$pure_item[] = substr($item[$i],$dot_pos+1);
				}
			}
		}
		$this->item = $pure_item;//item for use
		$this->select_item = $item;//item for sql query
		foreach ($this->model->default_col as $col) {
			$this->select_item[] = $model->get_table().".".$col;
		}
		//print_r($this->select_item);
		
		//print_r($pure_item);
		//print_r($item);
		//**********************************************************
		//table join console
		if ($join != "") {
			if (!is_array($join)) {
				$this->collection = $model->$join()->select($this->select_item);
			} else {
				$this->collection = $model;
				for ($i=0; $i < sizeof($join); $i++) { 
					$this->collection = $this->collection->$join[$i]();
				}
				$this->collection = $this->collection->select($this->select_item);
			}
		} else {
			$this->collection = $model->select($this->select_item);
		}
		//***********************************************************
		
		//print_r($collection);

		//$sIndexColumn = "id";

		
	}

	function multi_console($txt){
		return str_replace("{","",str_replace("}","",str_replace("}{", ",", $txt)));
	}

	function join($fn){
		$this->collection->$fn();
	}

	function where($where){
		$this->collection->where($where);
	}
	function indexNotIn($where){
		$this->collection->whereNotIn($this->model->get_table().".".$this->indexColumn,$where);
	}
	function limit($limit){
		$this->collection->limit($limit);
	}
	function offset($offset){
		$this->collection->offset($offset);
	}
	function groupby($groupby){
		$this->collection->groupby($groupby);
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
	function col($col,$fn){
		$this->fn[$col] = $fn;
	}
	function without($scope){
		$this->without[] = $scope;
	}

	function add_del(){
		$this->index(function($data,$model){
			if ($model->valid_deleting($data)) {
				return "<a class=\"btn btn-danger btn-small\" href=\"###\" onclick=\"dt_delete('".$model->get_table()."',".$data["id"].")\">删除</a>";
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
		}
		$this->index(function($data,$model) use ($title,$js_fn,$fn){
			$para = $fn($data,$model);
			if (is_array($para)) {
				$js_para = "";
				foreach ($para as $value) {
					$js_para .= ",'".$value."'";
				}
				$js_para = substr($js_para,1);
			} else {
				$js_para = $para;
			}
			return "<a class=\"btn btn-default btn-small\" href=\"###\" onclick=\"".$js_fn."(".$js_para.")\">".$title."</a>";
		});
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
		});
	}

	function render(){

		if (sizeof($this->without) > 0) {
			$this->collection->withoutGlobalScopes($this->without);
		}

		$this->datatables["draw"] = $_GET["draw"];

		$this->datatables["recordsTotal"] = $this->collection->count();

		
		if (isSET($_GET["ext"]) && $_GET["ext"] != ""){
			$this->sExt = string_to_array($_GET["ext"]);
		} else {
			$this->sExt = array();
		}

		if (isset($_GET["indexNotIn"])) {
			$this->indexNotIn(string_to_array($_GET["indexNotIn"]));
		}
		
		if (isset($_GET["search"]["value"]) && $_GET["search"]["value"] != "" )
		{
			$this->collection->where(function($query){
				$sSearch_words = str_replace("%","\\%",$_GET["search"]["value"]);//消除%的影响
				$words = preg_split("/[-.,;!\s']\s*/", $sSearch_words);
				for ($j=0; $j<count($words); $j++) {
				    for ($k=1; $k<count($this->item); $k++) {
					    if(!in_array($k, $this->sExt)){
							$sSearch_item = $this->item[$k];
							if(substr($sSearch_item,0,5) != "COUNT" && substr($sSearch_item,0,8) != "TRUNCATE" && substr($sSearch_item,0,3) != "SUM"){
								if (strpos($sSearch_item,"DISTINCT")){
									$sSearch_item = str_replace("DISTINCT","",$sSearch_item);
									//$sSearch_item = str_replace(")","",$sSearch_item);
								}
								if (substr($sSearch_item,0,12) == "GROUP_CONCAT"){
									$sSearch_item = str_replace("GROUP_","",$sSearch_item);
									//$sSearch_item = str_replace(")","",$sSearch_item);
								} 
								//if (chinesechar($words[$j]) > 0){
									//$query->orWhere($sSearch_item,"LIKE","BINARY %".$words[$j]."%");
								//} else {
									$query->orWhere($this->select_item[$k],"LIKE","%".$words[$j]."%");
								//}
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
					if(substr($this->item[$j],0,5) == "COUNT" || substr($this->item[$j],0,8) == "TRUNCATE" || substr($this->item[$j],0,3) == "SUM"){
						$count_word = preg_split("/[-,;!\s']\s*/", $_GET["columns"][$j]["search"]["value"]);
						$this->collection->having($this->select_item[$j],">=",floatval($count_word[0]));
						if (sizeof($count_word) > 1 && $count_word[1] != ""){
							$this->collection->having($this->select_item[$j],"<=",floatval($count_word[1]));
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
						//if (chinesechar($_GET["columns"][$j]["search"]["value"]) > 0){
							//$this->collection->where($sSearch_item,"LIKE","BINARY %".$sSearch_word."%");
						//} else {
							$this->collection->where($this->select_item[$j],"LIKE","%".$sSearch_word."%");
						//}
					}
				}
			}
		}

		
		$this->datatables["recordsFiltered"] = $this->collection->count();

		if ( isset( $_GET['start'] ) && $_GET['length'] != '-1' ){
			$this->collection->offset($_GET["start"]);
			$this->collection->limit($_GET["length"]);
		}
		if (isset($_GET["order"])){
			$this->collection->orderBy($this->select_item[$_GET["order"][0]["column"]],$_GET["order"][0]["dir"]);
		}

		$data = $this->collection->get()->toArray();
		for ($i=0; $i < sizeof($data); $i++) {
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
				$data[$i][$keys[$j]] = $fn($data[$i][$keys[$j]]);
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
				$row[$m] = "<span id='".$this->item[$m]."_".$raw_data["id"]."'>".$row[$m]."</span>";
			}

			$this->datatables["data"][] = $row;
		}
		return json_encode($this->datatables);
	}
}
