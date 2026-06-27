<?php

namespace App\Enums;

enum StatutRendezVous: string
{
    case EnAttente = 'en_attente';
    case Accepte   = 'accepte';
    case Refuse    = 'refuse';
    case Termine   = 'termine';
    case Annule    = 'annule';
}
