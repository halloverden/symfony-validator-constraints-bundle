<?php


namespace HalloVerden\ValidatorConstraintsBundle\Constraints;


use Symfony\Component\Validator\Constraints\Composite;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Class CustomCollection
 *
 * @package App\Validator\Constraints
 */
class CustomCollection extends Composite {

  /**
   * @var array
   */
  public $fields = [];

  public function __construct($options = null) {
    // no known options set? $options is the fields array
    if (\is_array($options)
      && !array_intersect(array_keys($options), ['groups', 'fields'])) {
      $options = ['fields' => $options];
    }

    parent::__construct($options);
  }

  protected function initializeNestedConstraints(): void {
    parent::initializeNestedConstraints();

    if (!\is_array($this->fields)) {
      throw new ConstraintDefinitionException(sprintf('The option "fields" is expected to be an array in constraint %s', __CLASS__));
    }

    foreach ($this->fields as $fieldName => $field) {
      $this->fields[$fieldName] = new AllConstraints($field);
    }
  }

  /**
   * Returns the name of the property that contains the nested constraints.
   *
   * @return string The property name
   */
  protected function getCompositeOption(): string {
    return 'fields';
  }

  /**
   * @return array
   */
  public function getRequiredOptions(): array {
    return ['fields'];
  }
}
