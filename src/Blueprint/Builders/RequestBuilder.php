<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

use Sysvale\CuidsGenerator\Blueprint\Builders\ValidationRuleBuilder;

class RequestBuilder
{
    public function build(string $model, array $fields): array
    {
        $rules = ValidationRuleBuilder::build($fields);

        return [
            "Store{$model}Request" => $rules,
            "Update{$model}Request" => $rules,
        ];
    }
}
