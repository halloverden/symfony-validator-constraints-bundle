<?php


namespace HalloVerden\ValidatorConstraintsBundle\Services;

use Doctrine\Common\Annotations\Reader;

class ClassInfoService implements ClassInfoServiceInterface {
  private array $reflectionClasses = [];

  /**
   * @param string $class
   *
   * @return \ReflectionClass
   * @throws \ReflectionException
   */
  public function getReflectionClass(string $class): \ReflectionClass {
    if (isset($this->reflectionClasses[$class]['reflectionClass'])) {
      return $this->reflectionClasses[$class]['reflectionClass'];
    }

    return $this->reflectionClasses[$class]['reflectionClass'] = new \ReflectionClass($class);
  }

  /**
   * @param string $class
   *
   * @return \ReflectionProperty[]
   * @throws \ReflectionException
   */
  public function getReflectionClassProperties(string $class): array {
    if (isset($this->reflectionClasses[$class]['reflectionProperties'])) {
      return $this->reflectionClasses[$class]['reflectionProperties'];
    }

    return $this->reflectionClasses[$class]['reflectionProperties'] = $this->getReflectionClass($class)->getProperties();
  }

  /**
   * @param string $class
   * @param string $property
   *
   * @return \ReflectionProperty|null
   * @throws \ReflectionException
   */
  public function getReflectionClassProperty(string $class, string $property): ?\ReflectionProperty {
    $reflectionProperties = $this->getReflectionClassProperties($class);

    if (!isset($reflectionProperties[$property])) {
      return null;
    }

    return $reflectionProperties[$property];
  }

  /**
   * @param string $class
   *
   * @return array
   * @throws \ReflectionException
   */
  public function getClassPropertiesAttributes(string $class): array {
    if (isset($this->reflectionClasses[$class]['propertyAttributes'])) {
      return $this->reflectionClasses[$class]['propertyAttributes'];
    }

    $annotations = [];
    foreach ($this->getReflectionClassProperties($class) as $property) {
      $annotations[$property->getName()] = $property->getAttributes();
    }
    return $this->reflectionClasses[$class]['propertyAttributes'] = $annotations;
  }

  /**
   * @param string $class
   *
   * @return array
   * @throws \ReflectionException
   */
  public function getClassAttributes(string $class): array {
    if (isset($this->reflectionClasses[$class]['classAttributes'])) {
      return $this->reflectionClasses[$class]['classAttributes'];
    }

    return $this->reflectionClasses[$class]['classAttributes'] = $this->getReflectionClass($class)->getAttributes();
  }

  /**
   * @param string        $class
   * @param string        $property
   * @param \Closure|null $filter
   *
   * @return array|null
   * @throws \ReflectionException
   */
  public function getClassPropertyAttributes(string $class, string $property, \Closure $filter = null): ?array {
    $propertyAnnotations = $this->getClassPropertiesAttributes($class);

    if (!isset($propertyAnnotations[$property])) {
      return null;
    }

    if ($filter !== null) {
      return array_filter($propertyAnnotations[$property], $filter);
    }

    return $propertyAnnotations[$property];
  }

  /**
   * @param string $class
   * @param string $property
   * @param string $attribute
   *
   * @return \ReflectionAttribute|null
   * @throws \ReflectionException
   */
  public function getClassPropertyAttribute(string $class, string $property, string $attribute): \ReflectionAttribute|null {
    $propertyAttributes = $this->getClassPropertyAttributes($class, $property);
    $propertyAttributes = $this->getClassPropertyAttributes($class, $property);

    if ($propertyAttributes === null) {
      return null;
    }

    foreach ($propertyAttributes as $propertyAttribute) {
      /* @var $propertyAttribute \ReflectionAttribute */
      if ($propertyAttribute->getName() instanceof $attribute) {
        return $propertyAttribute;
      }
    }

    return null;
  }

}
