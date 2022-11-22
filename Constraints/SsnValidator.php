<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use HalloVerden\ValidatorConstraintsBundle\Helpers\SSNHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @deprecated use NinValidator
 */
class SsnValidator extends ConstraintValidator {

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed $value The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   * @throws \Exception
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof Ssn) {
      throw new UnexpectedTypeException($constraint, Ssn::class);
    }

    // custom constraints should ignore null and empty values to allow
    // other constraints (NotBlank, NotNull, etc.) take care of that
    if (null === $value || '' === $value) {
      return;
    }

    try {
      $ssnPid = SSNHelper::getPIDFromSSN($value);
      $birthDate = SSNHelper::getBirthDateFromSSN($value, 'Y-m-d', true);
      if (!SSNHelper::validateSSN($birthDate, $ssnPid, SSNHelper::isDNumber($value))) {
        $this->context->buildViolation($constraint->message)->setCode(Ssn::ERROR_INVALID_SSN)->addViolation();
      }
    } catch (\Exception $exception) {
      $this->context->buildViolation($constraint->message)->setCode(Ssn::ERROR_INVALID_SSN)->addViolation();
    }

  }
}
