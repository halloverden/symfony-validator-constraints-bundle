<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ArrayClass
 *
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class ArrayClass extends Constraint {

  /**
   * @var array
   */
  public $classes;

  /**
   * @var array
   */
  public $constraints = [
    "classConstraints" => [],
    "propertyConstraints" => []
  ];

  /**
   * @var array
   */
  public $groupsToGet = [];

  /**
   * ArrayClass constructor.
   *
   * @param null $options
   */
  public function __construct($options = null) {
    parent::__construct($options);
  }

  /**
   * @return string|null
   */
  public function getDefaultOption() {
    return 'classes';
  }

  /**
   * @return array
   */
  public function getRequiredOptions() {
    return ['classes'];
  }

}
