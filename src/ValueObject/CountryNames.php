<?php

namespace App\ValueObject;

enum CountryNames: string
{
    case POLAND = 'Poland';
    case GERMANY = 'Germany';
    case SPAIN = 'Spain';
    case PORTUGAL = 'Portugal';

    public static function getAllowedValues(): array
    {
        return [
            self::POLAND->value,
            self::GERMANY->value,
            self::SPAIN->value,
            self::PORTUGAL->value,
        ];
    }
}
