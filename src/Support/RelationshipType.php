<?php

namespace Sysvale\CuidsGenerator\Support;

enum RelationshipType: string
{
    case HAS_ONE = 'hasOne';
    case HAS_MANY = 'hasMany';
    case BELONGS_TO = 'belongsTo';
    case BELONGS_TO_MANY = 'belongsToMany';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}