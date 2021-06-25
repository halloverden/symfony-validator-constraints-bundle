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
 * Class UniqueEntityPropertyValidator
 * @package HalloVerden\ValidatorConstraintsBundle\Constraints
 */
class UniqueEntityPropertyValidator extends BaseUniqueEntityConstraintValidator {

  /**
   * UniqueEntityPropertyValidator constructor
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
   * @param mixed $value
   * @param Constraint $constraint
   *
   * @throws \Exception
   */
  public function validate($value, Constraint $constraint): void {
    if (!$constraint instanceof UniqueEntityProperty) {
      throw new UnexpectedTypeException($constraint, UniqueEntityProperty::class);
    }

    $propertyName = $this->context->getPropertyName();
    $className = $this->context->getClassName();
    // By default, the property subject of the UniqueEntityProperty Constraint is one of the fields we need to check
    $constraint->fields[] = $propertyName;

    $em = $this->registry->getManagerForClass($className);
    $classMetadata = $em->getClassMetadata($className);

    /* @var $class ClassMetadata */
    if (!$criteria = $this->getCriteria($constraint->fields, $classMetadata, $this->context->getObject(), $constraint, $em)) {
      return;
    }
    $repository = $this->getRepository($em, $classMetadata);

    if (!$fetchedEntity = $this->fetchEntity($repository, $constraint, $criteria, $value)) {
      return;
    }

    $this->buildViolation($constraint, $propertyName, $value, $fetchedEntity);
  }

  /**
   * @param ObjectManager $em
   * @param ClassMetadata $classMetadata
   *
   * @return ObjectRepository
   * @throws \ReflectionException
   */
  private function getRepository(ObjectManager $em, ClassMetadata $classMetadata): ObjectRepository {
    if (null !== $classMetadata->getName()) {
      /* Retrieve repository from given entity name.
       * We ensure the retrieved repository can handle the entity
       * by checking the class is the same, or subclass of the supported entity.
       */
      $repository = $em->getRepository($classMetadata->getName());
      $supportedClass = $repository->getClassName();

      $entity = $classMetadata->getReflectionClass();

      if (!$entity->newInstanceWithoutConstructor() instanceof $supportedClass) {
        throw new ConstraintDefinitionException(sprintf('The "%s" entity repository does not support the "%s" entity. The entity should be an instance of or extend "%s".', $classMetadata->getName(), $classMetadata->getName(), $supportedClass));
      }
    } else {
      $repository = $em->getRepository($classMetadata->getName());
    }

    return $repository;
  }

  /**
   * @param UniqueEntityProperty $constraint
   * @param string $propertyName
   * @param $value
   * @param $fetchedEntity
   */
  private function buildViolation(UniqueEntityProperty $constraint, string $propertyName, $value, $fetchedEntity) {
    $this->context->buildViolation($constraint->message)
      ->setParameter('{{ value }}', $propertyName)
      ->setInvalidValue($value)
      ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
      ->setCause($fetchedEntity)
      ->addViolation();
  }

}