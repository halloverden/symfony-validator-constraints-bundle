<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 */
class Phone extends Constraint {
  const ERROR_INVALID_PHONE = 'caeb2f02-2af0-4af7-a15d-912d872e55ae';

  protected static $errorNames = [
    self::ERROR_INVALID_PHONE => 'INVALID_PHONE',
  ];

  public $message = 'phoneNumber.invalid';

  /**
   * @var array|null
   */
  public $validTypes;

  public function __construct($options = null) {
    $this->validTypes = $options['validTypes'] ?? null;
    parent::__construct($options);
  }

}
