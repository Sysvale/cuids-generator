<?php

namespace Sysvale\CuidsGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Sysvale\CuidsGenerator\Console\Commands\CuidsGenerateCommand;
use Sysvale\CuidsGenerator\Blueprint\PostProcessorRunner;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoModelPostProcessor;

class CuidsGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CuidsGenerateCommand::class,
            ]);
        }
    }

    public function boot(): void
    {}
}
