<?php

namespace Sysvale\CuidsGenerator\Tests\Feature;

use Sysvale\CuidsGenerator\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Sysvale\CuidsGenerator\Blueprint\Builders\FrontEndBuilder;

class CiTest extends TestCase
{
    /** @test */
    public function it_can_detect_the_command()
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('cuids:generate', $commands);
    }

    /** @test */
    public function it_can_access_the_builders_classes()
    {
        $builder = new FrontEndBuilder();
        
        $this->assertInstanceOf(FrontEndBuilder::class, $builder);
    }
}