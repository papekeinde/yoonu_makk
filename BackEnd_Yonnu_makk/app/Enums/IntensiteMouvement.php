<?php

namespace App\Enums;

enum IntensiteMouvement: string
{
    case Leger  = 'leger';
    case Modere = 'modere';
    case Fort   = 'fort';

    public function label(): string
    {
        return match($this) {
            self::Leger  => 'Léger',
            self::Modere => 'Modéré',
            self::Fort   => 'Fort',
        };
    }
}
