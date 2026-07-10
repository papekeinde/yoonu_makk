<?php

namespace App\Enums;

enum StatutDemandeAdhesion: string
{
    case EnAttente = 'en_attente';
    case Approuvee = 'approuvee';
    case Rejetee   = 'rejetee';
}
