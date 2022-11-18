<?php

namespace HalloVerden\ValidatorConstraintsBundle\Tests\Constraints;

use HalloVerden\ValidatorConstraintsBundle\Constraints\Nin;
use HalloVerden\ValidatorConstraintsBundle\Constraints\NinValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NinValidatorTest extends ConstraintValidatorTestCase {

  protected function createValidator(): NinValidator {
    return new NinValidator();
  }

  /**
   * @param string $nin
   *
   * @return void
   * @dataProvider validNinsProvider
   */
  public function testValidate_validNins_shouldNotAssertViolations(string $nin): void {
    $this->validator->validate($nin, new Nin());
    $this->assertNoViolation();
  }

  /**
   * @param string $nin
   *
   * @return void
   * @dataProvider invalidNinsProvider
   */
  public function testValidate_invalidNins_shouldAssertViolations(string $nin): void {
    $this->validator->validate($nin, new Nin());
    $this->buildViolation('This is not a valid nin')
      ->setInvalidValue($nin)
      ->setCode(Nin::ERROR_INVALID_NIN)
      ->setParameter('{{ value }}', '"' . $nin . '"')
      ->assertRaised();
  }

  /**
   * @return \Generator
   */
  public function validNinsProvider(): \Generator {
    yield ['26038908064'];
    yield ['15015534769'];
    yield ['10109131461'];
    yield ['21046737609'];
    yield ['20105041633'];
    yield ['07064327245'];
    yield ['04099933971'];
    yield ['15056310344'];
    yield ['18036913696'];
    yield ['04029217063'];
    yield ['01100605747'];
    yield ['28085646186'];
    yield ['23103808901'];
    yield ['14090476417'];
    yield ['23084839108'];
    yield ['01120858668'];
    yield ['19083237611'];
    yield ['25122071521'];

    // NPR Synthetic NINs
    yield ['20894696649'];
    yield ['05820148128'];
    yield ['26836395742'];
    yield ['23827898996'];

    // D-numbers
    yield ['64040143929'];
    yield ['70126722743'];
    yield ['52025824997'];
    yield ['64086107838'];
  }

  /**
   * @return \Generator
   */
  public function invalidNinsProvider(): \Generator {
    yield ['123'];
    yield ['12345678901'];
    yield ['test'];
    yield ['18048712345'];
    yield ['28085646123'];
    yield ['19083237612'];
    yield ['64086107839'];
    yield ['64086107848'];
    yield ['23827898997'];
    yield ['23827898916'];
  }

}
