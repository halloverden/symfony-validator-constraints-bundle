<?php


namespace HalloVerden\ValidatorConstraintsBundle\Helper;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

final class PhoneNumberHelper {
  private static array $phoneNumbers = [];

  /**
   * @param string $number
   * @param bool   $throwOnParseError
   * @param string $defaultRegion
   *
   * @return PhoneNumber|null
   * @throws NumberParseException
   */
  public static function getPhoneNumber(string $number, bool $throwOnParseError = false, string $defaultRegion = 'NO'): ?PhoneNumber {
    if (isset(self::$phoneNumbers[$number])) {
      return self::$phoneNumbers[$number];
    }

    try {
      return self::$phoneNumbers[$number] = PhoneNumberUtil::getInstance()->parse($number, $defaultRegion, null, true);
    } catch (NumberParseException $e) {
      if ($throwOnParseError) {
        throw $e;
      }

      return null;
    }
  }

  /**
   * @param PhoneNumber $phoneNumber1
   * @param PhoneNumber $phoneNumber2
   *
   * @return bool
   */
  public static function equals(PhoneNumber $phoneNumber1, PhoneNumber $phoneNumber2): bool {
    return $phoneNumber1->getNationalNumber() === $phoneNumber2->getNationalNumber() && $phoneNumber1->getCountryCode() === $phoneNumber2->getCountryCode();
  }

}
