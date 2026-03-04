<?php

namespace App\DTO;

class ActiveAdminDTO
{
    public function __construct(
        public readonly string $username,
        public readonly int $count
    ) {}
}
