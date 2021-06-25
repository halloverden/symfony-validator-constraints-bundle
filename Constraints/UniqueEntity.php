<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


/**
 * Class UniqueEntity
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @package App\Validator\Constraints
 */
class UniqueEntity extends BaseUniqueEntityConstraint {

  public function getRequiredOptions() {
    return ['fields'];
  }

  /**
   * {@inheritdoc}
   */
  public function getTargets() {
    return self::CLASS_CONSTRAINT;
  }

}
