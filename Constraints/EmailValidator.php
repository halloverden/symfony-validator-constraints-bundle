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


class EmailValidator extends ConstraintValidator {
  const NOT_BLANK_GROUP = 'not_blank';
  const EMAIL_GROUP = 'email';
  const KICKBOX_GROUP = 'kickbox';

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
    if (!$constraint instanceof Email) {
      throw new UnexpectedTypeException($constraint, Email::class);
    }
    $value = (string)$value;
    $violations = $this->validator->validate($value, self::getConstraints(), self::getGroupSequence());
    if ($violations->count() > 0) {
      self::addViolations($violations);
    }
  }

  private static function getConstraints() {
    return [
      new NotBlank(['groups'=> self::NOT_BLANK_GROUP]),
      new \Symfony\Component\Validator\Constraints\Email(['groups'=> self::EMAIL_GROUP]),
      new Kickbox(['groups'=> self::KICKBOX_GROUP])
    ];
  }

  private static function getGroupSequence() {
    return new GroupSequence([self::NOT_BLANK_GROUP, self::EMAIL_GROUP, self::KICKBOX_GROUP]);
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
