<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Password extends Constraint {
  public ?int $minLength = null;
  public ?bool $checkDataBreach = null;

  /**
   * Password constructor.
   */
  public function __construct(
    ?array $options = null,
    ?int $minLength = null,
    ?bool $checkDataBreach = null,
    ?array $groups = null,
    mixed $payload = null
  ) {
    parent::__construct($options, $groups, $payload);
    $this->minLength = $minLength ?? $this->minLength;
    $this->checkDataBreach = $checkDataBreach ?? $this->checkDataBreach;
  }
}
