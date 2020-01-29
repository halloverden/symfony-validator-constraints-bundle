<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 */
class Email extends Constraint {
  const ERROR_INVALID_EMAIL = '55427361-37fa-48ae-942e-6b7f44fac7ef';

  protected static $errorNames = [
    self::ERROR_INVALID_EMAIL => 'INVALID_EMAIL',
  ];

  public $message = 'email.invalid';
}
