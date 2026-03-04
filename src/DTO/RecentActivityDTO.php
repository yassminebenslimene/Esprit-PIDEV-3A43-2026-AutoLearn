<?php

namespace App\DTO;

class RecentActivityDTO
{
    public function __construct(
        public readonly string $date,
        public readonly int $count
    ) {}
}
