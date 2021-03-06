<?php

namespace App\procedure;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
/**
* 
*/
class procedure
{
	

	public $path = array();//审批路径

	public $auth = array();//审批授权
	
	public $procedure_model_name = "procedure";

	public $procedure_model_item_name = "procedure_item";
	
	public $procedure_model = false;//流程模型，载入时生成

	public $model = false;//关联的表模型

	public $model_name = false;//关联的表模型名称
	
	public $col = false;//涉及的列

	public $ids = false;//涉及的id

	public $procedure_lock = array();

	public $procedure_exec;

	public $procedure_rollback;

	public $msg = "";

	public $info = false;//流程信息，即procedure通过find获取

	public $item_info = false;

	public $item_history = false;

	public $pd_class = false;

	public $proc_id = 0;


	public $proc_name = "新流程";

	public $proc_exec = "PROC";

	public $pd_info = "";//信息，用于变更或执行详情

	public $comment = "";//用于备注

	public $view_page = false;//用于显示变更信息


	//载入相关信息
	function __construct($proc_id,$model_name=false,$ids=false,$info=false,$comment="")
	{
		//设置可省略参数
		if (is_object($model_name)) {
			$info = $model_name;
			$model_name = false;
			$ids = false;
		}
		//procedure model
		$class_name = "\\App\\".$this->procedure_model_name;
		$this->procedure_model = new $class_name();

		//procedure class name
		$this->pd_class = get_class($this);

		//procedure item model
		$class_name = "\\App\\".$this->procedure_model_item_name;
		$this->procedure_item_model = new $class_name();

		//如果有预先载入的流程而且id匹配,则载入
		if ($info !== false && $info->id == $proc_id) {
			$this->info = $info;
		}

		//如果是新建流程，则载入comment
		$this->comment = $comment;

		//load or get value
		if (is_numeric($proc_id)) {
			if (!$this->load_proc($proc_id)) {
				die("流程载入失败");
			}
		} else {
			$this->model_name = $model_name;
			$this->ids = is_array($ids)?$ids:multiple_to_array($ids);
		}

		//model instance
		$class_name = "\\App\\".$this->model_name;
		$this->model = new $class_name();

		//other boot
		$this->proc_boot();
	}

	static function load($id){
		$proc = \App\procedure::find($id);
		if ($proc == null) {
			return false;
		}
		$data = new $proc->pd_class($id,$proc);
		return $data;
	}

	static function in_proc_label($id){
		$procedure = \App\procedure\procedure::load($id);
		$pd_class_array = explode("\\",$procedure->pd_class);
    	return "[<a href=\"###\" onclick=\"dt_proc('".end($pd_class_array)."',".$id.",'".$procedure->model_name."','".array_to_multiple($procedure->ids)."','','".$procedure->proc_name."')\">流程中</a>]";
	}

	function proc_boot(){}


	function load_proc($id){
		//如果没有预先载入信息，则查询表载入
		if ($this->info !== false) {
			$this->proc_id = $id;
			$proc = $this->info;
		} else {
			$this->proc_id = $id;
			$proc = $this->procedure_model->find($id);
		}
		$this->proc_name = $proc->pd_name;
		if (!is_null($proc) && $proc->pd_class == $this->pd_class) {
			$this->info = $proc;
			$this->pd_info = $proc->pd_info;
			$this->model_name = $this->info->pd_model;
			$this->ids = multiple_to_array($this->info->pd_ids);
			//load item
			$proc_item = $this->procedure_item_model->where("pd_id",$id)->orderby("version","asc")->get();
			$this->item_info = $proc_item;
			//load history
			$proc_history = $this->procedure_item_model->select(["pdi_title","pdi_comment","procedure_item.updated_at","name",DB::raw("IF(pdi_action=1,'通过',IF(pdi_action=-1,'退回','')) as action")])->leftJoin("users",'users.id',"procedure_item.owner")->withoutGlobalscopes()->where("pd_id",$id)->whereNotNull("procedure_item.updated_at")->orderby("updated_at","asc")->get();
			$this->item_history = $proc_history;
			return true;
		} else {
			$this->msg = "载入失败";
			return false;
		}

	}

	function pd_info(){
		return $this->pd_info;
	}

	function is_first_proc(){
		if ($this->item_info) {
			return $this->item_info[0]->current_version == 1;
		}
		return false;
	}

	function get_current_proc(){
		if ($this->item_info) {
			foreach ($this->item_info as $item_info) {
				if ($item_info->current_version == 1) {
					return $item_info;
				}
			}
		}
		return false;
	}

