<?php

namespace App\Enum;

enum StatutEvenement: string
{
    case PLANIFIE = 'Plannifié';
    case EN_COURS = 'En cours';
    case PASSE = 'Passé';
    case ANNULE = 'Annulé';
}
