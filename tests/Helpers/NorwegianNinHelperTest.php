<?php

namespace HalloVerden\ValidatorConstraintsBundle\Tests\Helpers;

use HalloVerden\ValidatorConstraintsBundle\Helper\NorwegianNinHelper;
use PHPUnit\Framework\TestCase;

class NorwegianNinHelperTest extends TestCase {

  /**
   * @param string             $nin
   * @param \DateTimeInterface $expectedBirthDate
   *
   * @return void
   * @dataProvider ninsWithValidBirthDateProvider
   */
  public function testGetBirthDate_ninWithValidBirthDatePart_shouldReturnBirthDate(string $nin, \DateTimeInterface $expectedBirthDate): void {
    $birthDate = NorwegianNinHelper::getBirthDate($nin);
    $this->assertNotNull($birthDate);
    $this->assertEquals($expectedBirthDate, $birthDate);
  }

  /**
   * @param string $nin
   *
   * @return void
   * @dataProvider ninsWithInvalidBirthDateProvider
   */
  public function testGetBirthDate_ninWithInvalidBirthDatePart_shouldReturnNull(string $nin): void {
    $birthDate = NorwegianNinHelper::getBirthDate($nin);
    $this->assertNull($birthDate);
  }

  public function ninsWithValidBirthDateProvider(): \Generator {
    yield ['26038908064', \DateTime::createFromFormat('!Y-m-d', '1989-03-26')];
    yield ['15015534769', \DateTime::createFromFormat('!Y-m-d', '1955-01-15')];
    yield ['10109131461', \DateTime::createFromFormat('!Y-m-d', '1991-10-10')];
    yield ['21046737609', \DateTime::createFromFormat('!Y-m-d', '1967-04-21')];
    yield ['20105041633', \DateTime::createFromFormat('!Y-m-d', '1950-10-20')];
    yield ['07064327245', \DateTime::createFromFormat('!Y-m-d', '1943-06-07')];
    yield ['04099933971', \DateTime::createFromFormat('!Y-m-d', '1999-09-04')];
    yield ['15056310344', \DateTime::createFromFormat('!Y-m-d', '1963-05-15')];
    yield ['18036913696', \DateTime::createFromFormat('!Y-m-d', '1969-03-18')];
    yield ['04029217063', \DateTime::createFromFormat('!Y-m-d', '1992-02-04')];
    yield ['01100605747', \DateTime::createFromFormat('!Y-m-d', '1906-10-01')];
    yield ['28085646186', \DateTime::createFromFormat('!Y-m-d', '1956-08-28')];
    yield ['23103808901', \DateTime::createFromFormat('!Y-m-d', '1938-10-23')];
    yield ['14090476417', \DateTime::createFromFormat('!Y-m-d', '2004-09-14')];
    yield ['23084839108', \DateTime::createFromFormat('!Y-m-d', '1948-08-23')];
    yield ['01120858668', \DateTime::createFromFormat('!Y-m-d', '2008-12-01')];
    yield ['19083237611', \DateTime::createFromFormat('!Y-m-d', '1932-08-19')];
    yield ['25122071521', \DateTime::createFromFormat('!Y-m-d', '2020-12-25')];

    // NPR Synthetic NINs
    yield ['20894696649', \DateTime::createFromFormat('!Y-m-d', '1946-09-20')];
    yield ['05820148128', \DateTime::createFromFormat('!Y-m-d', '1901-02-05')];
    yield ['26836395742', \DateTime::createFromFormat('!Y-m-d', '1963-03-26')];
    yield ['23827898996', \DateTime::createFromFormat('!Y-m-d', '1978-02-23')];

    // D-numbers
    yield ['64040143929', \DateTime::createFromFormat('!Y-m-d', '1901-04-24')];
    yield ['70126722743', \DateTime::createFromFormat('!Y-m-d', '1967-12-30')];
    yield ['52025824997', \DateTime::createFromFormat('!Y-m-d', '1958-02-12')];
    yield ['64086107838', \DateTime::createFromFormat('!Y-m-d', '1961-08-24')];
  }

  /**
   * @return \Generator
   */
  public function ninsWithInvalidBirthDateProvider(): \Generator {
    yield ['123'];
    yield ['12345678901'];
    yield ['test'];
    yield ['99999907838'];
  }

}
