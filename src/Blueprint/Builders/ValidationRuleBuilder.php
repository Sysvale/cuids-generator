<?php

namespace Sysvale\CuidsGenerator\Blueprint\Builders;

class ValidationRuleBuilder
{
    public static function build(array $fields): array
    {
        return collect($fields)->mapWithKeys(fn($field) => [
            $field['name'] => self::buildRules($field)
        ])->toArray();
    }

    public static function buildForUpdate(array $fields, string $table, string $primaryKey = 'id'): array
    {
        return collect($fields)->mapWithKeys(function ($field) use ($table, $primaryKey) {
            $name = $field['name'];
            $rules = self::buildRules($field);

            $rules = str_replace(
                "unique:{$table},{$name}",
                "unique:{$table},{$name},{{$primaryKey}}",
                $rules
            );

            return [$name => $rules];
        })->toArray();
    }

    private static function buildRules(array $field): string
    {
        $rules = [];

        $rules[] = ($field['nullable'] ?? false) ? 'nullable' : 'required';

        if ($typeRules = self::getTypeRules($field['type'])) {
            $rules[] = $typeRules;
        }

        $extraRules = $field['validation_rules'] ?? [];
        $rules = array_merge($rules, (array) $extraRules);

        return implode('|', array_filter($rules));
    }

    private static function getTypeRules(string $type): ?string
    {
        return match ($type) {
            'string', 'text' => 'string',
            'integer', 'bigInteger' => 'integer',
            'boolean' => 'boolean',
            'date', 'datetime',
            'timestamp' => 'date',
            'decimal', 'float' => 'numeric',
            default => null,
        };
    }
}
