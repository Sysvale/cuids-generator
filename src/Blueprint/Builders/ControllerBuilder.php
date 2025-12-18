<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

use Illuminate\Support\Str;

class ControllerBuilder
{
    public function build(string $model): array
    {
        $singularVar = Str::camel($model);
        $pluralVar = Str::camel(Str::plural($model));

        return [
            "{$model}Controller" => [
                'index' => [
                    'query' => "all",
                    'resource' => "paginate:{$pluralVar}",
                ],
                'store' => [
                    'validate' => "Store{$model}Request",
                    'save' => $singularVar,
                ],
                'show' => [
                    'resource' => $singularVar,
                ],
                'update' => [
                    'validate' => "Update{$model}Request",
                    'update' => $singularVar,
                ],
                'destroy' => [
                    'delete' => $singularVar,
                ],
            ],
        ];
    }
}
