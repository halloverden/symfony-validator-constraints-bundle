<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class AssertIf extends Constraint {
  public Constraint $test;
  public array $constraints = [];
  public array $elseConstraints = [];
  public bool $includeViolations = false;

  /**
   * AssertIf constructor.
   */
  public function __construct(
    Constraint|array|null $test,
    ?array $constraints = null,
    ?array $elseConstraints = null,
    bool $includeViolations = null,
    ?array $groups = null,
    mixed $payload = null,
    array $options = [],
  ) {
    if (\is_array($test)) {
      $options = array_merge($test, $options);
    } elseif (null !== $test) {
      $options['value'] = $test;
    }

    parent::__construct($options, $groups, $payload);

    $this->constraints = $constraints ?? $this->constraints;
    $this->elseConstraints = $elseConstraints ?? $this->elseConstraints;
    $this->includeViolations = $includeViolations ?? $this->includeViolations;
  }

  /**
   * @inheritDoc
   */
  public function getDefaultOption(): ?string {
    return 'test';
  }

  /**
   * @inheritDoc
   */
  public function getRequiredOptions(): array {
    return ['test'];
  }

}
