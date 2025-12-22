<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class FormFieldPostProcessor
{
    public function handle(string $model): void
    {
        $lowerModel = Str::lower($model);
        $pluralModel = Str::plural($lowerModel);

        $path = resource_path("js/features/{$pluralModel}/constants/{$lowerModel}FormFields.ts");

        if (! File::exists($path)) return;

        $content = File::get($path);

        $content = str_replace('"', "'", $content);
        $content = trim($content) . PHP_EOL;

        File::put($path, $content);
    }
}