<?php

namespace Sysvale\CuidsGenerator\Blueprint;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class BlueprintWriter
{
    public function write(array $draft): string
    {
        $path = base_path('draft.yaml');

        File::put(
            $path,
            Yaml::dump($draft, 4, 2)
        );

        return $path;
    }
}
