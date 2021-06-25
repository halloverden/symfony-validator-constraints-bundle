<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class UniqueEntityPropertyValidator
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
class UniqueEntityPropertyValidator extends BaseUniqueEntityConstraintValidator {

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed $value
   * @param Constraint $constraint
   *
   * @throws \Exception
   */
  public function validate($value, Constraint $constraint): void {
    if (!$constraint instanceof UniqueEntityProperty) {
      throw new UnexpectedTypeException($constraint, UniqueEntityProperty::class);
    }
    //sets the UniqueEntityProperty as first element in the fields array
    array_unshift($constraint->fields[], $this->context->getPropertyName());

    //validate entity with the UniqueEntity Validator
    $violations = $this->context->getValidator()->validate($this->context->getObject(), $this->getConstraints($constraint->fields));
    if ($violations->count() > 0) {
      self::addViolations($violations);
    }
  }

  /**
   * @param $fields
   * @return UniqueEntity[]
   */
  private function getConstraints($fields): array {
    return [
      new UniqueEntity(['fields' => $fields])
    ];
  }

  /**
   * @param ConstraintViolationListInterface $violations
   */
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