	function get_final_auth(){
		if (sizeof($this->auth) > 0) {
			return end($this->auth);
		} else {
			return false;
		}
	}

	function get_prev_proc(){
		$current = 0;
		if ($this->item_info) {
			$prev = false;
			foreach ($this->item_info as $item_info) {
				if ($item_info->current_version == 1) {
					return $prev;
				}
				$prev = $item_info;
			}
		}
		return false;
	}

	function get_next_proc(){
		$current = 0;
		if ($this->item_info) {
			foreach ($this->item_info as $item_info) {
				if ($current == 1) {
					return $item_info;
				}
				if ($item_info->current_version == 1) {
					$current = 1;
				}
			}
		}
		return false;
	}

	function get_next_procs(){
		$current = 0;
		$tail_procs = array();
		if ($this->item_info) {
			foreach ($this->item_info as $item_info) {
				if ($current == 1) {
					$tail_procs[] = $item_info;
				}
				if ($item_info->current_version == 1) {
					$current = 1;
				}
			}
			if (sizeof($tail_procs) > 0) {
				return $tail_procs;
			}
		}
		return false;
	}

	function name($name){
		$this->proc_name = $name;
	}
	function info($pd_info){
		$this->pd_info = $pd_info;
	}

	function create_proc(){

		if ($this->proc_id == 0) {
			if ($this->model_name == false || $this->ids == false) {
				$this->msg = "流程信息不全";
				return false;
			} else {
				if (sizeof($this->path) == 0) {
					$this->msg = "审批路径未设定";
					return false;
				} else {
					if (DB::table($this->model_name)->whereIn("id",$this->ids)->where("procedure","")->count() != sizeof($this->ids)) {
						dd(($this->ids));
						$this->msg = "有流程正在进行中，无法再次启动流程";
						return false;
					} else {
				    	DB::transaction(function()
						{
							//create new procedure
						    $proc_id = DB::table($this->procedure_model_name)->insertGetId(["pd_name" => $this->proc_name,"pd_model" => $this->model_name,"pd_ids" => array_to_multiple($this->ids),"pd_class" => get_class($this),"pd_executed" => $this->proc_exec,"pd_info" => $this->pd_info,"pd_comment" => $this->comment,"created_by" => Auth::user()->id,"created_at" => Carbon::now()]);

						    //lock model item
						    DB::table($this->model_name)->whereIn("id",$this->ids)->update(["procedure" => $proc_id]);

						    //create procedure item by "path" of control model
					    	$i = 0;
					    	$insert_array = array();
					    	foreach ($this->path as $key => $value) {
					    		if ($i == 0) {
					    			$owner = Auth::user()->id;
					    			$current_version = 1;
					    		} else {
					    			$owner = 0;
					    			$current_version = null;
					    		}
					    		$insert_array[] = ["version" => $key,"current_version" => $current_version,"pdi_title" => $value,"created_by" => Auth::user()->id,"owner" => $owner,"pd_id" => $proc_id];
					    		$i++;
					    	}
					    	DB::table($this->procedure_model_item_name)->insert($insert_array);
						    

						    $this->load_proc($proc_id);
						});
				    }
						
				}
					
			}
			
		} else {
			$this->msg = "流程已存在";
			return false;
		}
		return true;

		


		/*
		$this->procedure_model->pd_name = $this->generate_pd_name();
		$this->procedure_model->pd_model = $this->model_name;
		$this->procedure_model->pd_ids = array_to_multiple($this->ids);
		$this->procedure_model->pd_class = get_class($this);
		$this->procedure_model->pd_executed = "PROC";
		if ($this->procedure_model->save()) {
			//lock model
			$class_name = "\\App\\".$this->model_name;
			$model = new $class_name();
			$model->onlySoftDeletes()->whereIn("id",$this->ids)->update(["procedure" => $this->procedure_model->id]);
			if ($model->status_control) {
				$this->path = $model->status_control->path;
			}
			if (sizeof($this->path) > 0) {
				$version = array_keys($this->path)[0];
				$title = $this->path[0];
			} else {
				$version = "A";
				$title = "编写";
			}
			//item create
			$item_class_name = "\\App\\".$this->procedure_model_item_name;
			$item_model = new $item_class_name();
			$item_model->pdi_title = $title;
			$item_model->version = $version;
			$item_model->owner = Auth::user()->id;
			$item_model->pd_id = $this->procedure_model->id;
			if ($item_model->save()) {
				# code...
			} else {
				echo $item_model->error();
			}
			$this->msg = "流程创建成功";
			$this->load_proc($this->procedure_model->id);
			return true;
		} else {
			return false;
		}
		*/
	}

