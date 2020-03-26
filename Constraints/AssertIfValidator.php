<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

class AssertIfValidator extends ConstraintValidator {

  /**
   * @var TranslatorInterface
   */
  private $translator;

  /**
   * AssertIfValidator constructor.
   *
   * @param TranslatorInterface $translator
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
