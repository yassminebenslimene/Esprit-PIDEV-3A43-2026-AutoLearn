<?php

namespace App\DTO;

class ActiveStudentDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly int $count
    ) {}
}
