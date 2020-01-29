<?php


namespace HalloVerden\ValidatorConstraintsBundle\Services;

use Doctrine\Common\Annotations\Reader;

class ClassInfoService implements ClassInfoServiceInterface {

  /**
   * @var Reader
   */
  private $reader;

  /**
   * @var array
   */
  private $reflectionClasses = [];


  /**
   * ClassInfoService constructor.
   *
   * @param Reader $reader
   */
  public function __construct(Reader $reader) {
    $this->reader = $reader;
  }

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
  public function getClassPropertiesAnnotations(string $class): array {
    if (isset($this->reflectionClasses[$class]['propertyAnnotations'])) {
      return $this->reflectionClasses[$class]['propertyAnnotations'];
    }

    $annotations = [];
    foreach ($this->getReflectionClassProperties($class) as $property) {
      $annotations[$property->getName()] = $this->reader->getPropertyAnnotations($property);
    }
    return $this->reflectionClasses[$class]['propertyAnnotations'] = $annotations;
  }

  /**
   * @param string $class
   *
   * @return array
   * @throws \ReflectionException
   */
  public function getClassAnnotations(string $class): array {
    if (isset($this->reflectionClasses[$class]['classAnnotations'])) {
      return $this->reflectionClasses[$class]['classAnnotations'];
    }

    return $this->reflectionClasses[$class]['classAnnotations'] = $this->reader->getClassAnnotations($this->getReflectionClass($class));
  }

  /**
   * @param string        $class
   * @param string        $property
   * @param \Closure|null $filter
   *
   * @return array|null
   * @throws \ReflectionException
   */
  public function getClassPropertyAnnotations(string $class, string $property, \Closure $filter = null): ?array {
    $propertyAnnotations = $this->getClassPropertiesAnnotations($class);

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
   * @param string $annotation
   *
   * @return mixed|null
   * @throws \ReflectionException
   */
  public function getClassPropertyAnnotation(string $class, string $property, string $annotation) {
    $propertyAnnotations = $this->getClassPropertyAnnotations($class, $property);

    if ($propertyAnnotations === null) {
      return null;
    }

    foreach ($propertyAnnotations as $propertyAnnotation) {
      if ($propertyAnnotation instanceof $annotation) {
        return $propertyAnnotation;
      }
    }

    return null;
  }

}
