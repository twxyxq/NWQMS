<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use table_item;
use table_data;
use depend_map;
//use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \App\procedure\status_control;


class NewSoftDeletingScope extends SoftDeletingScope
{
	 /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getQualifiedDeletedAtColumn(),"2037-12-31 23:59:59");
    }
}
/**
* table model
*/
abstract class table_model extends Model
{
    //softdeletes
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    //table item object. Defined at common/model. 
	public $item;

	//forbidden other user modify
	public $owner_lock = true;

	//locked when in proc
	public $proc_lock = true;

    //datatables data. 
	public $data;

	public $fn_deleting = array();

	public $fn_updating = array();

	public $default_col = array("id","super_code","procedure","status","version","owner","authority","current_version","created_by","created_at","updated_at","deleted_at");

	//excution setting

	public $table_delete = true;

	public $table_update = true;

	//version setting

	public $table_version = false;

	public $version_control = false;

	public $version_array = array();

	//parent setting
	public $parent_lock = false;

	public $parent_name = false;

	public $parent_col = "id";

	public $child_col = "";

	public $parent_scope = true;


	//status control
	public $status_avail = 0;

	public $status_lock = true;//if status_lock is true, when the status_avail is reached, the data is locked

	public $status_control = false;//if use status_control, the value will set The Control Model's name.

	public $status_array = false;

	
	//msg
	public $msg = false;



	public static function bootSoftDeletes()
    {
        static::addGlobalScope(new NewSoftDeletingScope());
    }

    public function scopeOnlySoftDeletes($query)
    {
        return $query->withoutGlobalScopes()->where('deleted_at', '2037-12-31 23:59:59');
    }

    function __construct(array $attributes = []){


    	$classname = explode("\\", get_class($this));
    	$this->table = $classname[sizeof($classname)-1];

    	$this->item = new table_item();
		$this->column();
    	parent::__construct($attributes);

    	//$values = array_values($vars);
    	$default_col = function(Blueprint $table){

    			//default table setting
	    		$table->increments('id');
		        $table->string('super_code',50)->default("");
		        $table->string('version')->default($this->version_init());
		        $table->integer('current_version')->default(1)->nullable();
		        $table->integer('status')->default("0");
		        $table->string('procedure',50)->default("");
		        $table->string('authority')->default("");
		        $table->integer('owner')->default("0");
		        $table->integer('created_by')->default("0");
		        $table->timestamp('deleted_at')->default("2037-12-31 23:59:59");
		        $table->timestamps();
		        //$table->longText("modifyHistory")->nullable();

		        //typical table setting
		    	$vars = get_object_vars($this->item);
		    	$keys = array_keys($vars);
	    		foreach ($keys as $key) {
    				$type = $this->item->$key->type;
    				if ($type != "default") {
    					if (isset($this->item->$key->type_para)) {
    						$para = $this->item->$key->type_para;
    						array_unshift($para, $key);
    					}
	    				if ($this->item->$key->def === false) {
	    					call_user_func_array(array($table,$type),$para);
	    				} else if ($this->item->$key->def == "null") {
	    					call_user_func_array(array($table,$type),$para)->nullable();
	    				} else {
	    					call_user_func_array(array($table,$type),$para)->default($this->item->$key->def);
	    				}
    				}
    				
	    		}

	    		//unique setting
	    		if ($this->item->get_unique()) {
	    			$unique_array = $this->item->get_unique();
	    			//version unique
	    			$version_unique_array = $unique_array;
	    			$version_unique_array[] = "super_code";
	    			$version_unique_array[] = "version";
	    			$version_unique_array[] = "deleted_at";
	    			$table->unique($version_unique_array,"version_unique");
					//current version unique
	    			$current_version_unique_array = $unique_array;
	    			$current_version_unique_array[] = "super_code";
	    			$current_version_unique_array[] = "current_version";
	    			$current_version_unique_array[] = "deleted_at";
	    			$table->unique($current_version_unique_array,"current_version_unique");
	    		}
		    };

		//$this->created_by = 1;
		//dd(Auth::user());
		//class name setting
    	if (!Schema::hasTable($this->table)) {
		    Schema::create($this->table,$default_col);
		}


		//Event setting
		$this->updating(function($data){
			//echo $this->get_table();
			if (!$this->valid_owner($data)) {
				return false;
			}
		});
		$this->deleting(function($data){
			if (!$this->valid_deleting($data)) {
				return false;
			}
		});
		$this->creating(function($data){
			if ($this->valid_value($data)) {
				$data->created_by = Auth::user()->id;
			} else {
				return false;
			}
		});
		//print_r(static::$dispatcher->getListeners());
		
		
    }

