<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HalloVerden\ValidatorConstraintsBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Imad ZAIRIG <imadzairig@gmail.com>
 */
class Json extends Constraint {
  const ERROR_INVALID_JSON = '0789c8ad-2d2b-49a4-8356-e2ce63998504';

  protected static $errorNames = [
    self::ERROR_INVALID_JSON => 'INVALID_JSON',
  ];

  public $message = 'json.invalid';
}
