<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Phone extends Constraint {
  const ERROR_INVALID_PHONE = 'caeb2f02-2af0-4af7-a15d-912d872e55ae';

  protected const ERROR_NAMES = [
    self::ERROR_INVALID_PHONE => 'INVALID_PHONE',
  ];

  public string $message = 'phoneNumber.invalid';
  public ?array $validTypes = null;

  /**
   * @inheritDoc
   */
  public function __construct(
    ?array $options = null,
    ?string $message = null,
    ?array $validTypes = null,
    ?array $groups = null,
    mixed $payload = null
  ) {
    parent::__construct($options, $groups, $payload);
    $this->message = $message ?? $this->message;
    $this->validTypes = $validTypes ?? $this->validTypes;
  }

}
