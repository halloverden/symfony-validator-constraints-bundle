<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


/**
 * Class UniqueProperty
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class UniqueEntityProperty extends BaseUniqueEntityConstraint {

  public function getRequiredOptions(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getTargets() {
    return self::PROPERTY_CONSTRAINT;
  }

}