	function get_current_owner(){
		$r = $this->procedure_item_model->where("pd_id",$this->proc_id)->where("current_version",1)->get();
		return $r[0]->owner;
	}

	//流程通过函数
	function pass_proc($comment = "",$next_owners = array()){
		$current_proc = $this->get_current_proc();
		$next_proc = $this->get_next_proc();
		$next_procs = $this->get_next_procs();
		if ($next_procs !== false) {
			if (sizeof($next_owners) == 0) {
				$next_owner = 0;
			} else {
				$next_owner = $next_owners[0];
			}
			$next_owner = $next_owner == 0 ? $next_proc->owner : $next_owner;
		} else {
			$next_owner = 0;
		}
		if ($current_proc !== false && $next_proc !== false && $current_proc->owner == Auth::user()->id && $next_owner != 0) {
			DB::transaction(function() use ($comment,$current_proc,$next_proc,$next_procs,$next_owner,$next_owners)
			{
				//设置当前节点的信息
				DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["current_version" => null,"pdi_action" => 1,"pdi_comment" => $comment,"updated_at" => Carbon::now()]);
				//设置后续节点信息
				DB::table($this->procedure_model_item_name)->where("id",$next_proc->id)->update(["current_version" => 1,"owner" => $next_owner]);
				//如果后续节点超过2个，而且有预设值，则写入后续节点
				if (sizeof($next_owners) >= 2) {
					for ($i=1; $i < min(sizeof($next_owners),sizeof($next_procs)); $i++) { 
						DB::table($this->procedure_model_item_name)->where("id",$next_procs[$i]->id)->update(["owner" => $next_owners[$i]]);
					}
				}
				//将审批项的owner转移,便于修改
				DB::table($this->model_name)->whereIn("id",$this->ids)->update(["owner" => $next_owner]);
				
			});
		} else if($current_proc !== false && $next_proc === false && $current_proc->owner == Auth::user()->id){
			//当最后一个节点时，关闭流程并执行相应的操作
			DB::transaction(function() use ($comment,$current_proc)
			{
				//设置当前节点信息
				DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["current_version" => null,"pdi_action" => 1,"pdi_comment" => $comment,"updated_at" => Carbon::now()]);

				//unlock model, recover owner
				DB::table($this->model_name)->whereIn("id",$this->ids)->update(["owner" => 0,"procedure" => ""]);

				//set finished flag
				DB::table($this->procedure_model_name)->where("id",$this->proc_id)->update(["updated_at" => Carbon::now()]);

				$this->finish_proc();


			});
			
		}
	}

	function rollback_proc($comment = ""){
		if (!$this->is_first_proc()) {
			DB::transaction(function() use ($comment)
			{
				$current_proc = $this->get_current_proc();
				$prev_proc = $this->get_prev_proc();

				//删除前置节点
				DB::table($this->procedure_model_item_name)->where("id",$prev_proc->id)->update(["deleted_at" => Carbon::now()]);
				//删除当前节点
				DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["current_version" => 0,"pdi_comment" => $comment,"pdi_action" => -1,"updated_at" => Carbon::now(),"deleted_at" => Carbon::now()]);
				//重建前置节点
				DB::table($this->procedure_model_item_name)->insert(["version" => $prev_proc->version,"current_version" => 1,"pdi_title" => $prev_proc->pdi_title,"created_by" => Auth::user()->id,"owner" => $prev_proc->owner,"pd_id" => $prev_proc->pd_id,"created_at" => Carbon::now()]);
				//重建当前节点
				DB::table($this->procedure_model_item_name)->insert(["version" => $current_proc->version,"current_version" => null,"pdi_title" => $current_proc->pdi_title,"created_by" => Auth::user()->id,"owner" => $current_proc->owner,"pd_id" => $current_proc->pd_id,"created_at" => Carbon::now()]);
				//将审批项的owner回退到前一个节点,便于修改
				DB::table($this->model_name)->whereIn("id",$this->ids)->update(["owner" => $prev_proc->owner]);

			});
		} else {

			$this->cancel_proc($comment);

		}
		
	}


	function cancel_proc($comment){

		$current_proc = $this->get_current_proc();

		DB::transaction(function() use($current_proc,$comment){

			//设置当前节点信息
			DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["current_version" => null,"pdi_action" => -1,"pdi_comment" => $comment,"updated_at" => Carbon::now()]);

			//将审批项的owner重置为0,unlock model item
			DB::table($this->model_name)->whereIn("id",$this->ids)->update(["owner" => 0,"procedure" => ""]);

			//delete proc
			DB::table($this->procedure_model_name)->where("id",$this->proc_id)->update(["deleted_at" => Carbon::now()]);
			
			//delete proc_item
			DB::table($this->procedure_model_item_name)->where("pd_id",$this->proc_id)->where("deleted_at","2037-12-31")->update(["deleted_at" => Carbon::now()]);

			$this->proc_id = 0;
		});
	}

	protected function finish_proc(){

	}

}


