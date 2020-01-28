<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class Nqchar
 *
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class Nqchar extends Constraint {
  const ERROR_INVALID_NQCHAR_FORMAT = 'd8570c7a-63d6-4c01-ae86-1e1a98013db9';

  protected static $errorNames = [
    self::ERROR_INVALID_NQCHAR_FORMAT => 'INVALID_NQCHAR_FORMAT'
  ];

  public $message = 'nqchar.invalid';
}
