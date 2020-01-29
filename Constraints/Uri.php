<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class Uri
 *
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class Uri extends Constraint {
  public const ERROR_INVALID_URI = '8d87724b-5c03-43b2-8451-8ab57d36d370';

  protected static $errorNames = [
    self::ERROR_INVALID_URI => 'INVALID_URI'
  ];

  public $message = 'uri.invalid';
}
