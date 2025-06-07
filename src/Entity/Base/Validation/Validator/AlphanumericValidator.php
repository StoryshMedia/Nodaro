<?php

namespace Smug\Core\Entity\Base\Validation\Validator;

use Smug\Core\Entity\Base\Validation\Constraint\ContainsAlphanumeric;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AlphanumericValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ContainsAlphanumeric) {
            throw new UnexpectedTypeException($constraint, ContainsAlphanumeric::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if ('strict' === $constraint->mode) {
            if (!DataHandler::isString($value)) {
                throw new UnexpectedValueException($value, 'string|int');
            }
        }

        if (!DataHandler::isString($value) && !DataHandler::isNumeric($value)) {
            throw new UnexpectedValueException($value, 'string|int');
        }

        if (preg_match('/^[a-zA-Z0-9]+$/', $value, $matches)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ string }}', $value)
            ->addViolation();
    }
}