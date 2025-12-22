<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class RoutePostProcessor
{
    public function handle(string $model): void
    {
        $path = base_path('routes/web.php');

        if (! File::exists($path)) {
            return;
        }

        $content = File::get($path);

        $content = $this->removeBlueprintResourceRoute($content, $model);

        $updated = $this->registerBackEndRoute($content, $model);

        File::put($path, $updated);
    }

    private function registerBackEndRoute(string $content, string $model): string
    {
        $lines = explode("\n", $content);

        $controller = Str::studly($model).'Controller';
        $routeName  = Str::kebab(Str::pluralStudly($model));

        $useLine   = "use App\Http\Controllers\\{$controller};";
        $routeLine = "    Route::apiResource('/{$routeName}', {$controller}::class);";

        $foundImportMarker  = false;
        $foundRegisterMarker = false;

        $result = [];

        foreach ($lines as $line) {
            if (str_contains($line, '@endcontrollerimport')) {
                $foundImportMarker = true;

                if (! str_contains($content, $useLine)) {
                    $result[] = $useLine;
                }
            }

            if (str_contains($line, '@endroutergister')) {
                $foundRegisterMarker = true;

                if (! str_contains($content, $routeLine)) {
                    if (! empty($result) && trim(end($result)) !== '') {
                        $result[] = '';
                    }

                    $result[] = $routeLine;
                }
            }

            $result[] = $line;
        }

        if (! $foundImportMarker) {
            throw new RuntimeException(
                'Não foi possível encontrar o marcador @endcontrollerimport em routes/web.php'
            );
        }

        if (! $foundRegisterMarker) {
            throw new RuntimeException(
                'Não foi possível encontrar o marcador @endroutergister em routes/web.php'
            );
        }

        return implode("\n", $result);
    }

    private function removeBlueprintResourceRoute(string $content, string $model): string
    {
        $controller = Str::studly($model) . 'Controller';

        $pattern = sprintf(
            '/Route::resource\s*\(\s*[\'"][^\'"]+[\'"]\s*,\s*App\\\\Http\\\\Controllers\\\\%s::class\s*\)\s*(->(?:only|except)\([^)]+\))?\s*;/m',
            $controller
        );

        return preg_replace($pattern, '', $content);
    }
}
