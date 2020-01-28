<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class AssertIf
 * @package App\Validator\Constraints
 *
 * @Annotation()
 */
class AssertIf extends Constraint {
  public $test;

  /**
   * @var array
   */
  public $constraints = [];

  /**
   * @var array
   */
  public $elseConstraints = [];

  /**
   * @var bool
   */
  public $includeViolations = false;
}
