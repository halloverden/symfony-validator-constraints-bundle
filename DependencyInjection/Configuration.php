<?php


namespace HalloVerden\ValidatorConstraintsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  /**
   * @inheritDoc
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder('hallo_verden_validator_constraints');

    $treeBuilder->getRootNode()
      ->children()
        ->arrayNode('kickbox')
          ->children()
            ->scalarNode('api_key')->end()
          ->end()
        ->end()
        ->arrayNode('phone')
          ->children()
            ->scalarNode('default_region')->end()
          ->end()
        ->end()
      ->end()
    ;

    return $treeBuilder;
  }
}
