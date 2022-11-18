<?php

namespace HalloVerden\ValidatorConstraintsBundle\Helpers;

final class NinHelper {
  private function __construct() {/* static class */}

  public const TYPE_D_NUMBER = 'D_NUMBER';

  /**
   * Synthetic NINs as defined by the norwegian national population register: https://skatteetaten.github.io/folkeregisteret-api-dokumentasjon/test-for-konsumenter/
   */
  public const TYPE_NPR_SYNTHETIC = 'NPR_SYNTHETIC';

  public const TYPES = [
    self::TYPE_D_NUMBER,
    self::TYPE_NPR_SYNTHETIC
  ];

  private const K1_WEIGHTS = [3, 7, 6, 1, 8, 9, 4, 5, 2];
  private const K2_WEIGHTS = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];

  /**
   * @param string $nin
   * @param array  $validTypes
   *
   * @return bool
   */
  public static function isValidNin(string $nin, array $validTypes = self::TYPES): bool {
    if (!\preg_match('/^[0-9]{11}$/', $nin)) {
      return false;
    }

    $day = \substr($nin, 0, 2);
    $month = \substr($nin, 2, 2);
    $year = \substr($nin, 4, 2);
    $ind = \substr($nin, 6, 3);
    $k1 = \substr($nin, 9, 1);
    $k2 = \substr($nin, 10, 1);

    $fullYear = self::getFullYear((int) $ind, (int) $year);
    if (false === $fullYear) {
      return false;
    }

    if (!self::isValidDate((int) $day, (int) $month, $fullYear, $validTypes)) {
      return false;
    }

    if (Mod11Helper::calculateControlDigitMod11(\substr($nin, 0, 9), self::K1_WEIGHTS) !== $k1) {
      return false;
    }

    if (Mod11Helper::calculateControlDigitMod11(\substr($nin, 0, 10), self::K2_WEIGHTS) !== $k2) {
      return false;
    }

    return true;
  }

  /**
   * @param int $ind
   * @param int $year
   *
   * @return int|false
   */
  private static function getFullYear(int $ind, int $year): int|false {
    if ($ind >= 0 && $ind <= 499) {
      return 1900 + $year;
    }

    if ($ind >= 500 && $ind <= 749 && $year >= 54 && $year <= 99) {
      return 1800 + $year;
    }

    if ($ind >= 500 && $ind <= 999 && $year >= 0 && $year <= 39) {
      return 2000 + $year;
    }

    if ($ind >= 900 && $ind <= 999 && $year >= 40 && $year <= 99) {
      return 1900 + $year;
    }

    return false;
  }

  /**
   * @param int   $day
   * @param int   $month
   * @param int   $year
   * @param array $validTypes
   *
   * @return bool
   */
  private static function isValidDate(int $day, int $month, int $year, array $validTypes): bool {
    if ($day > 40 && \in_array(self::TYPE_D_NUMBER, $validTypes, true)) {
      $day -= 40;
    }

    if ($month > 80 && \in_array(self::TYPE_NPR_SYNTHETIC, $validTypes, true)) {
      $month -= 80;
    }

    return checkdate($month, $day, $year);
  }

}
