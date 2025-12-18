<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;

class MongoVisibilityPostProcessor implements PostProcessor
{
    // Responsável por ajustar appends e hidden
    public function handle(string $model): void
    {
        $path = app_path("Models/{$model}.php");

        if (! File::exists($path)) {
            return;
        }

        $content = File::get($path);

        if (! str_contains($content, 'protected $appends')) {
            $content = preg_replace(
                '/protected \$fillable\s*=\s*\[[^\]]*\];/s',
                "$0\n\n    protected \$appends = ['id'];\n    protected \$hidden = ['_id'];",
                $content
            );
        }

        File::put($path, $content);
    }
}
