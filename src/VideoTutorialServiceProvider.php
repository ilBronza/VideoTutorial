<?php

namespace IlBronza\VideoTutorial;

use IlBronza\VideoTutorial\Http\Middleware\VideoTutorialMiddlewareRolesPermissions;
use IlBronza\VideoTutorial\View\Composers\ContextualVideoHelp;
use IlBronza\VideoTutorial\View\Composers\ContextualVideoUpload;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class VideoTutorialServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'videotutorial');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'videotutorial');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/videotutorial.php');

        View::composer('uikittemplate::app', ContextualVideoHelp::class);

        if ($this->app->bound('menu')) {
            // Load after AccountManager so the profile exists in both menu renderers.
            $providers = config('menu.serviceProviders', []);
            $providers[] = ContextualVideoUpload::class;
            config([
                'menu.serviceProviders' => $providers,
            ]);
            View::composer('uikittemplate::app', ContextualVideoUpload::class);
        }

        $this->app['router']->aliasMiddleware('videotutorial.roles', VideoTutorialMiddlewareRolesPermissions::class);

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/videotutorial.php', 'videotutorial');

        // Register the service the package provides.
        $this->app->singleton('videotutorial', function ($app) {
            return new VideoTutorial;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['videotutorial'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/videotutorial.php' => config_path('videotutorial.php'),
        ], 'videotutorial.config');

        $this->publishes([
            __DIR__.'/../database/migrations' => base_path('database/migrations'),
        ], 'videotutorial.migrations');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/ilbronza'),
        ], 'videotutorial.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/ilbronza'),
        ], 'videotutorial.assets');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ilbronza'),
        ], 'videotutorial.lang');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
