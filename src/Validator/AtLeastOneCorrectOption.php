<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AtLeastOneCorrectOption extends Constraint
{
    public string $message = 'Une question doit avoir au moins une option correcte.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
