<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TestPostProcessor
{
    public function handle(string $model): void
    {
        $path = base_path("tests/Feature/Http/Controllers/{$model}ControllerTest.php");

        $stubPath = __DIR__ . '/../Stubs/controller.test.stub';
        
        if (!File::exists($stubPath)) {
            return;
        }

        $stub = File::get($stubPath);

        $replaces = [
            '{{ model }}' => $model,
            '{{ pluralModel }}' => Str::plural($model),
            '{{ route }}' => Str::kebab(Str::plural($model)),
            '{{ table }}' => Str::snake(Str::plural($model)),
            '{{ variable }}' => Str::camel($model),
        ];

        $content = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $stub
        );

        $content = str_replace(
            "use App\Models\\" . $model . ";",
            "use Database\Factories\\" . $model . "Factory;",
            $content
        );

        File::put($path, $content);
    }
}
