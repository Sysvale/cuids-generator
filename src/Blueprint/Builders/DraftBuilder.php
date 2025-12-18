<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

use Illuminate\Support\Str;
use Sysvale\CuidsGenerator\Blueprint\Builders\ControllerBuilder;
use Sysvale\CuidsGenerator\Blueprint\Builders\RequestBuilder;

class DraftBuilder
{
    public function __construct(
        protected RequestBuilder $requestBuilder,
        protected ControllerBuilder $controllerBuilder
    ) {}

    public function build(string $model, array $fields, array $relationships): array
    {
        $draft = [
            'models' => [
                $model => $fields,
            ],
            'requests' =>$this->requestBuilder->build($model, $fields),
            'controllers' => $this->controllerBuilder->build($model),
        ];

        if (! empty($relationships)) {
            $draft['models'][$model]['relationships'] =
                $this->mapRelationships($relationships);
        }

        return $draft;
    }

    private function mapRelationships(array $relationships): array
    {
        $mapped = [];

        foreach ($relationships as $model => $type) {
            if (! isset($mapped[$type])) {
                $mapped[$type] = [];
            }

            $mapped[$type][] = $model;
        }

        return collect($mapped)
            ->map(fn ($models) => implode(',', $models))
            ->toArray();
    }

}
