<?php

namespace Sysvale\CuidsGenerator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sysvale\CuidsGenerator\CuidsGeneratorServiceProvider;
use Sysvale\CuidsGenerator\Providers\CuidsGeneratorServiceProvider as ProvidersCuidsGeneratorServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ProvidersCuidsGeneratorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Configurações básicas de ambiente, se necessário
    }
}