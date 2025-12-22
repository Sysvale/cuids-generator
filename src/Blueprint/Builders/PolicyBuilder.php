<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

class PolicyBuilder
{
    public function build(string $model): array
    {
        return [
            "{$model}Policy" => [
                'model' => $model,
                'methods' => [
                    'view',
                    'create',
                    'update',
                    'delete',
                ],
            ],
        ];
    }
}