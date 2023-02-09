<?php

namespace HalloVerden\ValidatorConstraintsBundle\Helpers;

final class NorwegianNinHelper {
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
    if (null === self::getBirthDate($nin, $validTypes)) {
      return false;
    }

    $k1 = \substr($nin, 9, 1);
    $k2 = \substr($nin, 10, 1);

    if (Mod11Helper::calculateControlDigitMod11(\substr($nin, 0, 9), self::K1_WEIGHTS) !== $k1) {
      return false;
    }

    if (Mod11Helper::calculateControlDigitMod11(\substr($nin, 0, 10), self::K2_WEIGHTS) !== $k2) {
      return false;
    }

    return true;
  }

  /**
   * @param string $nin
   * @param array  $validTypes
   *
   * @return \DateTimeInterface|null
   */
  public static function getBirthDate(string $nin, array $validTypes = self::TYPES): ?\DateTimeInterface {
    if (!\preg_match('/^[0-9]{11}$/', $nin)) {
      return null;
    }

    $day = self::getDay($nin, $validTypes);
    $month = self::getMonth($nin, $validTypes);
    $year = self::getYear($nin);

    if (false === $year) {
      return null;
    }

    if (!checkdate($month, $day, $year)) {
      return null;
    }

    try {
      return \DateTime::createFromFormat('Y-m-d', \sprintf('%d-%02d-%02d', $year, $month, $day));
    } catch (\Exception) {
      return null;
    }
  }

  /**
   * @param string   $nin
   * @param string[] $validTypes
   *
   * @return int
   */
  private static function getDay(string $nin, array $validTypes): int {
    $day = (int) \substr($nin, 0, 2);

    if ($day > 40 && \in_array(self::TYPE_D_NUMBER, $validTypes, true)) {
      return $day - 40;
    }

    return $day;
  }

  /**
   * @param string   $nin
   * @param string[] $validTypes
   *
   * @return int
   */
  private static function getMonth(string $nin, array $validTypes): int {
    $month = (int) \substr($nin, 2, 2);

    if ($month > 80 && \in_array(self::TYPE_NPR_SYNTHETIC, $validTypes, true)) {
      return $month - 80;
    }

    return $month;
  }

  /**
   * @param string $nin
   *
   * @return int|false
   */
  private static function getYear(string $nin): int|false {
    $year = (int) \substr($nin, 4, 2);
    $ind = (int) \substr($nin, 6, 3);

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

}
