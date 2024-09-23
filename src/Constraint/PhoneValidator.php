<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


class PhoneValidator extends ConstraintValidator {
  const NOT_BLANK_GROUP = 'not_blank';
  const PHONE_NUMBER_GROUP = 'phone_number';

  /**
   * PhoneValidator constructor.
   */
  public function __construct(
    private readonly string $defaultRegion = 'NO'
  ) {
  }

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof Phone) {
      throw new UnexpectedTypeException($constraint, Phone::class);
    }
    $value = (string)$value;
    $violations = $this->context->getValidator()->validate($value, $this->getConstraints($constraint->validTypes), self::getGroupSequence());
    if ($violations->count() > 0) {
      self::addViolations($violations);
    }
  }

  private function getConstraints(?array $validTypes): array {
    return [
      new NotBlank(['groups' => self::NOT_BLANK_GROUP]),
      new PhoneNumber(['defaultRegion' => $this->defaultRegion, 'groups' => self::PHONE_NUMBER_GROUP, 'validTypes' => $validTypes]),
    ];
  }

  private static function getGroupSequence(): GroupSequence {
    return new GroupSequence([self::NOT_BLANK_GROUP, self::PHONE_NUMBER_GROUP]);
  }

  private function addViolations(ConstraintViolationListInterface $violations): void {
    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation){
      $this->context->setConstraint($violation->getConstraint());
      $this->context->buildViolation($violation->getMessage())
        ->setCode($violation->getCode())
        ->setCause($violation->getCause())
        ->setInvalidValue($violation->getInvalidValue())
        ->addViolation();
    }
  }
}
