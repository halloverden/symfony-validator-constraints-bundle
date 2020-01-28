<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ArrayClassValidator extends BaseConstraintValidator {

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof ArrayClass) {
      throw new UnexpectedTypeException($constraint, ArrayClass::class);
    }

    if (null === $value) {
      return;
    }

    if (!\is_array($value)) {
      throw new UnexpectedValueException($value, 'array');
    }

    $this->initializeClassConstraints($constraint);
    $this->initializePropertyConstraints($constraint);

    $context = $this->context;

    $this->validateClass($constraint->constraints['classConstraints'], $value, $constraint->groupsToGet, $context);
    $this->validateProperties($constraint->constraints['propertyConstraints'], $value, $constraint->groupsToGet, $context);
  }

  /**
   * @param ArrayClass $constraint
   */
  private function initializeClassConstraints(ArrayClass & $constraint) {
    foreach ($constraint->classes as $class) {
      if (is_string($class) && class_exists($class)) {
        if (!isset($constraint->constraints['classConstraints'][$class])) {
          $constraint->constraints['classConstraints'][$class] = [];
        }
        $constraint->constraints['classConstraints'][$class] += $this->getClassConstraintsForClass($class, $constraint->groupsToGet);
      } else {
        throw new \RuntimeException("classes must be an array of classes");
      }
    }
  }

  private function validateClass(array $c, array $value, ?array $groups, ExecutionContextInterface $context) {
    foreach ($c as $class => $constraints) {
      foreach ($constraints as $constraint) {
        if ($constraint instanceof ArrayClassClassConstraintInterface) {
          $constraint->setClassName($class);
        }
      }

      $context->getValidator()->inContext($context)->validate($value, $constraints, $groups);
    }
  }

  /**
   * @param ArrayClass $constraint
   */
  private function initializePropertyConstraints(ArrayClass & $constraint) {
    foreach ($constraint->classes as $key => $class) {
      if (is_string($key) && class_exists($class)) {
        $constraint->constraints['propertyConstraints'][$key] += $this->getPropertyConstraintsForClass($class, $constraint->groupsToGet);
      } elseif (is_int($key) && class_exists($class)) {
        $constraint->constraints['propertyConstraints'] += $this->getPropertyConstraintsForClass($class, $constraint->groupsToGet);
      } else {
        throw new \RuntimeException("classes must be an array of classes");
      }
    }
  }

  /**
   * @param array                     $constraints
   * @param array                     $values
   * @param array|null                $groups
   * @param ExecutionContextInterface $context
   * @param string                    $prevPropertyPath
   */
  private function validateProperties(array $constraints, array $values, ?array $groups, ExecutionContextInterface $context, string $prevPropertyPath = '') {
    foreach ($constraints as $property => $propertyConstraint) {
      $value = isset($values[$property]) ? $values[$property] : null;
      $propertyPath = $prevPropertyPath . '['.$property.']';

      $constraintsToCheck = [];
      $nestedConstraints = [];
      if (is_array($propertyConstraint)) {

        foreach ($propertyConstraint as $key => $element) {
          if ($element instanceof Constraint) {
            $constraintsToCheck[$key] = $element;
          } else {
            $nestedConstraints[$key] = $element;
          }
        }

        if (count($nestedConstraints) > 0) {
          $this->validateProperties($nestedConstraints, $value ?: [], $groups, $context, $propertyPath);
        }
      } else {
        $constraintsToCheck = [$propertyConstraint];
      }

      $context->getValidator()->inContext($context)->atPath($propertyPath)->validate($value, $constraintsToCheck, $groups);
    }
  }
}
