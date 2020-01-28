<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Traversable;

/**
 * Class UniqueEntityValidator
 * @package App\Validator\Constraints
 */
class UniqueEntityValidator extends ConstraintValidator {
  private $registry;

  public function __construct(ManagerRegistry $registry) {

    $this->registry = $registry;
  }

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed $value The value that should be validated. Should be an entity or an array of values.
   * @param Constraint $constraint The constraint for the validation
   * @throws \Exception
   */
  public function validate($value, Constraint $constraint): void {
    if (!$constraint instanceof UniqueEntity) {
      throw new UnexpectedTypeException($constraint, UniqueEntity::class);
    }

    if (!\is_array($constraint->fields) && !\is_string($constraint->fields)) {
      throw new UnexpectedTypeException($constraint->fields, 'array');
    }

    if (null !== $constraint->errorPath && !\is_string($constraint->errorPath)) {
      throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
    }

    $fields = (array)$constraint->fields;

    if (0 === \count($fields)) {
      throw new ConstraintDefinitionException('At least one field has to be specified.');
    }

    if (null === $value) {
      return;
    }

    if (is_array($value)) {
      if (!$constraint->entityClass || !class_exists($constraint->entityClass)) {
        throw new ConstraintDefinitionException('$constraint->entityClass must be defined when validating an array');
      }
    }

    $em = $this->getManager($constraint, $value);

    if (is_array($value)) {
      $class = $em->getClassMetadata($constraint->entityClass);
    } else {
      $class = $em->getClassMetadata(get_class($value));
    }

    /* @var $class ClassMetadata */
    if (!$criteria = $this->getCriteria($fields, $class, $value, $constraint, $em)) {
      return;
    }

    $repository = $this->getRepository($constraint, $em, $class);
    if (!$fetchedEntity = $this->fetchEntity($repository, $constraint, $criteria, $value)) {
      return;
    }

    $this->buildViolation($constraint, $fields, $criteria, $em, $class, $fetchedEntity, $value);
  }

  /**
   * @param UniqueEntity $constraint
   * @param $value
   * @return ObjectManager
   */
  private function getManager(UniqueEntity $constraint, $value): ObjectManager {
    $em = null;

    if ($constraint->em) {
      $em = $this->registry->getManager($constraint->em);

      if (!$em) {
        throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
      }
    } else if (!is_array($value)) {
      $em = $this->registry->getManagerForClass(get_class($value));
    } else {
      $em = $this->registry->getManagerForClass($constraint->entityClass);
    }

    if (!$em) {
      if (is_array($value)) {
        throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', $constraint->entityClass));
      } else {
        throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', get_class($value)));
      }
    }

    return $em;
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
  private function getCriteria(array $fields, ClassMetadata $class, $value, UniqueEntity $constraint, ObjectManager $em) {
    $criteria = [];
    $hasNullValue = false;

    foreach ($fields as $fieldName) {
      if (is_array($value)) {
        $fieldValue = isset($value[$fieldName]) ? $value[$fieldName] : null;
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
   * @param UniqueEntity $constraint
   * @param ObjectManager $em
   * @param ClassMetadata $class
   * @return ObjectRepository
   */
  private function getRepository(UniqueEntity $constraint, ObjectManager $em, ClassMetadata $class): ObjectRepository {
    if (null !== $constraint->entityClass) {
      /* Retrieve repository from given entity name.
       * We ensure the retrieved repository can handle the entity
       * by checking the class is the same, or subclass of the supported entity.
       */
      $repository = $em->getRepository($constraint->entityClass);
      $supportedClass = $repository->getClassName();

      $entity = $class->getReflectionClass();

      if (!$entity->newInstanceWithoutConstructor() instanceof $supportedClass) {
        throw new ConstraintDefinitionException(sprintf('The "%s" entity repository does not support the "%s" entity. The entity should be an instance of or extend "%s".', $constraint->entityClass, $class->getName(), $supportedClass));
      }
    } else {
      $repository = $em->getRepository($class->getName());
    }

    return $repository;
  }

  /**
   * @param ObjectRepository $repository
   * @param UniqueEntity $constraint
   * @param array $criteria
   * @param $value
   *
   * @return array|\Countable|\Iterator|mixed|Traversable|null
   */
  private function fetchEntity(ObjectRepository $repository, UniqueEntity $constraint, array $criteria, $value) {
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
   * @param UniqueEntity $constraint
   * @param array $fields
   * @param array $criteria
   * @param ObjectManager $em
   * @param ClassMetadata $class
   * @param $result
   * @param $value
   */
  private function buildViolation(UniqueEntity $constraint, array $fields, array $criteria, ObjectManager $em, ClassMetadata $class, $result, $value) {
    $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : is_array($value) ? '[' . $fields[0] . ']' : $fields[0];
    $invalidValue = isset($criteria[$errorPath]) ? $criteria[$errorPath] : $criteria[$fields[0]];

    $this->context->buildViolation($constraint->message)
      ->atPath($errorPath)
      ->setParameter('{{ value }}', $this->formatWithIdentifiers($em, $class, $invalidValue))
      ->setInvalidValue($invalidValue)
      ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
      ->setCause($result)
      ->addViolation();
  }

  /**
   * @param ObjectManager $em
   * @param ClassMetadata $class
   * @param $value
   * @return string
   */
  private function formatWithIdentifiers(ObjectManager $em, ClassMetadata $class, $value) {
    if (!\is_object($value) || $value instanceof \DateTimeInterface) {
      return $this->formatValue($value, self::PRETTY_DATE);
    }

    if (method_exists($value, '__toString')) {
      return (string)$value;
    }

    if ($class->getName() !== $idClass = get_class($value)) {
      // non unique value might be a composite PK that consists of other entity objects
      if ($em->getMetadataFactory()->hasMetadataFor($idClass)) {
        $identifiers = $em->getClassMetadata($idClass)->getIdentifierValues($value);
      } else {
        // this case might happen if the non unique column has a custom doctrine type and its value is an object
        // in which case we cannot get any identifiers for it
        $identifiers = [];
      }
    } else {
      $identifiers = $class->getIdentifierValues($value);
    }

    if (!$identifiers) {
      return sprintf('object("%s")', $idClass);
    }

    array_walk($identifiers, function (&$id, $field) {
      if (!is_object($id) || $id instanceof \DateTimeInterface) {
        $idAsString = $this->formatValue($id, self::PRETTY_DATE);
      } else {
        $idAsString = sprintf('object("%s")', get_class($id));
      }

      $id = sprintf('%s => %s', $field, $idAsString);
    });

    return sprintf('object("%s") identified by (%s)', $idClass, implode(', ', $identifiers));
  }
}
