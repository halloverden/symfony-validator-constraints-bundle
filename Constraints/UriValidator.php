<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use League\Uri\Contracts\UriException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use League\Uri\Uri as LeagueUri;

class UriValidator extends ConstraintValidator {

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof Uri) {
      throw new UnexpectedTypeException($constraint, Uri::class);
    }

    if (null === $value || '' === $value) {
      return;
    }

    if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
      throw new UnexpectedValueException($value, 'string');
    }

    $value = (string) $value;

    try {
      LeagueUri::createFromString($value);
    } catch (UriException $exception) {
      $this->context->buildViolation($constraint->message)
        ->setParameter('{{ value }}', $this->formatValue($value))
        ->setCode(Uri::ERROR_INVALID_URI)
        ->addViolation();
    }
  }
}
