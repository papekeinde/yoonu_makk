<?php

namespace App\Enums;

enum TypeNotification: string
{
    case RappelRendezVous        = 'rappel_rendez_vous';
    case NouveauContenu          = 'nouveau_contenu';
    case StatutRendezVous        = 'statut_rendez_vous';
    case General                 = 'general';
    case RappelSuiviGrossesse    = 'rappel_suivi_grossesse';
    case AlerteGrossesse         = 'alerte_grossesse';
    case FelicitationsGrossesse  = 'felicitations_grossesse';
}
