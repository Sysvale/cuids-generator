<?php

namespace Sysvale\CuidsGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Sysvale\CuidsGenerator\Console\Commands\CuidsGenerateCommand;
use Sysvale\CuidsGenerator\Blueprint\PostProcessorRunner;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoCastCleanupPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoModelPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoSoftDeletesPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\MongoVisibilityPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\TestPostProcessor;

class CuidsGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerPostProcessors();

        if ($this->app->runningInConsole()) {
            $this->commands([
                CuidsGenerateCommand::class,
            ]);
        }
    }

    private function registerPostProcessors(): void
    {
        $this->app->singleton(PostProcessorRunner::class, function ($app) {
            return new PostProcessorRunner([
                $app->make(MongoModelPostProcessor::class),
                $app->make(MongoCastCleanupPostProcessor::class),
                $app->make(MongoSoftDeletesPostProcessor::class),
                $app->make(MongoVisibilityPostProcessor::class),
            ]);
        });
    }

    public function boot(): void
    {}
}
