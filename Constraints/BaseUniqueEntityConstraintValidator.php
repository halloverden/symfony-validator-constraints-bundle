<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Traversable;

/**
 * Class BaseUniqueEntityConstraintValidator
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
abstract class BaseUniqueEntityConstraintValidator extends ConstraintValidator {

  /**
   * @var ManagerRegistry
   */
  protected $registry;

  /**
   * @var PropertyAccessorInterface|null
   */
  protected $propertyAccessor;

  /**
   * BaseUniqueEntityConstraintValidator constructor
   *
   * @param ManagerRegistry $registry
   * @param PropertyAccessorInterface|null $propertyAccessor
   */
  public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor = null) {
    $this->registry = $registry;
    $this->propertyAccessor = $propertyAccessor;
  }

  /**
   * @param array $fields
   * @param ClassMetadata $class
   * @param $value
   * @param UniqueEntity $constraint
   * @param ObjectManager $em
   *
   * @return array|null
   */
  protected function getCriteria(array $fields, ClassMetadata $class, $value, BaseUniqueEntityConstraint $constraint, ObjectManager $em) {
    $criteria = [];
    $hasNullValue = false;

    foreach ($fields as $fieldName) {
      if (is_array($value)) {
        if (isset($value[$fieldName])) {
          $fieldValue = $value[$fieldName];
        } else {
          try {
            $propertyPath = new PropertyPath($fieldName);
          } catch (InvalidArgumentException | InvalidPropertyPathException $e) {
            return null;
          }

          $fieldValue = $this->getValueFromPropertyPath($value, $propertyPath);

          // If we got value from property path the fieldName is the last element in the path.
          $elements = $propertyPath->getElements();
          $fieldName = end($elements);
        }
      } else {
        if (!$class->hasField($fieldName) && !$class->hasAssociation($fieldName)) {
          throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $fieldName));
        }

        $fieldValue = $class->reflFields[$fieldName]->getValue($value);
      }

      if (null === $fieldValue) {
        $hasNullValue = true;
      }

      if ($constraint->ignoreNull && null === $fieldValue) {
        continue;
      }

      $criteria[$fieldName] = $fieldValue;

      if (null !== $criteria[$fieldName] && $class->hasAssociation($fieldName)) {
        /* Ensure the Proxy is initialized before using reflection to
         * read its identifiers. This is necessary because the wrapped
         * getter methods in the Proxy are being bypassed.
         */
        $em->initializeObject($criteria[$fieldName]);
      }
    }

    // validation doesn't fail if one of the fields is null and if null values should be ignored
    if ($hasNullValue && $constraint->ignoreNull) {
      return null;
    }

    // skip validation if there are no criteria (this can happen when the
    // "ignoreNull" option is enabled and fields to be checked are null
    if (empty($criteria)) {
      return null;
    }

    return $criteria;
  }

  /**
   * @param ObjectRepository $repository
   * @param UniqueEntity $constraint
   * @param array $criteria
   * @param $value
   *
   * @return array|\Countable|\Iterator|mixed|Traversable|null
   * @throws \Exception
   */
  protected function fetchEntity(ObjectRepository $repository, BaseUniqueEntityConstraint $constraint, array $criteria, $value) {
    $result = $repository->{$constraint->repositoryMethod}($criteria);

    if ($result instanceof \IteratorAggregate) {
      $result = $result->getIterator();
    }

    /* If the result is a MongoCursor, it must be advanced to the first
     * element. Rewinding should have no ill effect if $result is another
     * iterator implementation.
     */
    if ($result instanceof \Iterator) {
      $result->rewind();
      if ($result instanceof \Countable && 1 < count($result)) {
        $result = [$result->current(), $result->current()];
      } else {
        $result = $result->current();
        $result = null === $result ? [] : [$result];
      }
    } elseif (is_array($result)) {
      reset($result);
    } else {
      $result = null === $result ? [] : [$result];
    }

    if ($this->isUniqueResult($result, $value)) {
      return null;
    }

    return $result;
  }

  /**
   * @param $result
   * @param $value
   *
   * @return bool
   */
  private function isUniqueResult($result, $value): bool {
    /* If no entity matched the query criteria or a single entity matched,
     * which is the same as the entity being validated, the criteria is
     * unique.
     */
    if (!$result || count($result) === 0) {
      return true;
    } else if (count($result) > 1) {
      return false;
    } else {
      if (!is_array($value)) {
        return current($result) === $value;
      } else {
        // If we're validating an array, and not an entity,
        // the existence of a result means it's not unique.
        return false;
      }
    }
  }

  /**
   * @param array  $data
   * @param string $propertyPath
   *
   * @return mixed|null
   */
  private function getValueFromPropertyPath(array $data, string $propertyPath) {
    $propertyAccessor = $this->getPropertyAccessor();

    if (!$propertyAccessor) {
      return null;
    }

    if ($propertyAccessor->isReadable($data, $propertyPath)) {
      return $propertyAccessor->getValue($data, $propertyPath);
    }

    return null;
  }

  /**
   * @return PropertyAccessorInterface|null
   */
  private function getPropertyAccessor(): ?PropertyAccessorInterface {
    if (!$this->propertyAccessor && class_exists(PropertyAccess::class)) {
      return $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    return $this->propertyAccessor;
  }

}
