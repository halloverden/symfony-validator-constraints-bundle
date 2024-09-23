<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\ExpressionValidator as SymfonyExpressionValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ExpressionValidator
 *
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
class ExpressionValidator extends SymfonyExpressionValidator {

  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof Expression) {
      throw new UnexpectedTypeException($constraint, Expression::class);
    }

    $constraint->values['root'] = $this->context->getRoot();

    parent::validate($value, $constraint);
  }

}
