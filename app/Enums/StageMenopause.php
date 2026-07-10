<?php

namespace App\Enums;

enum StageMenopause: string
{
    case Perimenopause  = 'perimenopause';
    case Menopause      = 'menopause';
    case Postmenopause  = 'postmenopause';

    public function label(): string
    {
        return match($this) {
            self::Perimenopause => 'Péri-ménopause',
            self::Menopause     => 'Ménopause',
            self::Postmenopause => 'Post-ménopause',
        };
    }
}
