<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class Password
 *
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class Password extends Constraint {

  /**
   * @var int
   */
  public $minLength;

  /**
   * @var bool
   */
  public $checkDataBreach;
}
