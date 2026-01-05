<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;
use Sysvale\CuidsGenerator\Blueprint\PostProcessors\PostProcessor;

class RequestPostProcessor implements PostProcessor
{
    public function handle(string $model): void
    {
        $files = [
            app_path("Http/Requests/{$model}StoreRequest.php"),
            app_path("Http/Requests/{$model}UpdateRequest.php"),
        ];

        foreach ($files as $path) {
            if (! File::exists($path)) {
                continue;
            }

            $content = File::get($path);

            $content = preg_replace(
                "/'([a-z0-9_]+_id)'\s*=>\s*\[(.*?)'integer'(.*?)\]/",
                "'$1' => [$2'string'$3]",
                $content
            );

            $content = preg_replace(
                "/exists:([a-zA-Z0-9_]+),id/",
                "exists:$1,_id",
                $content
            );

            File::put($path, $content);
        }
    }
}
