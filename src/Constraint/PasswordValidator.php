<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PasswordValidator extends ConstraintValidator {
  private const GROUP_NOT_BLANK = 'GROUP_NOT_BLANK';
  private const GROUP_LENGTH = 'GROUP_LENGTH';
  private const GROUP_NOT_COMPROMISED = 'GROUP_NOT_COMPROMISED';

  /**
   * PasswordValidator constructor.
   */
  public function __construct(
    private readonly int $defaultMinLength = 12,
    private readonly bool $defaultCheckDataBreach = true) {
  }

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof Password) {
      throw new UnexpectedTypeException($constraint, Password::class);
    }

    if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
      throw new UnexpectedValueException($value, 'string');
    }

    $value = (string) $value;

    $constraints = $this->getConstraints($constraint);
    $this->context->getValidator()
      ->inContext($this->context)
      ->validate($value, array_values($constraints), new GroupSequence(array_keys($constraints)));
  }

  /**
   * @param Password $constraint
   *
   * @return array<string, Constraint>
   */
  private function getConstraints(Password $constraint): array {
    $constraints = [
      self::GROUP_NOT_BLANK => new NotBlank(['groups' => self::GROUP_NOT_BLANK]),
      self::GROUP_LENGTH => new Length(['groups' => self::GROUP_LENGTH, 'min' => $this->getMinLength($constraint)])
    ];

    if ($this->shouldCheckDataBreach($constraint)) {
      $constraints[self::GROUP_NOT_COMPROMISED] = new NotCompromisedPassword(['groups' => self::GROUP_NOT_COMPROMISED]);
    }

    return $constraints;
  }

  /**
   * @param Password $constraint
   *
   * @return int
   */
  private function getMinLength(Password $constraint): int {
    if ($constraint->minLength !== null) {
      return $constraint->minLength;
    }

    return $this->defaultMinLength;
  }

  /**
   * @param Password $constraint
   *
   * @return bool
   */
  private function shouldCheckDataBreach(Password $constraint): bool {
    if ($constraint->checkDataBreach !== null) {
      return $constraint->checkDataBreach;
    }

    return $this->defaultCheckDataBreach;
  }
}
