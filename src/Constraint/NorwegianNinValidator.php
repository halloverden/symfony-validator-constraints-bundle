<?php

namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use HalloVerden\ValidatorConstraintsBundle\Helper\NorwegianNinHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NorwegianNinValidator extends ConstraintValidator {

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof NorwegianNin) {
      throw new UnexpectedTypeException($constraint, NorwegianNin::class);
    }

    if (null === $value || '' === $value) {
      return;
    }

    if (!is_scalar($value) && !$value instanceof \Stringable) {
      throw new UnexpectedValueException($value, 'string');
    }

    $value = (string) $value;

    if (!NorwegianNinHelper::isValidNin($value, $constraint->types)) {
      $this->context
        ->buildViolation($constraint->message)
        ->setInvalidValue($value)
        ->setParameter('{{ value }}', $this->formatValue($value))
        ->setCode(NorwegianNin::ERROR_INVALID_NIN)
        ->addViolation();
    }
  }

}