    //setting function, must be used where class extented.
    abstract function column();

    function parent($value,$col=false,$model_name=false,$model_col=false){
    	if ($model_name === false) {
    		$model_name = $this->parent_name;
    	} else {
    		$this->parent_name = $model_name;
    	}
    	if ($model_col === false) {
    		$model_col = $this->parent_col;
    	} else {
    		$this->parent_col = $model_col;
    	}
    	if ($col === false) {
    		$col = $this->child_col;
    	} else {
    		$this->child_col = $col;
    	}
    	$this->parent_lock = true;

    	$this->item->$col->only($value);
    	$this->item->$col->input("null");

    	while ($model_name !== false) {
    		$model_name = "\\App\\".$model_name;
	    	$model = new $model_name();
	    	$parent_date = $model->find($value);
	    	if ($this->table_delete) {
	    		$this->table_delete = $model->table_delete;
	    		if ($model->table_delete) {
	    			$this->table_delete = $model->valid_deleting($parent_date);
	    		}
	    		$this->table_update = $this->table_delete;
	    	}
	    	if ($this->table_version) {
	    		$this->table_version = $model->table_version;
	    		if ($model->table_version) {
	    			$this->table_version = $model->valid_version_updating($parent_date);
	    		}
	    	}
	    	if ($this->table_delete === false && $this->table_update === false && $this->table_version === false) {
	    		break;
	    	}
	    	$model_name = $model->parent_name;
    	}
    	


    	if ($this->parent_scope) {
    		$this->addGlobalScope(function (Builder $builder) use ($col,$value) {
                $builder->where($col, $value);
            });
    	}
    	
    }

    function get_table(){
    	return $this->table;
    }

    function item_array($item_array,$para){
    	$except = false;
		$front = false;
		$back = false;
		if (sizeof($para) >= 3) {
			if (is_numeric($para[0]) || is_array($para[0])) {
				$except = $para[0];
			} else {
				$except = array($para[0]);
			}
			if (is_array($para[1])) {
				$front = $para[1];
			} else {
				$front = array($para[1]);
			}
			if (is_array($para[2])) {
				$back = $para[2];
			} else {
				$back = array($para[2]);
			}
		} else if (sizeof($para) > 0) {
			if (is_array($para[0])) {
				if (is_numeric($para[0][0]) || in_array($para[0][0],$item_array)) {
					$except = $para[0];
				} else {
					$front = $para[0];
				}
			} else {
				if (is_numeric($para[0]) || in_array($para[0],$item_array)) {
					if (is_numeric($para[0])) {
						$except = $para[0];
					} else {
						$except = array($para[0]);
					}
				} else {
					$front = array($para[0]);
				}
			}
			if (sizeof($para) > 1) {
				if ($front === false) {
					if (is_array($para[1])) {
						$front = $para[1];
					} else {
						$front = array($para[1]);
					}
				} else {
					if (is_array($para[1])) {
						$back = $para[1];
					} else {
						$back = array($para[1]);
					}
				}
			}
		}
    	if ($except !== false) {
    		if (is_numeric($except)) {
    			$item_array = array_slice($item_array,0,$except);
    		} else if (is_numeric($except[0])) {
    			$new_array = array();
    			foreach ($except as $index) {
    				$new_array[] = $item_array[$index];
    			}
    			$item_array = $new_array;
    		} else {
    			$item_array = array_diff($item_array,$except);
    		}
    	}
    	if ($front !== false) {
    		$item_array = array_merge($front,$item_array);
    	}
    	if ($back !== false) {
    		$item_array = array_merge($item_array,$back);
    	}
    	return $item_array;
    }

    function titles(){
    	$item_array = array();
		foreach ($this->item as $key => $value) {
			$item_array[] = $value->name;
		}
		$para = func_get_args();
		return $this->item_array($item_array,$para);
    }

    function titles_init(){
    	$item_array = array();
		foreach ($this->item as $key => $value) {
			if ($value->input == "init") {
				$item_array[] = $value->name;
			}
		}
		$para = func_get_args();
		return $this->item_array($item_array,$para);
    }

    function items(){
    	$item_array = array();
		foreach ($this->item as $key => $value) {
			$item_array[] = $key;
		}
		$para = func_get_args();
		return $this->item_array($item_array,$para);
    }

