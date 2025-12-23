<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;

class FactoryPostProcessor
{
    public function handle(string $model): void
    {
        $path = base_path("database/factories/{$model}Factory.php");

        if (! File::exists($path)) {
            return;
        }

        $content = File::get($path);

        $content = str_replace(
            'use Illuminate\Support\Str;',
            '',
            $content
        );

        $content = preg_replace(
            "/'([a-z0-9_]+_id)'\s*=>\s*([\w\\\]+)::factory\(\),/i",
            "'$1' => $2::factory()->create()->id,",
            $content
        );

        File::put($path, $content);
    }
}
