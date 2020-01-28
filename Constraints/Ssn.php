<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class Ssn
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class Ssn extends Constraint {
  public $message = 'ssn.invalid';

  const ERROR_INVALID_SSN = 'a503aab0-c3da-4526-90eb-34d02c055f1d';

  protected static $errorNames = [
    self::ERROR_INVALID_SSN => 'INVALID_SSN',
  ];
}
