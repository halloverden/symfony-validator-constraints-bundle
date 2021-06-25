<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class UniqueEntityValidator
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
class UniqueEntityValidator extends BaseUniqueEntityConstraintValidator {

  /**
   * UniqueEntityValidator constructor
   *
   * @param ManagerRegistry $registry
   * @param PropertyAccessorInterface|null $propertyAccessor
   */
  public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor = null) {
    $this->registry = $registry;
    $this->propertyAccessor = $propertyAccessor;
  }

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed $value The value that should be validated. Should be an entity or an array of values.
   * @param Constraint $constraint The constraint for the validation
   *
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
   * @param UniqueEntity $constraint
   * @param array $fields
   * @param array $criteria
   * @param ObjectManager $em
   * @param ClassMetadata $class
   * @param $result
   * @param $value
   */
  private function buildViolation(UniqueEntity $constraint, array $fields, array $criteria, ObjectManager $em, ClassMetadata $class, $result, $value) {
    $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : (is_array($value) ? '[' . $fields[0] . ']' : $fields[0]);
    $invalidValue = isset($criteria[$errorPath]) ? $criteria[$errorPath] : ($criteria[$fields[0]] ?? end($criteria));

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
