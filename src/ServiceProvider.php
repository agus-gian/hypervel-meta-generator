<?php

namespace AugustPermana\HypervelMetaGenerator;

use Hypervel\Support\ServiceProvider as BaseServiceProvider;
use AugustPermana\HypervelMetaGenerator\Commands\MakeMetaModel;
use AugustPermana\HypervelMetaGenerator\Commands\CleanOrphanedMeta;

/**
 * Service provider for the Hypervel Meta Generator package.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register commands for console
        $this->commands([
            MakeMetaModel::class,  // Command to generate metadata system
            CleanOrphanedMeta::class,  // Command to clean orphaned metadata
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
