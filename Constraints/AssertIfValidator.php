<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AssertIfValidator extends ConstraintValidator {
  /**
   * @var LoggerInterface
   */
  private $logger;

  /**
   * @var TranslatorInterface
   */
  private $translator;

  /**
   * @var ValidatorInterface
   */
  private $validator;

  /**
   * AssertIfValidator constructor.
   * @param LoggerInterface $logger
   * @param TranslatorInterface $translator
   * @param ValidatorInterface $validator
   */
  public function __construct(LoggerInterface $logger, TranslatorInterface $translator, ValidatorInterface $validator) {
    $this->logger = $logger;
    $this->translator = $translator;
    $this->validator = $validator;
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
      $c = new ExecutionContext($this->validator, $context->getRoot(), $this->translator);
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
