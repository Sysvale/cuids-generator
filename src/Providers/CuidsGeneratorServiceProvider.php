<?php

namespace Sysvale\CuidsGenerator\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Sysvale\CuidsGenerator\Console\Commands\CuidsGenerateCommand;
use Sysvale\CuidsGenerator\Blueprint\PostProcessorRunner;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\FactoryPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\Mongo\MongoModelTransformer;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\RequestPostProcessor;
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
                $app->make(MongoModelTransformer::class),
                $app->make(FactoryPostProcessor::class),
                $app->make(RequestPostProcessor::class),
                $app->make(TestPostProcessor::class),
            ]);
        });
    }

    public function boot(): void
    {}
}
