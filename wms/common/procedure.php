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
	

	public $path = array();
	
	public $procedure_model_name = "procedure";

	public $procedure_model_item_name = "procedure_item";
	
	public $procedure_model = false;

	public $model = false;

	public $model_name = false;
	
	public $col = false;

	public $ids = false;

	public $procedure_lock = array();

	public $procedure_exec;

	public $procedure_rollback;

	public $msg;

	public $info = false;

	public $item_info = false;

	public $item_history = false;

	public $pd_class = false;

	public $proc_id = 0;


	
	function __construct($proc_id,$model_name=false,$ids=false)
	{
		//procedure model
		$class_name = "\\App\\".$this->procedure_model_name;
		$this->procedure_model = new $class_name();

		//procedure class name
		$this->pd_class = get_class($this);

		//procedure item model
		$class_name = "\\App\\".$this->procedure_model_item_name;
		$this->procedure_item_model = new $class_name();

		//load or get value
		if (is_numeric($proc_id)) {
			$this->load_proc($proc_id);
		} else {
			$this->model_name = $model_name;
			$this->ids = is_array($ids)?$ids:multiple_to_array($ids);
		}

		//model instance
		if ($this->model_name) {
			$class_name = "\\App\\".$this->model_name;
			$this->model = new $class_name();
			if ($this->model->status_control) {
				$this->path = $this->model->status_control->path;
			}
		}

		//other boot
		$this->proc_boot();
	}

	function proc_boot(){}


	function load_proc($id){
		$this->proc_id = $id;
		$proc = $this->procedure_model->find($id);
		if (!is_null($proc) && $proc->pd_class == $this->pd_class) {
			$this->info = $proc;
			$this->model_name = $this->info->pd_model;
			$this->ids = multiple_to_array($this->info->pd_ids);
			//load item
			$proc_item = $this->procedure_item_model->where("pd_id",$id)->orderby("version","asc")->get();
			$this->item_info = $proc_item;
			//load history
			$proc_history = $this->procedure_item_model->select(["pdi_title","pdi_comment","procedure_item.updated_at","name",DB::raw("IF(pdi_action=1,'通过',IF(pdi_action=-1,'退回','')) as action")])->leftJoin("users",'users.id',"procedure_item.owner")->withoutGlobalscopes()->where("pd_id",$id)->whereNotNull("procedure_item.updated_at")->orderby("updated_at","asc")->get();
			$this->item_history = $proc_history;

		} else {
			$this->msg = "载入失败";
			return false;
		}

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

	function generate_pd_name(){
		return "新流程";
	}

	function create_proc(){

		DB::transaction(function()
		{
			//create new procedure
		    $proc_id = DB::table($this->procedure_model_name)->insertGetId(["pd_name" => $this->generate_pd_name(),"pd_model" => $this->model_name,"pd_ids" => array_to_multiple($this->ids),"pd_class" => get_class($this),"pd_executed" => "PROC"]);

		    //lock model item
		    DB::table($this->model_name)->whereIn("id",$this->ids)->update(["procedure" => $proc_id]);

		    //create procedure item by "path" of control model
		    if (sizeof($this->path) > 0) {
		    	$i = 0;
		    	foreach ($this->path as $key => $value) {
		    		if ($i == 0) {
		    			$owner = Auth::user()->id;
		    			$current_version = 1;
		    		} else {
		    			$owner = 0;
		    			$current_version = null;
		    		}
		    		DB::table($this->procedure_model_item_name)->insert(["version" => $key,"current_version" => $current_version,"pdi_title" => $value,"created_by" => Auth::user()->id,"owner" => $owner,"pd_id" => $proc_id]);
		    		$i++;
		    	}
		    } else {
		    	DB::table($this->procedure_model_item_name)->insert(["pdi_title" => "编写","created_by" => Auth::user()->id,"owner" => Auth::user()->id,"pd_id" => $proc_id]);
		    }
		    

		    $this->load_proc($proc_id);
		});


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
				//try{
					DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["current_version" => null,"pdi_action" => 1,"pdi_comment" => $comment,"updated_at" => Carbon::now()]);

					DB::table($this->procedure_model_item_name)->where("id",$next_proc->id)->update(["current_version" => 1,"owner" => $next_owner]);

					if (sizeof($next_owners) >= 2) {
						for ($i=1; $i < min(sizeof($next_owners),sizeof($next_procs)); $i++) { 
							DB::table($this->procedure_model_item_name)->where("id",$next_procs[$i]->id)->update(["owner" => $next_owners[$i]]);
						}
					}
				//} catch (\Exception $e){
					//dd(1);
				//}
			});
		} else if($current_proc !== false && $next_proc === false && $current_proc->owner == Auth::user()->id){

			DB::transaction(function() use ($comment,$current_proc)
			{
				DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["current_version" => null,"pdi_action" => 1,"pdi_comment" => $comment,"updated_at" => Carbon::now()]);

				//unlock model
				DB::table($this->model_name)->whereIn("id",$this->ids)->update(["procedure" => ""]);

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

				DB::table($this->procedure_model_item_name)->where("id",$prev_proc->id)->update(["deleted_at" => Carbon::now()]);

				DB::table($this->procedure_model_item_name)->insert(["version" => $prev_proc->version,"current_version" => 1,"pdi_title" => $prev_proc->pdi_title,"created_by" => Auth::user()->id,"owner" => $prev_proc->owner,"pd_id" => $prev_proc->pd_id,"created_at" => Carbon::now()]);

				DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["pdi_comment" => $comment,"pdi_action" => -1,"updated_at" => Carbon::now(),"deleted_at" => Carbon::now()]);

				DB::table($this->procedure_model_item_name)->insert(["version" => $current_proc->version,"current_version" => null,"pdi_title" => $current_proc->pdi_title,"created_by" => Auth::user()->id,"owner" => $current_proc->owner,"pd_id" => $current_proc->pd_id,"created_at" => Carbon::now()]);

			});
		} else {

			$current_proc = $this->get_current_proc();

			DB::table($this->procedure_model_item_name)->where("id",$current_proc->id)->update(["current_version" => null,"pdi_action" => -1,"pdi_comment" => $comment,"updated_at" => Carbon::now()]);

			$this->cancel_proc();
		}
		
	}

	function unpass_proc(){
		
	}

	function restart_proc(){

	}

	function cancel_proc(){
		DB::transaction(function(){
			//unlock model item
			DB::table($this->model_name)->whereIn("id",$this->ids)->update(["procedure" => ""]);

			//delete proc
			DB::table($this->procedure_model_name)->where("id",$this->proc_id)->update(["deleted_at" => Carbon::now()]);
			
			//delete proc_item
			DB::table($this->procedure_model_item_name)->where("pd_id",$this->proc_id)->where("deleted_at","2037-12-31 23:59:59")->update(["deleted_at" => Carbon::now()]);

			$this->proc_id = 0;
		});
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
		//DB::table($this->model_name)->whereIn("id",$this->ids)->update(["status" => $this->model->status_avail]);
	}

}


/**
* 
*/
class status_avail_procedure extends procedure
{
	
	
	
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
		$this->model->addGlobalScope("avail",function (Builder $builder) {
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