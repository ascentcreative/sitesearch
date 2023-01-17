<?php

namespace AscentCreative\SiteSearch;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Routing\Router;

use Laravel\Scout\EngineManager;
use AscentCreative\SiteSearch\Engines\EloquentEngine;   

class SiteSearchServiceProvider extends ServiceProvider
{
  public function register()
  {
    //
   
    $this->mergeConfigFrom(
        __DIR__.'/../config/sitesearch.php', 'sitesearch'
    );

  }

  public function boot()
  {

    if ($this->app->runningInConsole()) {
        $this->commands([
            \AscentCreative\SiteSearch\Commands\IndexModel::class,
        ]);
    }

    $this->loadViewsFrom(__DIR__.'/../resources/views', 'sitesearch');

    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

    $this->app->make(EngineManager::class)->extend('eloquent', function () {
        return new EloquentEngine();
    });
    
  }

  

  // register the components
  public function bootComponents() {

  }




  

    public function bootPublishes() {

      $this->publishes([
        __DIR__.'/../assets' => public_path('vendor/ascentcreative/sitesearch'),
    
      ], 'public');

      $this->publishes([
        __DIR__.'/config/sitesearch.php' => config_path('sitesearch.php'),
      ]);

    }



}