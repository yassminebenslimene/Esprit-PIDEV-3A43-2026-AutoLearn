<?php

namespace App\Validator;

use App\Entity\Question;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AtLeastOneCorrectOptionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AtLeastOneCorrectOption) {
            throw new UnexpectedTypeException($constraint, AtLeastOneCorrectOption::class);
        }

        if (!$value instanceof Question) {
            return;
        }

        // Vérifier si la question a au moins une option correcte
        $hasCorrectOption = false;
        foreach ($value->getOptions() as $option) {
            if ($option->isEstCorrecte()) {
                $hasCorrectOption = true;
                break;
            }
        }

        // Si aucune option correcte n'est trouvée et qu'il y a des options
        if (!$hasCorrectOption && $value->getOptions()->count() > 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
