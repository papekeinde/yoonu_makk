<?php

namespace App\Enums;

enum Genre: string
{
    case Femme = 'femme';
    case Homme = 'homme';

    public function label(): string
    {
        return match($this) {
            self::Femme => 'Femme',
            self::Homme => 'Homme',
        };
    }
}
