<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraint;

use Symfony\Component\Validator\Constraints\IdenticalTo as SymfonyIdenticalTo;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IdenticalTo extends SymfonyIdenticalTo {
}
