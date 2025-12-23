<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

use Blueprint\Models\Policy;
use Illuminate\Support\Str;
use Sysvale\CuidsGenerator\Blueprint\Builders\ControllerBuilder;
use Sysvale\CuidsGenerator\Blueprint\Builders\RequestBuilder;
use Sysvale\CuidsGenerator\Blueprint\Builders\PolicyBuilder;

class DraftBuilder
{
    public function __construct(
        protected RequestBuilder $requestBuilder,
        protected ControllerBuilder $controllerBuilder,
    ) {
    }

    public function build(string $model, array $fields, array $relationships): array
    {
        $formattedModelsFields = $this->mapFields($fields, $relationships);

        $draft = [
            'models' => [
                $model => $formattedModelsFields,
            ],
            'requests' => $this->requestBuilder->build($model, $fields),
            'controllers' => $this->controllerBuilder->build($model),
        ];

        if (! empty($relationships)) {
            $draft['models'][$model]['relationships'] =
                $this->mapRelationships($relationships);
        }

        return $draft;
    }

    private function mapFields(array $fields, array $relationships): array
    {
        $formatted = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $definition = $field['type'];

            if ($field['nullable'] ?? false) {
                $definition .= ' nullable';
            }

            $formatted[$name] = $definition;
        }

        foreach ($relationships as $relatedModel => $type) {
            if ($type === 'belongsTo') {
                $foreignKey = Str::snake($relatedModel) . '_id';
                $formatted[$foreignKey] = 'string';
            }
        }

        return $formatted;
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
