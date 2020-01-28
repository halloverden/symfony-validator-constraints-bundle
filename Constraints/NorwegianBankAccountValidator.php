<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NorwegianBankAccountValidator extends ConstraintValidator {
  const MULTIPLIER = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
  const SIMPLE_BANK_ACCOUNT_REGEX = '/^[0-9]{11}$/';

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed $value The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof NorwegianBankAccount) {
      throw new UnexpectedTypeException($constraint, NorwegianBankAccount::class);
    }

    // custom constraints should ignore null and empty values to allow
    // other constraints (NotBlank, NotNull, etc.) take care of that
    if (null === $value || '' === $value) {
      return;
    }

    if (filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => self::SIMPLE_BANK_ACCOUNT_REGEX]]) === false) {
      $this->context
        ->buildViolation($constraint->message)
        ->setCode(NorwegianBankAccount::ERROR_INVALID_NORWEGIAN_BANK_ACCOUNT)
        ->addViolation();
      return;
    }

    $bankAccountNumbers = str_split($value);
    $total = 0;

    foreach(self::MULTIPLIER as $index => $multiplier) {
      $total += ($bankAccountNumbers[$index] * self::MULTIPLIER[$index]);
    }

    $remainder = $total % 11;

    switch ($remainder) {
      case 0:
        if ($remainder !== intval($bankAccountNumbers[10])) {
          $this->context
            ->buildViolation($constraint->message)
            ->setCode(NorwegianBankAccount::ERROR_INVALID_NORWEGIAN_BANK_ACCOUNT)
            ->addViolation();
          return;
        }
        break;
      default:
        if ((11 - $remainder) !== intval($bankAccountNumbers[10])) {
          $this->context
            ->buildViolation($constraint->message)
            ->setCode(NorwegianBankAccount::ERROR_INVALID_NORWEGIAN_BANK_ACCOUNT)
            ->addViolation();
          return;
        }
    }
  }
}
