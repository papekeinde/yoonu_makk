<?php

namespace App\Enums;

enum TypeProfilFemme: string
{
    case Menopause = 'menopause';
    case Grossesse = 'grossesse';

    public function label(): string
    {
        return match($this) {
            self::Menopause => 'Ménopause',
            self::Grossesse => 'Grossesse',
        };
    }
}
