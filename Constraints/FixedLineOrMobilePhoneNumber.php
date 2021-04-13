<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use libphonenumber\PhoneNumberType;

/**
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
