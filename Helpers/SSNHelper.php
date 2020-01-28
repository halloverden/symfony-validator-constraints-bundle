<?php


namespace HalloVerden\ValidatorConstraintsBundle\Helpers;


class SSNHelper {
  /**
   * Returns the personal id part of the ssn
   *
   * @param string $SSN
   *
   * @return string
   */
  public static function getPIDFromSSN($SSN) {
    return substr($SSN, 6);
  }

  /**
   * Returns the birth date part of the ssn,
   * either as a string, or as a DateTime object
   *
   * @param      $SSN
   * @param      $format
   * @param bool $asDateTimeObject
   *
   * @return \DateTime|string
   * @throws \Exception
   */
  public static function getBirthDateFromSSN($SSN, $format, $asDateTimeObject = false) {
    // Splitting birthDate and ssn
    $day = substr($SSN, 0, 2);
    $month = substr($SSN, 2, 2);
    $year = substr($SSN, 4, 2);
    $ind = substr($SSN, 6, 3);

    // Checking for D-number
    if (intval($day) >= 40) {
      $day = intval($day) >= 50 ? intval($day) - 40 : '0' . strval(intval($day) - 40);
    }

    $indInt = intval($ind);
    $yearInt = intval($year);

    // Getting the correct year
    if ($indInt >= 0 && $indInt <= 499) {
      $year = '19' . $year;
    } else if ($indInt >= 500 && $indInt <= 749 && $yearInt >= 54 && $yearInt <= 99) {
      $year = '18' . $year;
    } else if ($indInt >= 500 && $indInt <= 999 && $yearInt >= 0 && $yearInt <= 39) {
      $year = '20' . $year;
    } else if ($indInt >= 900 && $indInt <= 999 && $yearInt >= 40 && $yearInt <= 99) {
      $year = '19' . $year;
    }

    $date = new \DateTime($year . '-' . $month . '-' . $day);

    if ($asDateTimeObject) {
      return $date;
    } else {
      return $date->format($format);
    }
  }

  public function getSSN($birthDate, $PID) {
    /* @var $birthDate \DateTime */
    return $birthDate->format('dmY') . $PID;
  }

  /**
   * @param \DateTime $birthDate
   * @param           $ssn
   * @param           $isDNumber
   *
   * @return bool
   */
  public static function validateSSN(\DateTime $birthDate, $ssn, $isDNumber) {
    // Checking length of ssn
    if (strlen($ssn) !== 5) {
      return false;
    }

    // Splitting birthDate and ssn
    $day = $birthDate->format('d');
    $month = $birthDate->format('m');
    $year = $birthDate->format('y');
    $ind = substr($ssn, 0, 3);

    $indInt = intval($ind);
    $yearInt = intval($year);

    if (!($indInt >= 0 && $indInt <= 499) &&
      !($indInt >= 500 && $indInt <= 749 && $yearInt >= 54 && $yearInt <= 99) &&
      !($indInt >= 500 && $indInt <= 999 && $yearInt >= 0 && $yearInt <= 39) &&
      !($indInt >= 900 && $indInt <= 999 && $yearInt >= 40 && $yearInt <= 99)
    ) {
      return false;
    }

    $d1 = intval(substr($day, 0, 1));

    if ($isDNumber) {
      $d1 +=4;
    }

    $d2 = intval(substr($day, 1, 1));
    $m1 = intval(substr($month, 0, 1));
    $m2 = intval(substr($month, 1, 1));
    $y1 = intval(substr($year, 0, 1));
    $y2 = intval(substr($year, 1, 1));
    $i1 = intval(substr($ind, 0, 1));
    $i2 = intval(substr($ind, 1, 1));
    $i3 = intval(substr($ind, 2, 1));
    $c1 = intval(substr($ssn, 3, 1));
    $c2 = intval(substr($ssn, 4, 1));

    // Calculate control check c1
    $c1calc = 11 -
      (((3 * $d1) + (7 * $d2) + (6 * $m1) + $m2 + (8 * $y1) + (9 * $y2) + (4 * $i1) + (5 * $i2) + (2 * $i3)) % 11);

    if ($c1calc === 11) {
      $c1calc = 0;
    }

    if ($c1calc === 10) {
      return false;
    }

    if ($c1 !== $c1calc) {
      return false;
    }

    // Calculate control check c2
    $c2calc = 11 -
      (((5 * $d1) + (4 * $d2) + (3 * $m1) + (2 * $m2) + (7 * $y1) + (6 * $y2) + (5 * $i1) + (4 * $i2) + (3 * $i3) +
          (2 * $c1calc)) % 11);

    if ($c2calc === 11) {
      $c2calc = 0;
    }

    if ($c2calc === 10) {
      return false;
    }

    return $c2 === $c2calc;
  }

  /**
   * Check if incoming ssn is a D-number
   *
   * @param $ssn
   * @return bool
   */
  public static function isDNumber($ssn) {
    return intval(substr($ssn, 0, 1)) >= 4;
  }
}
