<?php

namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use HalloVerden\ValidatorConstraintsBundle\Helper\NorwegianNinHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NorwegianNin extends Constraint {
  public const ERROR_INVALID_NIN = '4c10f6ba-a2ac-4a7d-b597-d27c5a8de008';

  protected const ERROR_NAMES = [
    self::ERROR_INVALID_NIN => 'INVALID_NIN'
  ];

  public string $message = 'This is not a valid nin';
  public array $types = NorwegianNinHelper::TYPES;

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
      if (!\in_array($type, NorwegianNinHelper::TYPES, true)) {
        throw new InvalidArgumentException(\sprintf('"%s" is not av valid type', $type));
      }
    }
  }

}