    function items_init(){
    	$item_array = array();
		foreach ($this->item as $key => $value) {
			if ($value->input == "init") {
				$item_array[] = $key;
			}
		}
		$para = func_get_args();
		return $this->item_array($item_array,$para);
    }

    public static function destroy($ids)
    {
        // We'll initialize a count here so we will return the total number of deletes
        // for the operation. The developers can then check this number as a boolean
        // type value or get this total count of records deleted for logging, etc.
        $count = 0;

        $ids = is_array($ids) ? $ids : func_get_args();

        $instance = new static;

        // We will actually pull the models from the database table and call delete on
        // each of them individually so that their events get fired properly with a
        // correct set of attributes in case the developers wants to check these.
        $key = $instance->getKeyName();

        //onlySoftDeletes() : only use softDeletes
        foreach ($instance->onlySoftDeletes()->whereIn($key, $ids)->get() as $model) {

        	DB::transaction(function() use ($instance,$model,&$count){
				
				
                //if version control, rollback version
                if($instance->table_version && $model->current_version == 1){
        		 	//$instance->rollback_version($model->id);
        		 	$collection = $instance->find($model->id);
			
					$old_version = $instance->get_old_version($collection->version);
					$old_collection = $instance->Uniquedata($model->id)->where("version",$old_version)->get();

					if ($old_version && !$old_collection->isEmpty()) {

								$collection->current_version = null;

								$collection->save();

								$collection = $instance->find($old_collection[0]->id);

								$collection->current_version = 1;

								$collection->save();

					}
                }

				$model->delete();
				
				$count++;
				//history write
                $history = array();
			 	$history[] = array("key" => "deleted_at", "new" => Carbon::now(), "old" => "2037-12-31 23:59:59");
			 	$modifyHistory = new \App\modify_history();
			 	$modifyHistory->model = static::class;
			 	$modifyHistory->model_id = $model->id;
			 	$modifyHistory->history = json_encode($history);
			 	$modifyHistory->save();

        	});
        }
        //$this->msg = "删除成功（".$count."）";
        return $count;
    }

    protected function performUpdate(Builder $query, array $options = []){
		//echo 111;
		if ($newHistory = $this->newModifyHistory()) {
			$modifyHistory = new \App\modify_history();
		 	$modifyHistory->model = static::class;
		 	$modifyHistory->model_id = $this->id;
		 	$modifyHistory->history = json_encode($newHistory);
		 	$modifyHistory->save();
		}
    	return parent::performUpdate($query,$options);
    }

    function user($builder){
        $builder->LeftJoin('users','users.id',$this->get_table().".created_by");
        return $builder;
    }

    function version_init(){
    	if (sizeof($this->version_array) == 0) {
    		return "A";
    	} else {
    		return $this->version_array[0];
    	}
    }

    function version_update($id){
		//Set column "version" editable, And restrict only this version
		//$this->item->col("version")->type("string");
		if ($this->table_version) {
			$collection = $this->find($id);
			$vars = get_object_vars($this->item);
			$keys = array_keys($vars);
			foreach ($keys as $key) {
				$this->$key = $collection->$key;
			}
			$updated_version = $this->get_updated_vertion($collection->version);
			if ($updated_version) {
				$this->version = $updated_version;
				if ($this->status_avail != 0) {
					$this->current_version = null;
					if ($this->save()) {
						$this->msg = "升版成功";
						return true;
					} else {
						//$this->msg = "执行错误";
						return false;
					}
				} else {
					try{
						DB::transaction(function() use ($collection){
							
							$collection->current_version = null;

							$collection->save();

							$this->save();
						});
					} catch (\Exception $e){
						//$this->msg = "直接升版失败";
						return false;
					}
					$this->msg = "升版成功";
					return true;
				}
			}
			$this->msg = "获取升版信息失败";
			return false;
		} else {
			$this->msg = "该数据不允许升版";
			return false;
		}
    }


    function get_updated_vertion($current_version){
		if (sizeof($this->version_array) == 0) {
			return chr(ord($current_version)+1);
			//dd($this);
		} else {
			if ($pos = array_search($current_version,$this->version_array)) {
				return isset($this->version_array[$pos+1])?$this->version_array[$pos+1]:false;
			} else {
				return false;
			}
		}
    }

    function get_old_version($current_version){
    	if (sizeof($this->version_array) == 0) {
			return chr(ord($current_version)-1);
			//dd($this);
		} else {
			if ($pos = array_search($current_version,$this->version_array)) {
				return isset($this->version_array[$pos-1])?$this->version_array[$pos-1]:false;
			} else {
				return false;
			}
		}
    }

