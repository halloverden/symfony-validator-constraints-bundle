<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraint;

class Kickbox extends Constraint {

  const RESULT_DELIVERABLE = 'deliverable';
  const RESULT_UNDELIVERABLE = 'undeliverable';
  const RESULT_RISKY = 'risky';
  const RESULT_UNKNOWN = 'unknown';

  const REASON_INVALID_EMAIL = '5cc2d06f-2a78-489c-a938-36cdefb0fb0d';
  const REASON_INVALID_DOMAIN = 'bc651a6c-7867-4de7-9989-85b716c62e5e';
  const REASON_REJECTED_EMAIL = '7ad384e1-78ee-43fc-b0ec-1186531c2054';
  const REASON_ACCEPTED_EMAIL = 'a5ad0b81-1202-46da-ad1c-70a8031ca3f5';
  const REASON_LOW_QUALITY = '45e3e7ee-f204-44f2-a935-580c0ba3f27c';
  const REASON_LOW_DELIVERABILITY = 'fa571f63-53eb-4ed1-9ab1-c12c11e1062e';
  const REASON_NO_CONNECT = '455b0b4b-5271-4106-9617-23b88090d62a';
  const REASON_TIMEOUT = '73214c85-4705-4a67-afdf-1ea1b16b90d8';
  const REASON_INVALID_SMTP = 'd10a686d-bf33-4cf1-8751-ee65664d6498';
  const REASON_UNAVAILABLE_SMTP = '020ea784-10ed-4ca0-9fcc-c6419e6d7192';
  const REASON_UNEXPECTED_ERROR = '516712d0-d0c3-423f-b2f6-8ed8207b5495';

  const ERROR_KICKBOX_API = '5e8c9ea5-8607-44e0-97ad-421d4e31b4fe';

  const KICKBOX_REASON_TO_CODE = [
    'invalid_email' => self::REASON_INVALID_EMAIL,
    'invalid_domain' => self::REASON_INVALID_DOMAIN,
    'rejected_email' => self::REASON_REJECTED_EMAIL,
    'accepted_email' => self::REASON_ACCEPTED_EMAIL,
    'low_quality' => self::REASON_LOW_QUALITY,
    'low_deliverability' => self::REASON_LOW_DELIVERABILITY,
    'no_connect' => self::REASON_NO_CONNECT,
    'timeout' => self::REASON_TIMEOUT,
    'invalid_smtp' => self::REASON_INVALID_SMTP,
    'unavailable_smtp' => self::REASON_UNAVAILABLE_SMTP,
    'unexpected_error' => self::REASON_UNEXPECTED_ERROR,
  ];

  protected static $errorNames = [
    self::REASON_INVALID_EMAIL =>  'REASON_INVALID_EMAIL',
    self::REASON_INVALID_DOMAIN =>  'REASON_INVALID_DOMAIN',
    self::REASON_REJECTED_EMAIL =>  'REASON_REJECTED_EMAIL',
    self::REASON_ACCEPTED_EMAIL =>  'REASON_ACCEPTED_EMAIL',
    self::REASON_LOW_QUALITY =>  'REASON_LOW_QUALITY',
    self::REASON_LOW_DELIVERABILITY =>  'REASON_LOW_DELIVERABILITY',
    self::REASON_NO_CONNECT =>  'REASON_NO_CONNECT',
    self::REASON_TIMEOUT =>  'REASON_TIMEOUT',
    self::REASON_INVALID_SMTP =>  'REASON_INVALID_SMTP',
    self::REASON_UNAVAILABLE_SMTP =>  'REASON_UNAVAILABLE_SMTP',
    self::REASON_UNEXPECTED_ERROR =>  'REASON_UNEXPECTED_ERROR',
    self::ERROR_KICKBOX_API => 'KICKBOX_API'
  ];

  public $message = 'kickbox.email.%s';

  /**
   * Results that creates a violation
   *
   * @var array
   */
  public $invalidResults = [
    self::RESULT_UNDELIVERABLE,
  ];

  public $violationOnApiError = false;

}
