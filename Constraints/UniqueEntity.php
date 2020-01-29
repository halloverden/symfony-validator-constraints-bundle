<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueEntity
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @package App\Validator\Constraints
 */
class UniqueEntity extends Constraint implements ArrayClassClassConstraintInterface {
  const NOT_UNIQUE_ERROR = 'f11bf751-a5f8-4512-adad-57257f9979aa';

  public $message = 'uniqueEntity.invalid';
  public $em = null;
  public $entityClass = null;
  public $errorPath = null;
  public $fields = [];
  public $ignoreNull = true;
  public $repositoryMethod = 'findBy';

  protected static $errorNames = [
    self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
  ];

  public function getRequiredOptions() {
    return ['fields'];
  }

  /**
   * {@inheritdoc}
   */
  public function getTargets() {
    return self::CLASS_CONSTRAINT;
  }

  public function getDefaultOption() {
    return 'fields';
  }

  public function setClassName(string $class): void {
    $this->entityClass = $class;
  }
}
