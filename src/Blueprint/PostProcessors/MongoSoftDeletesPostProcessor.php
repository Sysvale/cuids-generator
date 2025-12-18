<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;

class MongoSoftDeletesPostProcessor implements PostProcessor
{
    // Responsável por adicionar softDelte do Mongo
    public function handle(string $model): void
    {
        $path = app_path("Models/{$model}.php");

        if (! File::exists($path)) return;

        $content = File::get($path);

        if (! str_contains($content, 'MongoDB\\Laravel\\Eloquent\\SoftDeletes')) {
            $content = str_replace(
                "use MongoDB\\Laravel\\Eloquent\\Model;\n",
                "use MongoDB\\Laravel\\Eloquent\\Model;\nuse MongoDB\\Laravel\\Eloquent\\SoftDeletes;\n",
                $content
            );
        }

        if (! str_contains($content, 'use SoftDeletes;')) {
            $content = preg_replace(
                '/use HasFactory;/',
                "use HasFactory;\n    use SoftDeletes;",
                $content
            );
        }

        File::put($path, $content);
    }
}
