<?php

namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use HalloVerden\ValidatorConstraintsBundle\Helpers\Mod11Helper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class OrganizationNumberValidator extends ConstraintValidator {
  private const ORGANIZATION_NUMBER_WEIGHTS = [3, 2, 7, 6, 5, 4, 3, 2];

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof OrganizationNumber) {
      throw new UnexpectedTypeException($constraint, OrganizationNumber::class);
    }

    if (null === $value || '' === $value) {
      return;
    }

    if (!is_scalar($value) && !$value instanceof \Stringable) {
      throw new UnexpectedValueException($value, 'string');
    }

    $value = (string) $value;

    if (!\preg_match('/^[0-9]{9}$/', $value)) {
      $this->createViolation($constraint, $value);
      return;
    }

    if (Mod11Helper::calculateControlDigitMod11(\substr($value, 0, 8), self::ORGANIZATION_NUMBER_WEIGHTS) !== $value[8]) {
      $this->createViolation($constraint, $value);
    }
  }

  /**
   * @param OrganizationNumber $constraint
   * @param string             $value
   *
   * @return void
   */
  private function createViolation(OrganizationNumber $constraint, string $value): void {
    $this->context
      ->buildViolation($constraint->message)
      ->setInvalidValue($value)
      ->setParameter('{{ value }}', $this->formatValue($value))
      ->setCode(OrganizationNumber::ERROR_INVALID_ORGANIZATION_NUMBER)
      ->addViolation();
  }

}
