<?php

namespace Modules\SPTransfer\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\SPTransfer\Http\Controllers\Admin\AdminController;
use Modules\SPTransfer\Http\Controllers\Frontend\FrontendController;

/**
 * Register the routes required for your module here
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
     * @param  Router $router
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

    /**
     *
     */
    protected function registerWebRoutes(): void
    {
        $config = [
            'as'         => 'sptransfer.',
            'namespace'  => $this->namespace.'\Frontend',
            'middleware' => ['web'],
        ];

        Route::group($config, function () {
            Route::group(['middleware' => 'auth', 'prefix' => 'sptransfer'], function () {
                Route::get('/', 'FrontendController@index')->name('index');
                Route::post('/', 'FrontendController@store')->name('store');
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
                // Route::post('/deny', 'AdminController@deny')->name('deny');
                // Route::post('/delete', 'AdminController@delete')->name('delete');
                Route::post('/storeSettings', 'AdminController@storeSettings')->name('storeSettings');
            });
        });
    }

    
}
