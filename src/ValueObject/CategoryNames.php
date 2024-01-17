<?php

namespace App\ValueObject;

enum CategoryNames: string
{
    case FOOD = 'food';
    case SPORT = 'sport';
    case HEALTH = 'health';

    public static function getAllowedValues(): array
    {
        return [
            self::FOOD->value,
            self::SPORT->value,
            self::HEALTH->value,
        ];
    }
}
