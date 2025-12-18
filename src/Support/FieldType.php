<?php

namespace Sysvale\CuidsGenerator\Support;

enum FieldType: string
{
    case ARRAY = 'array';
    case DATE = 'date';
    case BOOLEAN = 'boolean';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case STRING = 'string';
    case TIMESTAMP = 'timestamp';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
