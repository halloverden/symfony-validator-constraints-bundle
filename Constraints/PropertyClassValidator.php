<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PropertyClassValidator extends BaseConstraintValidator {

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof PropertyClass) {
      throw new UnexpectedTypeException($constraint, PropertyClass::class);
    }

    $attributeValue = $this->getPropertyValue($constraint->propertyPath);

    $constraints = [];
    foreach ($constraint->classes as $class) {
      $constraints += $this->getPropertyConstraintsForClass($class, $constraint->groupsToGet ?: []);
    }

    if (!isset($constraints[$attributeValue])) {
      $this->context->buildViolation('Invalid attribute')->atPath($constraint->propertyPath)->setCode(PropertyClass::ERROR_INVALID_ATTRIBUTE)->addViolation();
      return;
    }

    $this->context->getValidator()->inContext($this->context)->validate($value, $constraints[$attributeValue], $constraint->groupsToGet);
  }

}
