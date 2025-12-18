<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;

class MongoCastCleanupPostProcessor implements PostProcessor
{
    // Remove casts do SQL que o bluePrint gera por padrão
    public function handle(string $model): void
    {
        $path = app_path("Models/{$model}.php");

        if (! File::exists($path)) return;

        $content = File::get($path);

        $content = preg_replace(
            '/protected function casts\(\): array\s*\{[\s\S]*?\n\s*\}/',
            '',
            $content
        );

        File::put($path, $content);
    }
}
