<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;

class ControllerPostProcessor
{
    public function handle(string $model): void
    {
        $path = base_path("app/Http/Controllers/{$model}Controller.php");

        if (!File::exists($path)) return;

        $content = File::get($path);

        $content = str_replace(
            'use Illuminate\Http\Request;',
            '',
            $content
        );

        $content = preg_replace(
            [
                '/\bRequest\s+\$request,\s*/',
                '/\(\s*Request\s+\$request\s*\)/',
            ],
            [
                '',
                '()',
            ],
            $content
        );



        File::put($path, $content);
    }
}
