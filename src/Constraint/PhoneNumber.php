<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use libphonenumber\PhoneNumberType;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PhoneNumber extends Constraint {
  const INVALID_PHONE_NUMBER = '71cb361e-3d33-4565-abd6-20d63ba987e8';
  const IS_POSSIBLE = '60f100fb-376a-47f4-88b8-a9d3a4f2507f';
  const INVALID_COUNTRY_CODE = '4c307dc9-bfe7-4f7b-ae40-a7bcee7ad562';
  const TOO_SHORT = 'a5a72a58-9384-4545-8579-5b0b7f12c40b';
  const TOO_LONG = '49ddd5bb-6bc1-415e-a9a8-2d9ca5c013cd';
  const IS_POSSIBLE_LOCAL_ONLY = '7659707b-f868-417b-afec-a00fac0ff310';
  const INVALID_LENGTH = '6bab7c70-f903-4fd1-8542-8fe18c301221';
  const INVALID_TYPE = 'fc1c7a49-ee84-4b45-a41d-4d3ee89d63bb';

  protected const ERROR_NAMES = [
    self::INVALID_PHONE_NUMBER => 'INVALID_PHONE_NUMBER',
    self::IS_POSSIBLE => 'IS_POSSIBLE',
    self::INVALID_COUNTRY_CODE => 'INVALID_COUNTRY_CODE',
    self::TOO_SHORT => 'TOO_SHORT',
    self::TOO_LONG => 'TOO_LONG',
    self::IS_POSSIBLE_LOCAL_ONLY => 'IS_POSSIBLE_LOCAL_ONLY',
    self::INVALID_LENGTH => 'INVALID_LENGTH',
    self::INVALID_TYPE => 'INVALID_TYPE',
  ];

  const PHONE_NUMBER_REASON_TO_CODE = [
    'invalid' => self::INVALID_PHONE_NUMBER,
    'is_possible' => self::IS_POSSIBLE,
    'invalid_country_code' => self::INVALID_COUNTRY_CODE,
    'too_short' => self::TOO_SHORT,
    'too_long' => self::TOO_LONG,
    'is_possible_local_only' => self::IS_POSSIBLE_LOCAL_ONLY,
    'invalid_length' => self::INVALID_LENGTH,
    'invalid_type' => self::INVALID_TYPE,
  ];

  const PHONE_NUMBER_VALIDATION_RESULT_TO_REASON = [
    0 => 'is_possible',
    1 => 'invalid_country_code',
    2 => 'too_short',
    3 => 'too_long',
    4 => 'is_possible_local_only',
    5 => 'invalid_length'
  ];

  public string $message = 'phoneNumber.%s';
  public ?string $defaultRegion = null;

  /**
   * @var int[]
   */
  public array $validTypes = [PhoneNumberType::MOBILE, PhoneNumberType::FIXED_LINE_OR_MOBILE];

  /**
   * PhoneNumber constructor.
   */
  public function __construct(
    ?array $options = null,
    ?string $message = null,
    ?string $defaultRegion = null,
    ?array $validTypes = null,
    ?array $groups = null,
    mixed $payload = null
  ) {
    if (!class_exists(PhoneNumberType::class)) {
      throw new \LogicException(sprintf('The "%s" class requires the "libphonenumber" component. Try running "composer require giggsey/libphonenumber-for-php".', self::class));
    }

    parent::__construct($options, $groups, $payload);

    $this->message = $message ?? $this->message;
    $this->defaultRegion = $defaultRegion ?? $this->defaultRegion;
    $this->validTypes = $validTypes ?? $this->validTypes;
  }

}
