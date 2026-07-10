<?php

namespace App\Enums;

enum TypeRecommandation: string
{
    case Nutrition               = 'nutrition';
    case ActivitePhysique        = 'activite_physique';
    case HygieneVie              = 'hygiene_vie';
    case Consultation            = 'consultation';
    case Prenatal                = 'prenatal';
    case Allaitement             = 'allaitement';
    case PreparationAccouchement = 'preparation_accouchement';
}
