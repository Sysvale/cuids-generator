<?php

namespace Sysvale\CuidsGenerator\BluePrint\PostProcessors;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class FormFieldPostProcessor
{
    public function handle(string $model): void
    {
        $lowerModel = Str::lower($model);
        $pluralModel = Str::plural($lowerModel);

        $path = base_path("js/features/{$pluralModel}/constants/{$lowerModel}FormFields.ts");

        dump('path', $path);
        dump('Existe? ', File::exists($path));
        if (! File::exists($path)) return;

        dump('encontrou o arquivo');

        $content = File::get($path);

        $content = str_replace('"', "'", $content);
        $content = trim($content) . PHP_EOL;

        File::put($path, $content);
    }
}