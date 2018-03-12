<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Blueprint;

class User extends Authenticatable
{
    use Notifiable;

    //datatables数据
    public $data;
    public $default_col = array();

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'email', 'password', 'default_key', 'mobile', 'avatar', "auth"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function __construct(array $attributes = []){

        parent::__construct($attributes);

        if (!Schema::hasTable("users")) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code')->unique();
                $table->string('name');
                $table->string('email');
                $table->string('auth')->nullable();
                $table->string('mobile')->nullable();
                $table->string('wechat_id')->nullable();
                $table->string('avatar')->nullable();
                $table->string('default_key')->nullable();
                $table->integer('user_level')->default(0);
                $table->string('user_org')->default("N/A");
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->integer('created_by')->default(0);
                //$table->timestamp('created_at')->nullable();
                $table->timestamp('deleted_at')->default('2037-12-31')->nullable();
            });
        }

        if (!Schema::hasTable("password_resets")) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token')->index();
                $table->timestamp('created_at')->nullable();
            });
        }
    }


    function get_table(){
        return "users";
    }


    function user_list(){
        $this->data = new \table_data(array("id","code","name","auth","created_at","user_org"),$this);
        $this->data->where("user_level","<=",Auth::user()->user_level);
        $this->data->where(function($query){
                $query->orWhere("user_org",Auth::user()->user_org);
                $query->orWhere("user_org","N/A");
            });
        $this->data->col("code",function($value,$raw_data){
            return "<a href=\"###\" onclick=\"table_flavr('/panel/user_auth?id=".$raw_data["id"]."')\">".$value."</a>";
        });
        return $this->data->render();
    }

}
