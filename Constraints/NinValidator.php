<?php

namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use HalloVerden\ValidatorConstraintsBundle\Helpers\NinHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NinValidator extends ConstraintValidator {

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof Nin) {
      throw new UnexpectedTypeException($constraint, Nin::class);
    }

    if (null === $value || '' === $value) {
      return;
    }

    if (!is_scalar($value) && !$value instanceof \Stringable) {
      throw new UnexpectedValueException($value, 'string');
    }

    $value = (string) $value;

    if (!NinHelper::isValidNin($value, $constraint->types)) {
      $this->context
        ->buildViolation($constraint->message)
        ->setInvalidValue($value)
        ->setParameter('{{ value }}', $this->formatValue($value))
        ->setCode(Nin::ERROR_INVALID_NIN)
        ->addViolation();
    }
  }

}
