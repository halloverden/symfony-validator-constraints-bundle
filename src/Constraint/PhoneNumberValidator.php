<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use HalloVerden\ValidatorConstraintsBundle\Helper\PhoneNumberHelper;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PhoneNumberValidator extends ConstraintValidator {
  private readonly PhoneNumberUtil $phoneNumberUtil;

  /**
   * PhoneNumberValidator constructor.
   */
  public function __construct(
    private readonly string $defaultRegion = 'NO',
    ?PhoneNumberUtil $phoneNumberUtil = null
  ) {
    if (!class_exists(PhoneNumberUtil::class)) {
      throw new \LogicException(sprintf('The "%s" class requires the "libphonenumber" component. Try running "composer require giggsey/libphonenumber-for-php".', self::class));
    }

    $this->phoneNumberUtil = $phoneNumberUtil ?? PhoneNumberUtil::getInstance();
  }

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof PhoneNumber) {
      throw new UnexpectedTypeException($constraint, PhoneNumber::class);
    }

    // custom constraints should ignore null and empty values to allow
    // other constraints (NotBlank, NotNull, etc.) take care of that
    if (null === $value || '' === $value) {
      return;
    }

    if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString')) && !$value instanceof \libphonenumber\PhoneNumber) {
      throw new UnexpectedValueException($value, 'string|' . \libphonenumber\PhoneNumber::class);
    }

    if (!$this->isValidPhoneNumber($value, $constraint)) {
      $reason = $this->getInvalidityReason($value, $constraint);
      $this->context->buildViolation(sprintf($constraint->message, $reason))->setCode(PhoneNumber::PHONE_NUMBER_REASON_TO_CODE[$reason])->addViolation();
    }
  }

  /**
   * Checks if the phone number provided is valid by checking its formal validity according to region first, and then by checking if its type is allowed.
   *
   * @param \libphonenumber\PhoneNumber|string $value
   * @param PhoneNumber                        $constraint
   *
   * @return bool
   */
  private function isValidPhoneNumber(\libphonenumber\PhoneNumber|string $value, PhoneNumber $constraint): bool {
    if (!$value instanceof \libphonenumber\PhoneNumber) {
      try {
        $phoneNumber = PhoneNumberHelper::getPhoneNumber((string) $value, true, $constraint->defaultRegion ?: $this->defaultRegion);
      } catch (NumberParseException $e) {
        return false;
      }
    } else {
      $phoneNumber = $value;
    }
    return $this->phoneNumberUtil->isValidNumber($phoneNumber) && $this->isPhoneNumberTypeAllowed($phoneNumber, $constraint);
  }

  /**
   * Checks if the number provided has an allowed type.
   *
   * @param \libphonenumber\PhoneNumber|string $value
   * @param PhoneNumber                        $constraint
   *
   * @return bool
   */
  private function isPhoneNumberTypeAllowed(\libphonenumber\PhoneNumber|string $value, PhoneNumber $constraint): bool {
    if(!empty($constraint->validTypes)) {
      if (!$value instanceof \libphonenumber\PhoneNumber) {
        try {
          $phoneNumber = PhoneNumberHelper::getPhoneNumber((string)$value, true, $constraint->defaultRegion ?: $this->defaultRegion);
        } catch (NumberParseException $e) {
          return false;
        }
      } else {
        $phoneNumber = $value;
      }
      return (in_array($this->phoneNumberUtil->getNumberType($phoneNumber), $constraint->validTypes));
    }
    return true;
  }

  /**
   * Returns the reason for which the phone number is not valid. The default is 'invalid'.
   *
   * @param string      $value
   * @param PhoneNumber $constraint
   *
   * @return string
   */
  private function getInvalidityReason(string $value, PhoneNumber $constraint): string {
    try {
      $phoneNumber = PhoneNumberHelper::getPhoneNumber((string) $value, true, $constraint->defaultRegion ?: $this->defaultRegion);
    } catch (NumberParseException $e) {
      return 'invalid';
    }

    if(!$this->phoneNumberUtil->isPossibleNumber($value, $constraint->defaultRegion ?: $this->defaultRegion)) {
      return PhoneNumber::PHONE_NUMBER_VALIDATION_RESULT_TO_REASON[$this->phoneNumberUtil->isPossibleNumberWithReason($phoneNumber)];
    } else {
      //if the number is possible check if invalidity is because of type
      if(!$this->isPhoneNumberTypeAllowed($phoneNumber,$constraint)){
        return 'invalid_type';
      }
    }
    return 'invalid';
  }

}
