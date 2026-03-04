<?php

namespace App\DTO;

class TopChallengeDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $titre,
        public readonly int $exercicesCount
    ) {}
}
