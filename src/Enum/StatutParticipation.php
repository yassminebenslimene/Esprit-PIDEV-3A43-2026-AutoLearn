<?php

namespace App\Enum;

enum StatutParticipation: string
{
    case EN_ATTENTE = 'en_attente';
    case ACCEPTEE = 'acceptee';
    case REJETEE = 'rejetee';
    case ANNULEE = 'annulee';
}
