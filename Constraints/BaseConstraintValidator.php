<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use HalloVerden\ValidatorConstraintsBundle\Services\ClassInfoServiceInterface;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

abstract class BaseConstraintValidator extends ConstraintValidator {

  /**
   * @var ClassInfoServiceInterface
   */
  private $classInfoService;

  /**
   * @var PropertyAccessorInterface
   */
  private $propertyAccessor;


  /**
   * BaseConstraintValidator constructor.
   *
   * @param ClassInfoServiceInterface $classInfoService
   * @param PropertyAccessorInterface $propertyAccessor
   */
  public function __construct(ClassInfoServiceInterface $classInfoService, PropertyAccessorInterface $propertyAccessor) {
    $this->classInfoService = $classInfoService;
    $this->propertyAccessor = $propertyAccessor;
  }

  /**
   * @param $class
   * @param array $groups
   * @return array
   */
  protected function getClassConstraintsForClass($class, array $groups): array {
    $annotations = $this->classInfoService->getClassAttributes($class);

    return array_filter($annotations, function (\ReflectionAttribute $attribute) use ($groups) {
      $instance = $attribute->newInstance();
      return $instance instanceof Constraint && array_intersect($groups, $instance->groups ?: []);
    });
  }

  /**
   * @param       $class
   * @param array $groups
   *
   * @return Constraint[]
   */
  protected final function getPropertyConstraintsForClass($class, array $groups): array {
    $constraints = [];
    foreach ($this->classInfoService->getClassPropertiesAttributes($class) as $property => $attributes) {

      if (class_exists(SerializedName::class)) {
        $serializedName = $this->classInfoService->getClassPropertyAttribute($class, $property, SerializedName::class);

        if ($serializedName) {
          $instance = $serializedName->newInstance();

          if ($instance instanceof SerializedName) {
            $property = $instance->name;
          }
        }
      }

      $constraints[$property] = array_filter($attributes, function ($attribute) use ($groups) {
        return $attribute instanceof Constraint && array_intersect($groups, $attribute->groups ?: []);
      });
    }

    return $constraints;
  }

  /**
   * @param string $propertyPath
   *
   * @return mixed
   */
  protected final function getPropertyValue(string $propertyPath) {
    if (null === $root = $this->context->getObject()) {
      throw new \RuntimeException('Unable to get root');
    }

    return $this->propertyAccessor->getValue($root, $propertyPath);
  }

}
