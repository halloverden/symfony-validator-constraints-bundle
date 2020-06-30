<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class Uri
 *
 * @package App\Validator\Constraints
 *
 * @Annotation
 */
class Uri extends Constraint {
  public const ERROR_INVALID_URI = '8d87724b-5c03-43b2-8451-8ab57d36d370';
  public const ERROR_URI_MISSING_SCHEME = 'a2e8beb9-39c6-49c4-b529-8466e95774c5';

  protected static $errorNames = [
    self::ERROR_INVALID_URI => 'INVALID_URI',
    self::ERROR_URI_MISSING_SCHEME => 'URI_MISSING_SCHEME'
  ];

  public $message = 'uri.invalid';
  public $messageMissingScheme = 'uri.missing_scheme';
  public $requireScheme = false;
}
