<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


/**
 * Class UniqueProperty
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class UniqueEntityProperty extends BaseUniqueEntityConstraint {

  /**
   * @var string
   */
  public $message = 'uniqueProperty.invalid.{{ value }}';

  public $fields = [];
  public $ignoreNull = true;
  public $repositoryMethod = 'findBy';

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