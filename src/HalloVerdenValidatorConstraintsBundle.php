<?php


namespace HalloVerden\ValidatorConstraintsBundle;


use HalloVerden\ValidatorConstraintsBundle\Constraint\AssertIfValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraint\IdenticalToValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraint\PhoneNumberValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraint\PhoneValidator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

final class HalloVerdenValidatorConstraintsBundle extends AbstractBundle {

  public function configure(DefinitionConfigurator $definition): void {
    $definition->rootNode()
      ->children()
        ->arrayNode('phone')
          ->children()
            ->scalarNode('default_region')->end()
          ->end()
        ->end()
      ->end()
    ;
  }

  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void {
    $container->services()
      ->set('hallo_verden_validator_constraints.constraint.identical_to', IdenticalToValidator::class)
        ->args([service('property_accessor')->nullOnInvalid()])
        ->tag('validator.constraint_validator')
      ->set('hallo_verden_validator_constraints.constraint.assert_if', AssertIfValidator::class)
        ->args([service('translator')->nullOnInvalid()])
        ->tag('validator.constraint_validator')
      ->set('hallo_verden_validator_constraints.constraint.phone', PhoneValidator::class)
        ->args([$config['phone']['default_region'] ?? 'NO'])
        ->tag('validator.constraint_validator')
      ->set('hallo_verden_validator_constraints.constraint.phone_number', PhoneNumberValidator::class)
        ->args([$config['phone']['default_region'] ?? 'NO'])
        ->tag('validator.constraint_validator')
    ;
  }

}
