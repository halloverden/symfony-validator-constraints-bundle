<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


/**
 * Class UniqueEntityProperty
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Validator\Constraints
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
