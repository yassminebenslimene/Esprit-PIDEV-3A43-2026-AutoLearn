<?php

namespace App\Enum;

enum TypeEvenement: string
{
    case ATELIER = 'atelier';
    case CONFERENCE = 'conference';
    case HACKATHON = 'hackathon';
    case COMPETITION = 'competition';
    case FORMATION = 'formation';
}
