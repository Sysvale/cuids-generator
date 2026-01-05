<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;

class CollectionPostProcessor
{
    public function handle(string $model): void
    {
        $path = base_path("app/Http/Resources/{$model}Collection.php");

        $stubPath = __DIR__ . '/../Stubs/collection.stub';

        if (!File::exists($stubPath)) {
            return;
        }

        $stub = File::get($stubPath);

        $replaces = [
            '{{ model }}' => $model,
        ];

        $content = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $stub
        );

        File::put($path, $content);
    }
}
