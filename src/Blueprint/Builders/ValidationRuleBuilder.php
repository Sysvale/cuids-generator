<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

class ValidationRuleBuilder
{
    public static function build(array $fields): array
    {
        return collect($fields)->map(fn ($type) => self::rule($type))->toArray();
    }

    private static function rule(string $type): string
    {
        $rules = match ($type) {
            'string' => 'string|max:255',
            'integer' => 'integer',
            'boolean' => 'boolean',
            'date', 'timestamp' => 'date',
            default => 'string',
        };

        return "required|{$rules}";
    }
}
