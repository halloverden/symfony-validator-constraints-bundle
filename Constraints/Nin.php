<?php

namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use HalloVerden\ValidatorConstraintsBundle\Helpers\NinHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * Class Nin
 *
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Nin extends Constraint {
  public const ERROR_INVALID_NIN = '4c10f6ba-a2ac-4a7d-b597-d27c5a8de008';

  protected const ERROR_NAMES = [
    self::ERROR_INVALID_NIN => 'INVALID_NIN'
  ];

  public string $message = 'This is not a valid nin';
  public array $types = NinHelper::TYPES;

  /**
   * Nin constructor.
   */
  public function __construct(
    ?array $options = null,
    ?string $message = null,
    ?array $types = null,
    ?array $groups = null,
    mixed $payload = null
  ) {
    parent::__construct($options, $groups, $payload);

    $this->message = $message ?? $this->message;
    $this->types = $types ?? $this->types;

    foreach ($this->types as $type) {
      if (!\in_array($type, NinHelper::TYPES, true)) {
        throw new InvalidArgumentException(\sprintf('"%s" is not av valid type', $type));
      }
    }
  }

}
