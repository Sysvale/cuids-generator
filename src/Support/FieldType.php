<?php

namespace Sysvale\CuidsGenerator\Support;

enum FieldType: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case DATE = 'date';
    case TIMESTAMP = 'timestamp';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
