<?php

namespace App\Providers;

use App\wj_base_model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //wj_base_model::deleting(function($wj_base_model){
            //if ($wj_base_model->has_depended()) {
                //return false;
            //}
        //});
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
