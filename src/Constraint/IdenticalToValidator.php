<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;
use Symfony\Component\Validator\Constraints\IdenticalToValidator as SymfonyIdenticalToValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IdenticalToValidator extends SymfonyIdenticalToValidator {
  private ?PropertyAccessorInterface $propertyAccessor;

  /**
   * IdenticalToValidator constructor.
   *
   * @param PropertyAccessorInterface|null $propertyAccessor
   */
  public function __construct(PropertyAccessorInterface $propertyAccessor = null) {
    parent::__construct($propertyAccessor);
    $this->propertyAccessor = $propertyAccessor;
  }


  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof AbstractComparison) {
      throw new UnexpectedTypeException($constraint, AbstractComparison::class);
    }

    if ($path = $constraint->propertyPath) {
      if (null === ($object = $this->context->getObject()) && null === ($object = $this->context->getRoot())) {
        return;
      }

      try {
        $constraint->value = $this->getPropertyAccessor()->getValue($object, $path);
      } catch (NoSuchPropertyException $e) {
        throw new ConstraintDefinitionException(sprintf('Invalid property path "%s" provided to "%s" constraint: ', $path, get_debug_type($constraint)).$e->getMessage(), 0, $e);
      }

      $constraint->propertyPath = null;
    }

    parent::validate($value, $constraint);
  }

  /**
   * @return PropertyAccessorInterface
   */
  private function getPropertyAccessor(): PropertyAccessorInterface {
    if (null === $this->propertyAccessor) {
      $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    return $this->propertyAccessor;
  }


}
