<?php

namespace App\Enums;

enum TypeProfilFemme: string
{
    case Menopause = 'menopause';
    case Grossesse = 'grossesse';
    case General   = 'general';

    public function label(): string
    {
        return match($this) {
            self::Menopause => 'Ménopause',
            self::Grossesse => 'Grossesse',
            self::General   => 'Général',
        };
    }
}
