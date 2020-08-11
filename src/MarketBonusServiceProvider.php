<?php

namespace ArtemiyKudin\Bonus;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class MarketBonusServiceProvider extends ServiceProvider
{
    public function boot(Filesystem $filesystem)
    {
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('marketBonus.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_packages_market_bonus_table.php.stub' => $this->getMigrationFileName($filesystem),
            ], 'migrations');

            $this->publishes([
                __DIR__ . '/../lang' => resource_path('/lang'),
            ], 'lang');
        }
        $this->loadFactoriesFrom(__DIR__.'/../database/factories');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->registerModelBindings();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php',
            'marketBonus'
        );
    }

    protected function registerModelBindings()
    {
        $config = $this->app->config['marketBonus.models'];

        if (!$config) {
            return;
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path.'*_create_packages_market_bonus_table.php');
            })->push($this->app->databasePath()."/migrations/{$timestamp}_create_packages_market_bonus_table.php")
            ->first();
    }
}
