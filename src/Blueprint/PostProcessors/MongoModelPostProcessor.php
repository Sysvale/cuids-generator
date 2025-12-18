<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MongoModelPostProcessor implements PostProcessor
{
    // Responsável por ajustes gerais (Model base + connection + collection)
    public function handle(string $model): void
    {
        $path = app_path("Models/{$model}.php");

        if (! File::exists($path)) return;

        $content = File::get($path);

        $content = str_replace(
            'use Illuminate\Database\Eloquent\Model;',
            'use MongoDB\Laravel\Eloquent\Model;',
            $content
        );

        if (! str_contains($content, 'MongoDB\\Laravel\\Eloquent\\Model')) {
            return;
        }

        if (! str_contains($content, 'protected $connection')) {
            $collection = Str::snake(Str::pluralStudly($model));

            $content = preg_replace(
                '/class\s+' . $model . '\s+extends\s+Model\s*\{/',
                "class {$model} extends Model\n{\n".
                "    protected \$connection = 'mongodb';\n\n".
                "    protected \$collection = '{$collection}';",
                $content
            );
        }

        File::put($path, $content);
    }
}