    function status_control(){
    	$this->status_control = new \App\procedure\status_control($this);
    	$this->status_avail = $this->status_control->get_avail_status();
    }

    function version_control(){
    	$this->table_version = true;
    	//$this->version_control = new \App\procedure\version_control($this);
    	$this->addGlobalScope("current_version", function (Builder $builder) {
                $builder->where("current_version", 1);
            });
    }


    function have_depended($id,$col=""){
    	if ($col == "") {
    		return depend_map::have_depended($id,$this->table,"id");
    	} else {
    		return depend_map::have_depended($id,$this->table,$col);
    	}
    	
    }

    function item_walker($fn){
    	$vars = get_object_vars($this->item);
		$keys = array_keys($vars);
		foreach ($keys as $key) {
			if (!in_array($key, array("unique"))) {
				$fn($key);
			}
		}
    }

    function newModifyHistory(){
    	$dirtyArray = $this->getDirty();
    	$keyArray = array_keys($dirtyArray);
    	$history = array();
    	//$history = array(["user" => Auth::user()->id,"time" => Carbon::now()]);
    	foreach ($keyArray as $key) {
    		if ($key == "modifyHistory") {
    			return false;
    		}
    		$history[] = array("key" => $key, "new" => $dirtyArray[$key], "old" => $this->original[$key]);
    	}
    	return $history;
    }

    function valid_owner($data){
    	//必须启用owner_lock,否则不控制
    	if ($this->owner_lock) {
	    	if (is_array($data)) {
	    		if ($data["owner"] == 0) {
	    			$owner = $data["created_by"];
	    		} else {
	    			$owner = $data["owner"];
	    		}
	    	} else if (is_object($data)) {
	    		if (isset($data->owner) && isset($data->created_by)) {
		    		if ($data->owner == 0) {
		    			$owner = $data->created_by;
		    		} else {
		    			$owner = $data->owner;
		    		}
	    		}
	    		if (isset($data->original["owner"]) && isset($data->original["created_by"])) {
		    		if ($data->original["owner"] == 0) {
		    			$owner = $data->original["created_by"];
		    		} else {
		    			$owner = $data->original["owner"];
		    		}
	    		}
	 			if (!isset($owner)) {//没有找到owner默认不允许操作
	 				return false;
	 			}
	    	} else if (is_numeric($data)) {
	    		$collection = $this->find($data);
	    		if ($collection["owner"] == 0) {
	    			$owner = $collection["created_by"];
	    		} else {
	    			$owner = $collection["owner"];
	    		}
	    	} else {
	    		return false;
	    	}
    		if ($owner != Auth::user()->id) {
	    		return false;
	    	}
	    	return true;
    	}
    	return true;
    	
    }

    function get_obj_data($data,$col){
    	if (is_array($data)) {
			return $data[$col];
		} else if (is_object($data)) {
			return $data->$col;
		} else {
			return false;
		}
    }

    function valid_value($data){
    	//只验证改变的值
    	$dirtyArray = $data->getDirty();

    	//判断值的合法性
    	foreach ($dirtyArray as $key => $value) {
    		if (!in_array($key,$this->default_col) && !$this->item->valid_value($key,$value)) {
    			$this->msg = "'".$key.":".$value."'输入值不合法";
    			return false;
    		}
    	}

    	//判断值的唯一性
    	return $this->valid_unique($dirtyArray);
    }

    function valid_unique($dirtyArray=array()){
    	//判断值的唯一性
    	if ($unique_array = $this->item->get_unique()) {
	    	if ($unique_array !== false && sizeof(array_intersect($unique_array, array_keys($dirtyArray))) > 0) {
	    		$result = $this->select(["id"]);
	    		$unique_text = "";

	    		//where condition create 
	    		foreach ($unique_array as $col) {
	    			if (isset($dirtyArray[$col])) {
	    				$where_txt = $dirtyArray[$col];
	    			} else if (isset($data->original[$col])) {
	    				$where_txt = $data->original[$col];
	    			} else if ($this->item->$col->def) {
						$where_txt = $this->item->$col->def;
	    			} else {
	    				return true;
	    			}
	    			$unique_text .= ",".$this->item->$col->name;
	    			$result->where($col,$where_txt);
	    		}
	    		//add default unique
	    		if (isset($dirtyArray["version"])) {
	    			$result->where("version",$dirtyArray["version"]);
	    		}
	    		

	    		//only use softdeleted
	    		$result->onlySoftDeletes();

	    		//empty is ok
	    		if (!$result->get()->isEmpty()) {
	    			$this->msg = "'".substr($unique_text,1)."'不允许有重复的值";
	    			return false;
	    		}
	    		return true;
	    	}
	    }
	    return true;
    }