/**
* 状态生效流程
*/
class status_avail_procedure extends procedure
{

	public $proc_exec = "PROC";

	public $view_page = "dt_edit";

	function pd_info(){
		if (strlen($this->info->pd_comment) > 0) {
			return $this->info->pd_comment;
		}
		return "";
	}

	function proc_boot(){
		if ($this->model->status_control) {
			$this->path = $this->model->status_control->path;
		}
	}
	
	protected function finish_proc(){

		$unique_array = $this->model->item->get_unique();
		foreach ($this->ids as $id) {
			$old_model_data = $this->model->withoutGlobalscopes()->find($id);
			//dd($old_model_data);
			$c = DB::table($this->model_name);
			foreach ($unique_array as $u) {
				$c->where($u,$old_model_data->$u);
			}
			$c->update(["current_version" => null]);
		}

		DB::table($this->model_name)->whereIn("id",$this->ids)->update(["status" => $this->model->status_avail,"current_version" => 1]);

	}
}
/**
* 作废流程
*/
class cancel_procedure extends procedure
{

	public $proc_exec = "CANCEL";

	public $path = array(0 => "编写", 1000 => "批准");

	public $view_page = "dt_edit";

	function pd_info(){
		$html = "信息作废：<br>";
		if ($this->ids !== false) {
			$cancel_info = $this->model->withoutGlobalscopes()->whereIn("id",$this->ids)->get();
		}
		foreach ($cancel_info as $key => $value) {
			$html .= "※ <del> ";
			foreach ($this->model->items_init(9) as $item) {
				$html .= $value[$item]." ";
			}
			$html .= "</del><br>";
		}
		return $html;
	}
	
	protected function finish_proc(){

		$this->model->destroy($this->ids,"weld_syn","deleted_at");

	}
}

/**
* 报告作废流程
*/
class cancel_report_procedure extends cancel_procedure
{

	public $proc_name = "报告作废流程";

	public $view_page = true;

	public $auth = array("1000" => AUTH_EXAM_REPORT_CANCEL);

	public function view_page(){
		$exam_controller = new \App\Http\Controllers\exam();
		$_GET["report_id"] = $this->ids[0];
		return $exam_controller->report_detail();
	}
	
	protected function finish_proc(){

		$this->model->report_delete($this->ids);

	}
}

/**
* 检验组作废流程
*/
class cancel_exam_plan_procedure extends cancel_procedure
{

	public $proc_name = "检验组作废流程";

	public $view_page = true;

	public $auth = array("1000" => AUTH_EXAM_PLAN_CANCEL);

	public function view_page(){
		if (!isset($_GET["id"])) {
			return "信息错误";
		} else if (strpos($_GET["id"],"{") !== false) {
			$_GET["id"] = multiple_to_array($_GET["id"])[0];
		}
		$exam_controller = new \App\Http\Controllers\consignation();
		return $exam_controller->group_detail(true);
	}
	
	protected function finish_proc(){

		$this->model->exam_plan_delete($this->ids);

	}
}


/**
* 检验委托作废流程
*/
class cancel_exam_sheet_procedure extends cancel_procedure
{

	public $proc_name = "检验委托作废流程";

	public $view_page = true;

	public $auth = array("1000" => AUTH_EXAM_SHEET_CANCEL);

	public function view_page(){
		if (!isset($_GET["id"])) {
			return "信息错误";
		} else if (strpos($_GET["id"],"{") !== false) {
			$_GET["sheet_id"] = multiple_to_array($_GET["id"])[0];
		} else {
			$_GET["sheet_id"] = $_GET["id"];
		}
		$exam_controller = new \App\Http\Controllers\consignation();
		return $exam_controller->sheet_detail(true);
	}
	
	protected function finish_proc(){

		$this->model->exam_sheet_delete($this->ids);

	}
}


