<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use Kickbox\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class KickboxValidator extends ConstraintValidator {

  /**
   * @var \Kickbox\Api\Kickbox
   */
  private $kickbox;

  /**
   * @var LoggerInterface
   */
  private $logger;

  /**
   * KickboxValidator constructor.
   *
   * @param string          $apiKey
   * @param LoggerInterface $logger
   */
  public function __construct(string $apiKey, LoggerInterface $logger) {
    if (!class_exists(Client::class)) {
      throw new \LogicException(sprintf('The "%s" class requires the "kickbox" component. Try running "composer require kickbox/kickbox".', self::class));
    }

    $this->kickbox = (new Client($apiKey))->kickbox();
    $this->logger = $logger;
  }

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($value, Constraint $constraint) {
    if (!$constraint instanceof Kickbox) {
      throw new UnexpectedTypeException($constraint, Kickbox::class);
    }

    if (null === $value || '' === $value) {
      return;
    }

    if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
      throw new UnexpectedValueException($value, 'string');
    }

    $value = (string) $value;

    try {
      $response = $this->kickbox->verify($value);
    } catch (\Exception $exception) {
      $this->logger->error(Kickbox::ERROR_KICKBOX_API, ['message' => $exception->getMessage(), 'code' => $exception->getCode()]);

      if ($constraint->violationOnApiError) {
        $this->context->buildViolation('Api Error')->setCode(Kickbox::ERROR_KICKBOX_API)->addViolation();
      }

      return;
    }

    if (in_array($response->body['result'], $constraint->invalidResults)) {
      $reason = $response->body['reason'];
      $this->context->buildViolation(sprintf($constraint->message, $reason))
        ->setParameter('{{ value }}', $this->formatValue($value))
        ->setCode(Kickbox::KICKBOX_REASON_TO_CODE[$reason])
        ->addViolation();
    }
  }
}
