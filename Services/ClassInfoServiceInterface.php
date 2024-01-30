<?php


namespace HalloVerden\ValidatorConstraintsBundle\Services;


interface ClassInfoServiceInterface {

  /**
   * @param string $class
   *
   * @return \ReflectionClass
   */
  public function getReflectionClass(string $class): \ReflectionClass;

  /**
   * @param string $class
   *
   * @return array
   */
  public function getReflectionClassProperties(string $class): array;

  /**
   * @param string $class
   * @param string $property
   *
   * @return \ReflectionProperty|null
   */
  public function getReflectionClassProperty(string $class, string $property): ?\ReflectionProperty;

  /**
   * @param string $class
   *
   * @return array
   */
  public function getClassPropertiesAttributes(string $class): array;

  /**
   * @param string $class
   *
   * @return array
   */
  public function getClassAttributes(string $class): array;

  /**
   * @param string        $class
   * @param string        $property
   * @param \Closure|null $filter
   *
   * @return array|null
   */
  public function getClassPropertyAttributes(string $class, string $property, \Closure $filter = null): ?array;

  /**
   * @param string $class
   * @param string $property
   * @param string $attribute
   *
   * @return \ReflectionAttribute|null
   */
  public function getClassPropertyAttribute(string $class, string $property, string $attribute): \ReflectionAttribute|null;
}
