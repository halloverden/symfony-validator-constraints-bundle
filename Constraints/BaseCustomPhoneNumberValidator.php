<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class BaseCustomPhoneNumberValidator
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
abstract class BaseCustomPhoneNumberValidator extends ConstraintValidator {

  /**
   * @var string
   */
  private $defaultRegion;

  /**
   * PhoneValidator constructor.
   *
   * @param string $defaultRegion
   */
  public function __construct(string $defaultRegion = 'NO') {
    $this->defaultRegion = $defaultRegion;
  }

  /**
   * @param mixed $value
   * @param Constraint $constraint
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof BaseCustomPhoneNumber) {
      throw new UnexpectedTypeException($constraint, BaseCustomPhoneNumber::class);
    }
    $value = (string)$value;
    $violations = $this->context->getValidator()->validate($value, $this->getConstraints($constraint->getValidTypes()));
    if ($violations->count() > 0) {
      self::addViolations($violations);
    }
  }

  private function getConstraints(?array $validTypes) {
    return [
      new PhoneNumber(['defaultRegion' => $this->defaultRegion, 'validTypes' => $validTypes]),
    ];
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
