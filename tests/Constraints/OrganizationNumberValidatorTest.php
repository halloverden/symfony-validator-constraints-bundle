<?php

namespace HalloVerden\ValidatorConstraintsBundle\Tests\Constraints;

use HalloVerden\ValidatorConstraintsBundle\Constraint\OrganizationNumber;
use HalloVerden\ValidatorConstraintsBundle\Constraint\OrganizationNumberValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class OrganizationNumberValidatorTest extends ConstraintValidatorTestCase {

  protected function createValidator(): OrganizationNumberValidator {
    return new OrganizationNumberValidator();
  }

  /**
   * @param string $organizationNumber
   *
   * @return void
   * @dataProvider validOrganizationNumbersProvider
   */
  public function testValidate_validOrganizationNumbers_shouldNotAssertViolation(string $organizationNumber): void {
    $this->validator->validate($organizationNumber, new OrganizationNumber());
    $this->assertNoViolation();
  }

  /**
   * @param string $organizationNumber
   *
   * @return void
   * @dataProvider invalidOrganizationNumbersProvider
   */
  public function testValidate_invalidOrganizationNumbers_shouldAssertViolation(string $organizationNumber): void {
    $this->validator->validate($organizationNumber, new OrganizationNumber());
    $this->buildViolation('This is not a valid organization number')
      ->setInvalidValue($organizationNumber)
      ->setCode(OrganizationNumber::ERROR_INVALID_ORGANIZATION_NUMBER)
      ->setParameter('{{ value }}', '"' . $organizationNumber . '"')
      ->assertRaised();
  }

  /**
   * @return \Generator
   */
  public function validOrganizationNumbersProvider(): \Generator {
    yield ['915338534'];
    yield ['986930957'];
    yield ['990906270'];
    yield ['992169311'];
    yield ['982689198'];
    yield ['911600773'];
    yield ['929835980'];
    yield ['973054694'];
    yield ['917801126'];
    yield ['924765739'];
    yield ['992949406'];
    yield ['918092560'];
    yield ['892367302'];
    yield ['922687609'];
    yield ['914145503'];
    yield ['994752774'];
    yield ['995344610'];
    yield ['927261650'];
    yield ['991603220'];
    yield ['915931820'];
    yield ['924544600'];
    yield ['930142190'];
    yield ['925607630'];
  }

  /**
   * @return \Generator
   */
  public function invalidOrganizationNumbersProvider(): \Generator {
    yield ['123456789'];
    yield ['abc'];
    yield ['1234'];
    yield ['4564654161616816'];
    yield ['dsanmofbauyv'];
    yield ['973 0 54 694'];
    yield ['x'];
    yield ['000000400'];
  }

}
