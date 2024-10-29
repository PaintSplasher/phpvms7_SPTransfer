<?php

namespace Modules\SPTransfer\Providers;

use App\Contracts\Modules\ServiceProvider;

/**
 * @package $NAMESPACE$
 */
class AppServiceProvider extends ServiceProvider
{
    private $moduleSvc;

    protected $defer = false;

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->moduleSvc = app('App\Services\ModuleService');

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerLinks();

        // Uncomment this if you have migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
        
        // Register the widget namespace
        app('arrilot.widget-namespaces')->registerNamespace('SPTransfer', 'Modules\SPTransfer\Widgets');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }

    /**
     * Add module links here
     */
    public function registerLinks(): void
    {
        // Show this link if logged in
        $this->moduleSvc->addFrontendLink('HUB Transfer', '/sptransfer', 'fas fa-exchange-alt', $logged_in=true);

        // Admin links:
        $this->moduleSvc->addAdminLink('SPTransfer', '/admin/sptransfer', 'pe-7s-back');
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('sptransfer.php'),
        ], 'sptransfer');

        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'sptransfer');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/sptransfer');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([$sourcePath => $viewPath],'views');

        $this->loadViewsFrom(array_merge(array_filter(array_map(function ($path) {
            $path = str_replace('default', setting('general.theme'), $path);
            // Check if the directory exists before adding it
            if (file_exists($path.'/modules/sptransfer') && is_dir($path.'/modules/sptransfer'))
              return $path.'/modules/sptransfer';

            return null;
        }, \Config::get('view.paths'))), [$sourcePath]), 'sptransfer');
        
        // Register the views namespace
        $this->loadViewsFrom($sourcePath, 'SPTransfer');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/SPTransfer');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'SPTransfer');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'SPTransfer');
        }
    }


}
