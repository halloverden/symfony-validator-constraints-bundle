<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class UniqueEntityPropertyValidator
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
class UniqueEntityPropertyValidator extends ConstraintValidator {

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
    $violations = $this->context->getValidator()->validate($this->context->getObject(), $this->getConstraints($constraint));
    if ($violations->count() > 0) {
      self::addViolations($violations);
    }
  }

  /**
   * @param UniqueEntityProperty $constraint
   * @return UniqueEntity[]
   */
  private function getConstraints(UniqueEntityProperty $constraint): array {
    return [
      new UniqueEntity([
        'fields'           => $constraint->fields,
        'em'               => $constraint->em,
        'entityClass'      => $constraint->entityClass,
        'errorPath'        => $constraint->errorPath,
        'ignoreNull'       => $constraint->ignoreNull,
        'repositoryMethod' => $constraint->repositoryMethod,
      ])
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
