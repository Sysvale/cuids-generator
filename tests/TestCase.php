<?php

namespace Sysvale\CuidsGenerator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sysvale\CuidsGenerator\Providers\CuidsGeneratorServiceProvider;
use Blueprint\BlueprintServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BlueprintServiceProvider::class,
            CuidsGeneratorServiceProvider::class,
        ];
    }
}