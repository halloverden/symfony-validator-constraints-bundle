<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

final class AssertIfValidator extends ConstraintValidator {
  private TranslatorInterface $translator;

  /**
   * AssertIfValidator constructor.
   */
  public function __construct(?TranslatorInterface $translator = null) {
    if (null === $translator) {
      $translator = new class() implements TranslatorInterface, LocaleAwareInterface {
        use TranslatorTrait;
      };
      $translator->setLocale('en');
    }

    $this->translator = $translator;
  }


  /**
   * @inheritDoc
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof AssertIf) {
      throw new UnexpectedTypeException($constraint, AssertIf::class);
    }

    $context = $this->context;

    if ($constraint->includeViolations) {
      $c = $context;
      $v = $context->getValidator()->inContext($c)->validate($value, $constraint->test, $context->getGroup());
    } else {
      $c = new ExecutionContext($context->getValidator(), $context->getRoot(), $this->translator);
      $c->setNode($value, $context->getObject(), $context->getMetadata(), $context->getPropertyPath());
      $v = $context->getValidator()->inContext($c)->validate($value, $constraint->test, $context->getGroup());
    }

    if (0 === count($v->getViolations())) {
      $context->getValidator()->inContext($context)->validate($value, $constraint->constraints, $context->getGroup())->getViolations();
    } elseif ($constraint->elseConstraints) {
      $context->getValidator()->inContext($context)->validate($value, $constraint->elseConstraints, $context->getGroup())->getViolations();
    }
  }
}
