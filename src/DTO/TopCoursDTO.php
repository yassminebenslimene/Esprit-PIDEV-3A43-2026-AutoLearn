<?php

namespace App\DTO;

class TopCoursDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $titre,
        public readonly int $chaptersCount
    ) {}
}
