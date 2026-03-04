<?php

namespace App\DTO;

class PostReactionCountDTO
{
    public function __construct(
        public readonly string $type,
        public readonly int $count
    ) {}
}
