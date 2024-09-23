<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;


class NqcharValidator extends ConstraintValidator {
  /** @see https://tools.ietf.org/html/rfc6749#appendix-A */
  const PATTERN = '/^[\x21\x23-\x5B\x5D-\x7E]*$/';

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof Nqchar) {
      throw new UnexpectedTypeException($constraint, Nqchar::class);
    }

    if (null === $value || '' === $value) {
      return;
    }

    if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
      throw new UnexpectedValueException($value, 'string');
    }

    $value = (string) $value;

    if (!preg_match(self::PATTERN, $value)) {
      $this->context->buildViolation($constraint->message)
        ->setCode(Nqchar::ERROR_INVALID_NQCHAR_FORMAT)
        ->addViolation();
    }
  }
}
