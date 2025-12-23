<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RoutePostProcessor
{
    public function handle(string $model): void
    {
        $path = base_path('routes/web.php');

        if (!File::exists($path)) {
            return;
        }

        $content = File::get($path);

        $content = $this->transformToApiResource($content, $model);

        $content = $this->ensureControllerImport($content, $model);

        File::put($path, $content);
    }

    private function transformToApiResource(string $content, string $model): string
    {
        $controller = Str::studly($model) . 'Controller';
        $resourceName = Str::kebab(Str::pluralStudly($model));

        $pattern = '/Route::resource\s*\(\s*[\'"][^\'"]+[\'"]\s*,\s*\\\\?App\\\\Http\\\\Controllers\\\\' . $controller . '::class\s*\)(?:\s*->(?:except|only)\s*\([^)]+\))?\s*;/';

        $replacement = "Route::apiResource('/{$resourceName}', {$controller}::class);";

        return preg_replace($pattern, $replacement, $content);
    }

    private function ensureControllerImport(string $content, string $model): string
    {
        $controller = Str::studly($model) . 'Controller';
        $useLine = "use App\Http\Controllers\\{$controller};";

        if (str_contains($content, $useLine)) {
            return $content;
        }

        $pattern = '/use App\\\\Http\\\\Controllers\\\\.*;/';
        
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $lastMatch = end($matches[0]);
            $lastMatchText = $lastMatch[0];
            $lastMatchOffset = $lastMatch[1];

            return substr_replace(
                $content, 
                $lastMatchText . "\n" . $useLine, 
                $lastMatchOffset, 
                strlen($lastMatchText)
            );
        }

        if (preg_match_all('/use .*;/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $lastMatch = end($matches[0]);
            return substr_replace(
                $content, 
                $lastMatch[0] . "\n" . $useLine, 
                $lastMatch[1], 
                strlen($lastMatch[0])
            );
        }

        return str_replace('<?php', "<?php\n\n{$useLine}", $content);
    }
}