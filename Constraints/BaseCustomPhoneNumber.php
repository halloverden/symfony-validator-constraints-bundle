<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class BaseCustomPhoneNumber
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
abstract class BaseCustomPhoneNumber extends Constraint {
  const ERROR_INVALID_PHONE = 'caeb2f02-2af0-4af7-a15d-912d872e55ae';

  protected static $errorNames = [
    self::ERROR_INVALID_PHONE => 'INVALID_PHONE',
  ];

  public $message = 'phoneNumber.invalid';

  public function __construct($options = null) {
    parent::__construct($options);
  }

  public abstract function getValidTypes(): ?array;

}
