<?php

namespace App\DTO;

class CourseProgressDTO
{
    public function __construct(
        public readonly int $coursId,
        public readonly int $completedChapters
    ) {}
}
