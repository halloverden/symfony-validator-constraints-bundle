<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use League\Uri\Exceptions\SyntaxError;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use League\Uri\Uri as LeagueUri;

class UriValidator extends ConstraintValidator {

  /**
   * UriValidator constructor.
   */
  public function __construct() {
    if (!class_exists(LeagueUri::class)) {
      throw new \LogicException(sprintf('The "%s" class requires the "League/uri" component. Try running "composer require league/uri".', self::class));
    }
  }

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
      $uri = LeagueUri::createFromString($value);
    } catch (SyntaxError $exception) {
      $this->context->buildViolation($constraint->message)
        ->setParameter('{{ value }}', $this->formatValue($value))
        ->setCode(Uri::ERROR_INVALID_URI)
        ->addViolation();

      return;
    }

    if ($constraint->requireScheme && !$uri['scheme']) {
      $this->context->buildViolation($constraint->messageMissingScheme)
        ->setParameter('{{ value }}', $this->formatValue($value))
        ->setCode(Uri::ERROR_URI_MISSING_SCHEME)
        ->addViolation();
    }
  }
}
