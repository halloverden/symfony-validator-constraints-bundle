<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PhoneValidator extends ConstraintValidator {
  const NOT_BLANK_GROUP = 'not_blank';
  const PHONE_NUMBER_GROUP = 'phone_number';

  /**
   * @var ValidatorInterface
   */
  private $validator;

  public function __construct(ValidatorInterface $validator) {
    $this->validator = $validator;
  }

  /**
   * @param mixed $value
   * @param Constraint $constraint
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof Phone) {
      throw new UnexpectedTypeException($constraint, Phone::class);
    }
    $value = (string)$value;
    $violations = $this->validator->validate($value, self::getConstraints(), self::getGroupSequence());
    if ($violations->count() > 0) {
      self::addViolations($violations);
    }
  }

  private static function getConstraints() {
    return [
      new NotBlank(['groups' => self::NOT_BLANK_GROUP]),
      new PhoneNumber(['defaultRegion' => \App\Entity\User\Phone::DEFAULT_REGION, 'groups' => self::PHONE_NUMBER_GROUP]),
    ];
  }

  private static function getGroupSequence() {
    return new GroupSequence([self::NOT_BLANK_GROUP, self::PHONE_NUMBER_GROUP]);
  }

  private function addViolations(ConstraintViolationListInterface $violations) {
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
