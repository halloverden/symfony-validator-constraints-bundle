<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NorwegianBankAccount extends Constraint {
  const ERROR_INVALID_NORWEGIAN_BANK_ACCOUNT = '21b34210-d71d-4710-bed2-6c20d0861906';

  protected const ERROR_NAMES = [
    self::ERROR_INVALID_NORWEGIAN_BANK_ACCOUNT => 'INVALID_NORWEGIAN_BANK_ACCOUNT',
  ];

  public string $message = 'norwegianBankAccount.invalid';

  /**
   * NorwegianBankAccount constructor.
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
