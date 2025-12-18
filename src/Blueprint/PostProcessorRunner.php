<?php

namespace Sysvale\CuidsGenerator\Blueprint;

class PostProcessorRunner
{
    public function __construct(
        protected iterable $processors
    ) {}

    public function run(string $model): void
    {
        foreach ($this->processors as $processor) {
            $processor->handle($model);
        }
    }
}
