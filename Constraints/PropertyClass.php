<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

class PropertyClass extends Constraint {
  const ERROR_INVALID_ATTRIBUTE = '7b9f3c3d-1f7f-4d13-a71c-33eb807d3bd4';

  protected static $errorNames = [
    self::ERROR_INVALID_ATTRIBUTE => 'ERROR_INVALID_ATTRIBUTE',
  ];

  public $propertyPath;
  public $classes = [];
  public $groupsToGet;

  public function getRequiredOptions(): array {
    return ['propertyPath', 'classes'];
  }
}
