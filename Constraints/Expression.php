<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use Symfony\Component\Validator\Constraints\Expression as SymfonyExpression;


class Expression extends SymfonyExpression {

  /**
   * @inheritDoc
   */
  public function validatedBy() {
    return ExpressionValidator::class;
  }

}
