<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

class ValidationRuleBuilder
{
    public static function required(array $fields): array
    {
        return self::build($fields, false);
    }

    public static function optional(array $fields): array
    {
        return self::build($fields, true);
    }

    private static function build(array $fields, bool $partial): array
    {
        return collect($fields)->map(fn ($type) => self::rule($type, $partial))->toArray();
    }

    private static function rule(string $type, bool $partial): string
    {
        $rules = match ($type) {
            'string' => 'string|max:255',
            'integer' => 'integer',
            'boolean' => 'boolean',
            'date', 'timestamp' => 'date',
            default => 'string',
        };

        return $partial ? "sometimes|{$rules}" : "required|{$rules}";
    }
}
