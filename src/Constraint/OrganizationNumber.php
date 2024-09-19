<?php

namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Validation of organization numbers according to https://www.brreg.no/om-oss/registrene-vare/om-enhetsregisteret/organisasjonsnummeret/
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class OrganizationNumber extends Constraint {
  const ERROR_INVALID_ORGANIZATION_NUMBER = '6bf749da-467f-4a7f-bf36-7a36b76cddd8';

  public string $message = 'This is not a valid organization number';

  protected const ERROR_NAMES = [
    self::ERROR_INVALID_ORGANIZATION_NUMBER => 'INVALID_ORGANIZATION_NUMBER'
  ];

  /**
   * OrganizationNumber constructor.
   */
  public function __construct(
    ?array $options = null,
    string $message = null,
    array $groups = null,
    mixed $payload = null
  ) {
    parent::__construct($options, $groups, $payload);
    $this->message = $message ?? $this->message;
  }

}
