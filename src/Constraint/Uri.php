<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Uri extends Constraint {
  public const ERROR_INVALID_URI = '8d87724b-5c03-43b2-8451-8ab57d36d370';
  public const ERROR_URI_MISSING_SCHEME = 'a2e8beb9-39c6-49c4-b529-8466e95774c5';

  protected const ERROR_NAMES = [
    self::ERROR_INVALID_URI => 'INVALID_URI',
    self::ERROR_URI_MISSING_SCHEME => 'URI_MISSING_SCHEME'
  ];

  public string $message = 'uri.invalid';
  public string $messageMissingScheme = 'uri.missing_scheme';
  public bool $requireScheme = false;

  /**
   * @inheritDoc
   */
  public function __construct(
    ?array $options = null,
    ?string $message = null,
    ?string $messageMissingScheme = null,
    ?bool $requireScheme = null,
    ?array $groups = null,
    mixed $payload = null
  ) {
    parent::__construct($options, $groups, $payload);

    $this->message = $message ?? $this->message;
    $this->messageMissingScheme = $messageMissingScheme ?? $this->messageMissingScheme;
    $this->requireScheme = $requireScheme ?? $this->requireScheme;
  }

}
