<?php

namespace Sysvale\CuidsGenerator\Support;

enum FieldType: string
{
    case ARRAY = 'array';
    case BOOLEAN = 'boolean';
    case DATE = 'date';
    case FLOAT = 'float';
    case INTEGER = 'integer';
    case STRING = 'string';
    case TIMESTAMP = 'timestamp';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
