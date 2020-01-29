<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class NorwegianBankAccount
 *
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class NorwegianBankAccount extends Constraint {
  const ERROR_INVALID_NORWEGIAN_BANK_ACCOUNT = '21b34210-d71d-4710-bed2-6c20d0861906';

  protected static $errorNames = [
    self::ERROR_INVALID_NORWEGIAN_BANK_ACCOUNT => 'INVALID_NORWEGIAN_BANK_ACCOUNT',
  ];

  public $message = 'norwegianBankAccount.invalid';
}