    function valid_deleting($data){
    	if ($this->table_delete) {
			if (is_array($data)) {
				$id = $data["id"];
				$status = $data["status"];
				$procedure = $data["procedure"];
				$current_version = $data["current_version"];
			} else if (is_object($data)) {
				$id = $data->id;
				$status = $data->status;
				$procedure = $data->procedure;
				$current_version = $data->current_version;
			} else if(is_numeric($data)){
				$collection = $this->find($data);
				$id = $collection->id;
				$status = $collection->status;
				$procedure = $collection->procedure;
				$current_version = $collection->current_version;
			} else {
				return false;
			}
			//条件：必须是owner，必须没有被使用。没有进行状态控制，没有正在进行的流程且状态不属于生效状态，必须是当前版本
			if ($this->valid_owner($data)) {
				if (!$this->item->is_used($id)) {
					if ($this->status_control === false || (strlen($procedure) == 0 && !$this->status_control->valid_status($status)) && $current_version == 1) {
						return true;
					} else {
						$this->msg = "该项目正在流程中";
					}
				} else {
					$this->msg = "已经被使用";
				}
			} else {
				$this->msg = "您没有授权";
			}
		}
    	return false;
    }

    function valid_status_check($data){

    	if ($this->status_control) {
    		if ($this->valid_owner($data) && strlen($this->get_obj_data($data,"procedure")) == 0) {
    			return true;
    		} else {
    			$proc = new \App\procedure\procedure($this->get_obj_data($data,"procedure"));
    			if ($proc->get_current_owner() == Auth::user()->id) {
    				return true;
    			}
    		}
    	}
    	return false;
    }

    //条件：必须有版本控制，必须不能删除，必须不在流程中，必须是当前版本
    function valid_version_updating($data){
		if (is_array($data)) {
			$procedure = $data["procedure"];
			$current_version = $data["current_version"];
		} else if (is_object($data)) {
			$procedure = $data->procedure;
			$current_version = $data->current_version;
		} else {
			return false;
		}
    	if ($this->table_version && $this->valid_owner($data) && !$this->valid_deleting($data) && strlen($procedure) == 0 && $current_version == 1) {
	    	return true;
	    }
    	return false;
    }

    function is_version_updating($data){
    	if ($data["current_version"] == 1) {
    		$data["version"] = $this->get_updated_vertion($data["version"]);
    		return !$this->valid_unique($data);
    	}
    	return false;
    }
   
    function fn_deleting($fn){
    	$this->fn_deleting[] = $fn;
    	$this->deleting($fn);
    }

    function fn_updating($fn){
    	$this->fn_updating[] = $fn;
    	$this->updating($fn);
    }
    /*
	function table_del($item,$join=""){
		$this->data = new table_data($item,$this,$join);
		$this->data->index(function($row){
			//if ($this->valid_owner()) {
				# code...
			//}
			if ($this->valid_deleting($row[0])) {
				return "【<a href=\"###\" onclick=\"dt_delete('".$this->table."',".$row[0].")\">删除</a>】";
			} else {
				return "<span style='color:lightgrey'>[已使用]</span>";
			}
		});
		//$this->data->col("setting_name",function($value){
			//return "<a href='###'>".$value."</a>";
		//});
        return $this->data;
	}
	*/
	function table_data($item,$join=""){
		$this->data = new table_data($item,$this,$join);
	}


	function scopeAvailable($query){
		return $query->where($this->get_table().".status",$this->status_avail);
	}

	function scopeCurrentVersion($query){
		return $query->where($this->get_table().".current_version",1);
	}

	function scopeUniquedata($query,$id){
		if ($unique_array = $this->item->get_unique()) {
			$collection = $this->find($id);
			$query->where("id","<>",$id);
			foreach ($unique_array as $u) {
				$query->where($u,$collection->$u);
			}
			return $query->orderby("version","desc");
		}
		//设置一个肯定不成立 的式子
		return $query->whereNull("id");
		//return $query->where
		//return $query->where($this->get_table().".current_version",$id);
		
		
	}



}