/**
* 信息变更流程
*/
class alt_procedure extends procedure
{
	public $proc_exec = "ALT";

	public $path = array(0 => "编写", 100 => "审核", 1000 => "批准");

	public $view_page = "dt_alt_info";

	function pd_info(){
		if ($this->pd_info !== "") {
			$info = json_decode($this->pd_info);
			$html = "变更详情：<br>";
			foreach ($info->dirty as $key => $value) {
				$html .= $key.": <del> ".$info->original->$key." </del> => ".$info->dirty->$key." &nbsp ";
			}
			return $html;
		}
	}

	protected function finish_proc(){

		$info = json_decode($this->pd_info);

		$model_name = "\\App\\".$this->model_name;

		$collection = $model_name::find($this->ids[0]);

		$auth_cols = array();//授权编辑的列

		foreach ($info->dirty as $key => $value) {
			if ($info->original->$key == $collection->$key) {
				$collection->$key = $info->dirty->$key;
				$auth_cols[] = $key;
			} else {
				die("原始数据已变化，不能修改");
			}
		}

		if (!$collection->valid_value($collection)) {
			die($collection->msg);
		}
		$collection->authorize_user(Auth::user()->id);//对人员授权
		$collection->authorize_exec($auth_cols);//对编辑的列授权
		if(!$collection->save()){
			die($collection->msg);
		}

	}
	
}
/**
* 水压试验变更流程
*/
class alt_pressure_test_procedure extends alt_procedure
{
	//public $proc_exec = "ALT";

	//public $path = array(0 => "编写", 100 => "审核", 1000 => "批准");

	function pd_info(){
		if ($this->pd_info !== "") {
			$info = json_decode($this->pd_info);
			$html = "水压试验变更：".$info->dirty->pressure_test==1?"无转有":"有转无";
			$html .= "变更原因：".$info->dirty->pressure_test_reason;
			return $html;
		}
	}

	protected function finish_proc(){

		$info = json_decode($this->pd_info);

		if (!isset($info->dirty->pressure_test) || !isset($info->original->pressure_test)) {
			die("变更类型不正确");
		} else if(!(($info->dirty->pressure_test == 1 && $info->original->pressure_test == 0) || ($info->dirty->pressure_test == 0 && $info->original->pressure_test == 1))) {
			die("变更数据不正确");
		}

		$model_name = "\\App\\".$this->model_name;

		$collection = $model_name::whereIn("id",$this->ids)->where("pressure_test",$info->original->pressure_test);

		if ($collection->count() != sizeof($this->ids)) {
			die("原始数据已变化，不能修改");
		}

		$auth_cols = array("level","pressure_test","RT","UT","PT","MT","SA","HB");//授权编辑的列


		$model = new $model_name();

		$grade = array();

		foreach ($this->ids as $id) {
			$clt = $model->find($id);
			$clt->pressure_test = $info->dirty->pressure_test;
			$clt->authorize_user(Auth::user()->id);//对人员授权
			$clt->authorize_exec($auth_cols);//对编辑的列授权
			$cal_result = level_and_rate_cal($clt->medium, $clt->pressure, $clt->temperature, $clt->pressure_test, $clt->ac, $clt->at, $clt->ath, $clt->bc, $clt->bt, $clt->bth, $clt->jtype,$grade);
			foreach (array("level","RT","UT","PT","MT","SA","HB") as $value) {
				$clt->$value = $cal_result[$value]; 
			}
			if (!$clt->save()) {
				die($clt->msg);
			}
		}

	}
	
}
/**
* 指定检验比例流程
*/
class alt_exam_specify_procedure extends alt_procedure
{
	//public $proc_exec = "ALT";

	//public $path = array(0 => "编写", 100 => "审核", 1000 => "批准");

	function pd_info(){
		if ($this->pd_info !== "") {
			$info = json_decode($this->pd_info);
			$html = "指定检验比例原因：".$info->dirty->exam_specify_reason."<br>指定后比例：";
			foreach (array("RT","UT","PT","MT","SA","HB") as $key) {
				$html .= $key."：".$info->dirty->$key." &nbsp; ";
			}
			return $html;
		}
	}

