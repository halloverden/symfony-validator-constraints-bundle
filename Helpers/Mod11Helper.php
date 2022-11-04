<?php

namespace HalloVerden\ValidatorConstraintsBundle\Helpers;

final class Mod11Helper {
  private function __construct() {/* static class */}

  /**
   * @param string $input
   * @param array  $weights
   * @param string $onRemainder1 what is returned if remainder is 1
   *
   * @return string
   */
  public static function calculateControlDigitMod11(string $input, array $weights, string $onRemainder1 = '-'): string {
    $length = strlen($input);
    if ($length !== count($weights)) {
      throw new \InvalidArgumentException('a weight needs to be assigned to each digit');
    }

    $sum = 0;
    for ($i = 0; $i < $length; $i++) {
      if (!\ctype_digit($input[$i])) {
        throw new \InvalidArgumentException('All characters in $input must be a digit');
      }

      $sum += \intval($input[$i]) * $weights[$i];
    }

    return match ($remainder = $sum % 11) {
      0 => '0',
      1 => $onRemainder1,
      default => \strval(11 - $remainder)
    };
  }

}
