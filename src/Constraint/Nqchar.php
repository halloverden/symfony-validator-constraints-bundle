<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Nqchar extends Constraint {
  const ERROR_INVALID_NQCHAR_FORMAT = 'd8570c7a-63d6-4c01-ae86-1e1a98013db9';

  protected const ERROR_NAMES = [
    self::ERROR_INVALID_NQCHAR_FORMAT => 'INVALID_NQCHAR_FORMAT'
  ];

  public string $message = 'nqchar.invalid';

  /**
   * Nqchar constructor.
   */
  public function __construct(
    ?array $options = null,
    ?string $message = null,
    ?array $groups = null,
    mixed $payload = null
  ) {
    parent::__construct($options, $groups, $payload);
    $this->message = $message ?? $this->message;
  }

}
