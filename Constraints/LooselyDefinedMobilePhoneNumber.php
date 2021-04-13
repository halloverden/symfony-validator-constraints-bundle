<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use libphonenumber\PhoneNumberType;

/**
 * Class LooselyDefinedMobilePhoneNumber
 * This Constraint is used to validate mobile numbers or numbers where it's impossible to determine if they are fixed line or mobile
 *
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class LooselyDefinedMobilePhoneNumber extends BaseCustomPhoneNumber {

  /**
   * @var array|null
   */
  public $validTypes;

  public function __construct($options = null) {
    $this->validTypes = [ PhoneNumberType::MOBILE, PhoneNumberType::FIXED_LINE_OR_MOBILE ];
    parent::__construct($options);
  }


  public function getValidTypes(): ?array {
    return $this->validTypes;
  }
}
