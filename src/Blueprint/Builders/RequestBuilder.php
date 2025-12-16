<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

use Sysvale\CuidsGenerator\Blueprint\Builders\ValidationRuleBuilder;

class RequestBuilder
{
    public function build(string $model, array $fields): array
    {
        return [
            "Store{$model}Request" => ValidationRuleBuilder::required($fields),
            "Update{$model}Request" => ValidationRuleBuilder::optional($fields),
        ];
    }
}
