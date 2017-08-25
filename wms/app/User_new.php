<?php

namespace App;

use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Auth\Authenticatable; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

require_once "table_model.php";

class User extends table_model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    function column(){

        $this->item->col("code")->type("string")->name("工号");
        $this->item->col("name")->type("string")->name("姓名");

        $this->item->col("email")->type("string")->name("邮箱");
        $this->item->col("auth")->type("string")->name("授权")->def("null");
        $this->item->col("password")->type("string")->name("密码");
        $this->item->col("remember_token")->type("string",100)->name("记住密码")->def("null");

        $this->item->unique("code");
    }


    /*
    function __construct(array $attributes = []){

        parent::__construct($attributes);

        if (!Schema::hasTable("users")) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code')->unique();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('auth')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->integer('created_by')->default(0);
            });
        }

        if (!Schema::hasTable("password_resets")) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token')->index();
                $table->timestamp('created_at')->nullable();
            });
        }
    }*/

}
