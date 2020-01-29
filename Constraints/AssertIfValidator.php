<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertIfValidator extends ConstraintValidator {

  /**
   * @param mixed $value
   * @param Constraint $constraint
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof AssertIf) {
      throw new UnexpectedTypeException($constraint, AssertIf::class);
    }

    if (!$constraint->test instanceof Constraint) {
      throw new UnexpectedTypeException($constraint->test, Constraint::class);
    }

    $context = $this->context;

    if ($constraint->includeViolations) {
      $c = $context;
      $v = $context->getValidator()->inContext($c)->validate($value, $constraint->test, $context->getGroup());
    } else {
      $v = $context->getValidator()->startContext()->validate($value, $constraint->test, $context->getGroup());
    }

    if (0 === count($v->getViolations())) {
      $context->getValidator()->inContext($context)->validate($value, $constraint->constraints, $context->getGroup())->getViolations();
    } elseif ($constraint->elseConstraints) {
      $context->getValidator()->inContext($context)->validate($value, $constraint->elseConstraints, $context->getGroup())->getViolations();
    }
  }
}
