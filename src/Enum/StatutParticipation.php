<?php

namespace App\Enum;

enum StatutParticipation: string
{
    case EN_ATTENTE = 'En attente';
    case ACCEPTE = 'Accepté';
    case REFUSE = 'Refusé';
}
