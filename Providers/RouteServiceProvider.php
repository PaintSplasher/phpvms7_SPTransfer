<?php

namespace Modules\SPTransfer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Modules\SPTransfer\Http\Controllers\Admin\AdminController;

/**
 * Register the routes required for your module here.
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = 'Modules\SPTransfer\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @param Router $router
     *
     * @return void
     */
    public function before(Router $router)
    {
        //
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        $this->registerWebRoutes();
        $this->registerAdminRoutes();
    }

    protected function registerWebRoutes(): void
    {
        $config = [
            'as'         => 'sptransfer.',
            'namespace'  => $this->namespace.'\Frontend',
            'middleware' => ['web'],
        ];

        Route::group($config, function () {
            Route::group(['middleware' => 'auth', 'prefix' => 'sptransfer'], function () {
                Route::get('/hub', 'HubFrontendController@index')->name('hub.index'); 
                Route::post('/hub', 'HubFrontendController@store')->name('hub.store');        
                Route::get('/airline', 'AirlineFrontendController@index')->name('airline.index'); 
                Route::post('/airline', 'AirlineFrontendController@store')->name('airline.store');
            });
        });
        
    }

    protected function registerAdminRoutes(): void
    {
        $config = [
            'as'         => 'admin.',
            'prefix'     => 'admin/',
            'namespace'  => $this->namespace.'\Admin',
            'middleware' => ['web', 'role:admin'],
        ];

        Route::group($config, function () {
            Route::group(['as' => 'sptransfer.', 'prefix' => 'sptransfer/'], function () {
                Route::get('/', 'AdminController@index')->name('index');
                Route::post('/update', 'AdminController@update')->name('update');
                Route::post('/update_airline', 'AdminController@update_airline')->name('update_airline');
                Route::post('/storeSettings', 'AdminController@storeSettings')->name('storeSettings');
            });
        });
    }
}
