<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors\Mongo;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\PostProcessor;

class MongoModelTransformer implements PostProcessor
{
    public function handle(string $model): void
    {
        $path = app_path("Models/{$model}.php");

        if (!File::exists($path)) return;

        $content = File::get($path);

        $content = $this->setupBaseStructure($content, $model);

        $content = $this->applySoftDeletes($content);

        $content = $this->cleanupCasts($content);

        $content = $this->setupVisibility($content);

        File::put($path, $content);
    }

    protected function setupBaseStructure(string $content, string $model): string
    {
        $content = str_replace(
            'use Illuminate\Database\Eloquent\Model;',
            'use MongoDB\Laravel\Eloquent\Model;',
            $content
        );

        if (!str_contains($content, 'protected $connection')) {
            $collection = Str::snake(Str::pluralStudly($model));
            
            $content = preg_replace(
                '/(class\s+' . $model . '\s+extends\s+Model\s*\{)/',
                "$1\n    protected \$connection = 'mongodb';\n    protected \$collection = '{$collection}';",
                $content
            );
        }

        return $content;
    }

    protected function applySoftDeletes(string $content): string
    {
        if (!str_contains($content, 'MongoDB\\Laravel\\Eloquent\\SoftDeletes')) {
            $content = str_replace(
                "use MongoDB\Laravel\Eloquent\Model;",
                "use MongoDB\Laravel\Eloquent\Model;\nuse MongoDB\Laravel\Eloquent\SoftDeletes;",
                $content
            );
        }

        if (!str_contains($content, 'use SoftDeletes;')) {
            $content = preg_replace(
                '/(use HasFactory;)/',
                "$1\n    use SoftDeletes;",
                $content
            );
        }

        return $content;
    }

    protected function cleanupCasts(string $content): string
    {
        return preg_replace(
            '/protected function casts\(\): array\s*\{[\s\S]*?\n\s*\}/',
            '',
            $content
        );
    }

    protected function setupVisibility(string $content): string
    {
        if (!str_contains($content, 'protected $appends')) {

            $content = preg_replace(
                '/(protected\s+\$fillable\s*=\s*\[[^\]]*\];)/s',
                "$1\n\n    protected \$appends = ['id'];\n    protected \$hidden = ['_id'];",
                $content
            );
        }

        return $content;
    }
}