<?php

namespace Sysvale\CuidsGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Sysvale\CuidsGenerator\Console\Commands\CuidsGenerateCommand;
use Sysvale\CuidsGenerator\Blueprint\PostProcessorRunner;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\ControllerPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\CollectionPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\FactoryPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\Mongo\MongoModelTransformer;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\RequestPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\TestPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\FormFieldPostProcessor;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\RoutePostProcessor;
use Illuminate\Support\Facades\File;

class CuidsGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerPostProcessors();
        $this->configureBlueprintStubs();

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
                $app->make(CollectionPostProcessor::class),
                $app->make(ControllerPostProcessor::class),
                $app->make(RoutePostProcessor::class),
                $app->make(FormFieldPostProcessor::class),
            ]);
        });
    }

    private function configureBlueprintStubs(): void
    {
        if ($this->app->runningInConsole()) {
            $targetPath = base_path('stubs/blueprint');
            $sourcePath = realpath(__DIR__ . '/../../stubs/blueprint');

            if ($sourcePath && !file_exists($targetPath)) {
                if (!is_dir(base_path('stubs'))) {
                    mkdir(base_path('stubs'), 0755, true);
                }

                if (function_exists('symlink')) {
                    symlink($sourcePath, $targetPath);
                } else {
                    File::copyDirectory($sourcePath, $targetPath);
                }
            }
        }
    }

    public function boot(): void
    {
    }
}
