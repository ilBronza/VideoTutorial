<?php

namespace IlBronza\VideoTutorial;

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
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ilbronza');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'ilbronza');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

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