	protected function finish_proc(){

		$info = json_decode($this->pd_info);

		if (!isset($info->dirty->exam_specify) || !isset($info->original->exam_specify)) {
			die("变更类型不正确");
		} else if(!$info->dirty->exam_specify == 1) {
			die("变更数据不正确");
		}

		$model_name = "\\App\\".$this->model_name;


		$auth_cols = array("exam_specify","exam_specify_reason","RT","UT","PT","MT","SA","HB");//授权编辑的列


		$model = new $model_name();

		$grade = array();

		foreach ($this->ids as $id) {
			$clt = $model->find($id);
			$clt->authorize_user(Auth::user()->id);//对人员授权
			$clt->authorize_exec($auth_cols);//对编辑的列授权
			foreach ($auth_cols as $value) {
				$clt->$value = $info->dirty->$value; 
			}
			if (!$clt->save()) {
				die($clt->msg);
			}
		}

	}
	
}
/**
* 删除恢复
*/
class recovery extends procedure
{
	public $proc_exec = "RECOVERY";

	public $path = array(0 => "编写", 100 => "审核", 1000 => "批准");

	public $auth = array(1000 => "weld_qc3");

	function pd_info(){
		return "删除恢复";
	}

	protected function finish_proc(){

		$model_name = "\\App\\".$this->model_name;

		$collection = $model_name::withoutGlobalscopes()->find($this->ids[0]);

		$collection->deleted_at = "2037-12-31";

		if (!$collection->save()) {
			throw new \Exception($collection->msg);
			
		}

	}
	
}
/**
* 任务删除恢复
*/
class tsk_recovery extends recovery
{

	public $view_page = true;

	function view_page(){
		$tsk_controller = new \App\Http\Controllers\tsk();
		$_GET["delete"] = 1;
		$_GET["id"] = multiple_to_array($_GET["id"])[0];
		return $tsk_controller->tsk_detail();
	}

	function pd_info(){
		return "任务删除恢复";
	}

	protected function finish_proc(){

		$model_name = "\\App\\".$this->model_name;

		$collection = $model_name::withoutGlobalscopes()->find($this->ids[0]);

		$collection->deleted_at = "2037-12-31";

		$wj_ids = multiple_to_array($collection->wj_ids);
		$wjs = \App\wj::whereIn("id",$wj_ids)->where("tsk_id",0)->get();

		if (sizeof($wjs) != sizeof($wj_ids)) {
			throw new \Exception("焊口已经变动，无法恢复");
		}

		foreach ($wjs as $wj) {
			$wj->tsk_id = $collection->id;
			$wj->authorize_user("weld_qc3");
			$wj->authorize_exec("tsk_id");
			if (!$wj->save()) {
				throw new \Exception($wj->msg);
			}
		}

		$collection->authorize_user("weld_qc3");
		$collection->authorize_exec("deleted_at");
		if (!$collection->save()) {
			throw new \Exception($collection->msg);
		}

	}
	
}
/**
* 
*/
class model_control
{
	
	public $path = array();
	
	public $procedure_model_name = "procedure";
	
	public $procedure_model = false;

	public $model = false;

	public $model_name = false;
	
	public $col = false;

	public $id = false;

	public $procedure_lock = array();

	public $procedure_exec;

	public $procedure_rollback;


	
	function __construct($model=false)
	{
		$class_name = "\\App\\".$this->procedure_model_name;
		$this->procedure_model = new $class_name();
		$this->model = $model;
		$this->control_boot();
	}

	function control_boot(){}
}
/**
* 
*/
class status_control extends model_control
{
	public $path = array(0 => "编写", 100 => "审核", 1000 => "批准");

	protected $status_avail = 1000;

	public $col = "status";



	function control_boot(){
		$this->model->addGlobalScope("avail",function ($builder) {
            $builder->where($this->model->get_table().".".$this->col, $this->status_avail);
        });
	}
	

	function get_avail_status(){
		return $this->status_avail;
	}

	function valid_status($status){
		if ($this->status_avail == $status) {
			return true;
		}
		return false;
	}
}


/**
* 
*/
class version_control extends model_control
{
	public $col = "version";


	function control_boot(){
		//$this->model->addGlobalScope("current_version",function (Builder $builder) {
            //$builder->max($this->model->get_table().".version")->groupby($this->model->get_table().".version");
        //});
	}


	function version_update(){
		$collection = $this->model->find($this->id);
		$vars = get_object_vars($this->model->item);
		$keys = array_keys($vars);
		foreach ($keys as $key) {
			$this->model->$key = $collection->$key;
		}
		$this->model->version = chr(ord($collection->version)+1);
		//unset($collection->id);
		return $this->model->save();
	}

	function roll_back(){
		$collection = $this->model->find($this->id);
		$collection->version = chr(ord($collection->version)-1);
		return $collection->save();
	}

	
}