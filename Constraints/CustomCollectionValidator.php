<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Class CustomCollectionValidator
 *
 * @package App\Validator\Constraints
 */
class CustomCollectionValidator extends ConstraintValidator {

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof CustomCollection) {
      throw new UnexpectedTypeException($constraint, CustomCollection::class);
    }

    if (null === $value) {
      return;
    }

    if (!\is_array($value)) {
      throw new UnexpectedValueException($value, 'array');
    }

    $context = $this->context;

    foreach ($constraint->fields as $field => $fieldConstraint) {
      $context->getValidator()
        ->inContext($context)
        ->atPath('[' . $field . ']')
        ->validate(isset($value[$field]) ? $value[$field] : null, $fieldConstraint->constraints);
    }
  }
}
