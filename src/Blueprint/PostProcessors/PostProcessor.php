<?php

namespace Sysvale\CuidsGenerator\Blueprint\PostProcessors;

interface PostProcessor
{
    public function handle(string $model): void;
}
