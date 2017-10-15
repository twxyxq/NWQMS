<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class User extends Authenticatable
{
    use Notifiable;

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
                $table->string('email')->unique();
                $table->string('auth')->nullable();
                $table->string('mobile')->nullable();
                $table->string('wechat_id')->nullable();
                $table->string('avatar')->nullable();
                $table->string('default_key')->nullable();
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

}
