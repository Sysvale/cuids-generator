<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

use Illuminate\Support\Str;

class ControllerBuilder
{
    public function build(string $model): array
    {
        $resource = Str::camel($model);

        return [
            "{$model}Controller" => [
                'index' => [
                    'query' => 'all',
                ],
                'store' => [
                    'validate' => "Store{$model}Request",
                    'save' => $resource,
                ],
                'show' => [
                    'query' => 'find:id',
                ],
                'update' => [
                    'validate' => "Update{$model}Request",
                    'save' => $resource,
                ],
                'destroy' => [
                    'delete' => $resource,
                ],
            ],
        ];
    }
}
