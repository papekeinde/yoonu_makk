<?php

namespace App\Enums;

enum TypeSymptome: string
{
    case BouffeesChaleur      = 'bouffees_chaleur';
    case SueursNocturnes      = 'sueurs_nocturnes';
    case TroublesSommeil      = 'troubles_sommeil';
    case Fatigue              = 'fatigue';
    case Stress               = 'stress';
    case Anxiete              = 'anxiete';
    case Irritabilite         = 'irritabilite';
    case DouleursArticulaires = 'douleurs_articulaires';
    case SecheresseVaginale   = 'secheresse_vaginale';
    case TroublesUrinaires    = 'troubles_urinaires';

    // Symptômes grossesse
    case NauseeVomissement    = 'nausee_vomissement';
    case BrurlureEstomac      = 'brulure_estomac';
    case DouleursDorsales     = 'douleurs_dorsales';
    case OedemesJambes        = 'oedemes_jambes';
    case ConstipationGrossesse = 'constipation_grossesse';
    case VertigesGrossesse    = 'vertiges_grossesse';
    case ContractionsBenignes = 'contractions_benignes';
    case MouvementsReduits    = 'mouvements_reduits';
    case SaignementLegers     = 'saignement_legers';
    case PerteEaux            = 'perte_eaux';

    public function label(): string
    {
        return match($this) {
            self::BouffeesChaleur       => 'Bouffées de chaleur',
            self::SueursNocturnes       => 'Sueurs nocturnes',
            self::TroublesSommeil       => 'Troubles du sommeil',
            self::Fatigue               => 'Fatigue',
            self::Stress                => 'Stress',
            self::Anxiete               => 'Anxiété',
            self::Irritabilite          => 'Irritabilité',
            self::DouleursArticulaires  => 'Douleurs articulaires',
            self::SecheresseVaginale    => 'Sécheresse vaginale',
            self::TroublesUrinaires     => 'Troubles urinaires',
            self::NauseeVomissement     => 'Nausées / Vomissements',
            self::BrurlureEstomac       => 'Brûlures d\'estomac',
            self::DouleursDorsales      => 'Douleurs dorsales',
            self::OedemesJambes         => 'Œdèmes des jambes',
            self::ConstipationGrossesse => 'Constipation',
            self::VertigesGrossesse     => 'Vertiges',
            self::ContractionsBenignes  => 'Contractions bénignes',
            self::MouvementsReduits     => 'Mouvements bébé réduits',
            self::SaignementLegers      => 'Saignements légers',
            self::PerteEaux             => 'Perte des eaux',
        };
    }

    public function estSymptomeGrossesse(): bool
    {
        return in_array($this, [
            self::NauseeVomissement,
            self::BrurlureEstomac,
            self::DouleursDorsales,
            self::OedemesJambes,
            self::ConstipationGrossesse,
            self::VertigesGrossesse,
            self::ContractionsBenignes,
            self::MouvementsReduits,
            self::SaignementLegers,
            self::PerteEaux,
        ]);
    }
}
