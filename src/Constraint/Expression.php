<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use Symfony\Component\Validator\Constraints\Expression as SymfonyExpression;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Expression extends SymfonyExpression {

  /**
   * @inheritDoc
   */
  public function validatedBy(): string {
    return ExpressionValidator::class;
  }

}
