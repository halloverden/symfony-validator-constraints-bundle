<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use libphonenumber\PhoneNumberType;

/**
 * Class FixedLineOrMobilePhoneNumber
 * This Constraint is used to validate fixed line or mobile phone numbers
 *
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class FixedLineOrMobilePhoneNumber extends BaseCustomPhoneNumber {

  /**
   * @var array|null
   */
  public $validTypes;

  public function __construct($options = null) {
    $this->validTypes = [ PhoneNumberType::FIXED_LINE, PhoneNumberType::MOBILE, PhoneNumberType::FIXED_LINE_OR_MOBILE ];
    parent::__construct($options);
  }

  public function getValidTypes(): ?array {
    return $this->validTypes;
  }
}
