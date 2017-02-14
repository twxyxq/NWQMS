<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
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
/**
* softDelete, Change deleted_at to "0"
*/


class table_model1 extends Model
{
    //softdeletes
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    //table item object. Defined at common/model. 
    public $item;

    //forbidden other user modify
    public $owner_lock = true;

    //datatables data. 
    public $data;

    public $fn_deleting = array();

    public $fn_updating = array();

    public $default_col = array("id","status","version","created_by","created_at","updated_at","deleted_at");

    

    //parent setting
    public $parent_lock = false;

    public $parent_name = false;

    public $parent_col = "id";

    //version array
    public $version_array = array();

    //status control
    public $status_avail = 0;

    public $status_control = false;//if use status_control, the value will set The Control Model's name.

   

    function __construct(array $attributes = []){
        $this->item = new table_item();
        parent::__construct($attributes);
        
        echo 1;
        //$values = array_values($vars);
        $default_col = function(Blueprint $table){

                //default table setting
                $table->increments('id');
                $table->string('version')->default($this->version_init());
                $table->integer('status')->default(0);
                $table->integer('owner')->default(0);
                $table->integer('created_by')->default(0);
                $table->timestamp('deleted_at')->default("2037-12-31 23:59:59");
                $table->timestamps();
                //$table->longText("modifyHistory")->nullable();

                //typical table setting
                $vars = get_object_vars($this->item);
                $keys = array_keys($vars);
                foreach ($keys as $key) {
                    $type = $this->item->$key->type;
                    if ($type != "default") {
                        if ($this->item->$key->def === false) {
                            $table->$type($key);
                        } else if ($this->item->$key->def == "null") {
                            $table->$type($key)->nullable();
                        } else {
                            $table->$type($key)->default($this->item->$key->def);
                        }
                    }
                    
                }

                //unique setting
                if ($this->item->get_unique()) {
                    $unique_array = $this->item->get_unique();
                    $unique_array[] = "version";
                    $unique_array[] = "deleted_at";
                    $table->unique($unique_array);
                }
            };

        //$this->created_by = 1;
        //dd(Auth::user());
        //class name setting
        $classname = explode("\\", get_class($this));
        $this->table = $classname[sizeof($classname)-1];
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table,$default_col);
        }


        //Event setting
        
        $this->deleting(function($data){
            if ($this->valid_owner($data->created_by)) {
                if (!$this->valid_deleting($data->id)) {
                    return false;
                }
            } else {
                return false;
            }
        });
        $this->deleted(function($data){
            $history = array();
            $history[] = array("key" => "deleted_at", "new" => Carbon::now(), "old" => "2037-12-31 23:59:59");
            $modifyHistory = new \App\modify_history();
            $modifyHistory->model = $this->get_table();
            $modifyHistory->model_id = $data->id;
            $modifyHistory->history = json_encode($history);
            $modifyHistory->save();
        });
        $this->creating(function(){
            $this->created_by = Auth::user()->id;
        });
        $this->updating(function($data){
            if (!$this->valid_owner($data->original["created_by"])) {
                return false;
            }
            if ($newHistory = $this->newModifyHistory($data)) {
                $modifyHistory = new \App\modify_history();
                $modifyHistory->model = $this->get_table();
                $modifyHistory->model_id = $data->id;
                $modifyHistory->history = json_encode($newHistory);
                $modifyHistory->save();
            }
        });
        
    }

    //setting function, must be used where class extented.
   // abstract function column();

    function parent($col,$value,$model=false,$model_col="id"){
        $this->item->$col->only($value);
        $this->parent_name = $model;
        $this->parent_col = $model_col;

        static::addGlobalScope(function (Builder $builder) use ($col,$value) {
                $builder->where($col, $value);
            });
    }

    function get_table(){
        return $this->table;
    }



    function user(){
        return $this->LeftJoin('users','users.id',$this->get_table().".created_by");
    }

    function version_init(){
        if (sizeof($this->version_array) == 0) {
            return "A";
        } else {
            return $this->version_array[0];
        }
    }

    function status_control(status_control $sc){
        $this->status_control = $sc;
        $this->status_avail = $sc->get_avail_status();
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

    function newModifyHistory($data){
        $dirtyArray = $data->getDirty();
        $keyArray = array_keys($dirtyArray);
        $history = array();
        //$history = array(["user" => Auth::user()->id,"time" => Carbon::now()]);
        foreach ($keyArray as $key) {
            if ($key == "modifyHistory") {
                return false;
            }
            $history[] = array("key" => $key, "new" => $dirtyArray[$key], "old" => $data->original[$key]);
        }
        return $history;
    }

    function valid_owner($id){
        if ($this->owner_lock) {
            if ($id != Auth::user()->id) {
                return false;
            }
            return true;
        }
        return true;
        
    }
   
    function fn_deleting($fn){
        $this->fn_deleting[] = $fn;
        $this->deleting($fn);
    }

    function fn_updating($fn){
        $this->fn_updating[] = $fn;
        $this->updating($fn);
    }

    function valid_deleting($id){
        return !$this->item->is_used($id);
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
        return $query->where("status",$this->status_avail);
    }



}
/**
* 
*/
class test extends table_model
{
    
   function column(){
    
   }
}
