<?php


namespace HalloVerden\ValidatorConstraintsBundle\DependencyInjection;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\ValidatorConstraintsBundle\Constraints\ArrayClassValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\AssertIfValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\IdenticalToValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\KickboxValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\PhoneNumberValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\PhoneValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\PropertyClassValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\UniqueEntityPropertyValidator;
use HalloVerden\ValidatorConstraintsBundle\Constraints\UniqueEntityValidator;
use HalloVerden\ValidatorConstraintsBundle\Services\ClassInfoService;
use HalloVerden\ValidatorConstraintsBundle\Services\ClassInfoServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class HalloVerdenValidatorConstraintsExtension extends Extension {

  /**
   * @inheritDoc
   */
  public function load(array $configs, ContainerBuilder $container) {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $bundles = $container->getParameter('kernel.bundles');

    if (class_exists(Annotation::class)) {
      if (isset($bundles['FrameworkBundle'])) {
        $reader = new Reference(Reader::class);
      } else {
        $reader = new AnnotationReader();
      }

      $classInfoService = new Definition(ClassInfoServiceInterface::class, [
        '$reader' => $reader
      ]);
      $classInfoService->setClass(ClassInfoService::class);
      $container->setDefinition(ClassInfoServiceInterface::class, $classInfoService);

      $propertyAccessor = null;
      if (class_exists(PropertyAccessor::class)) {
        if (isset($bundles['FrameworkBundle'])) {
          $propertyAccessor = new Reference(PropertyAccessorInterface::class);
        } else {
          $propertyAccessor = new PropertyAccessor();
        }

        $arguments = [
          '$classInfoService' => new Reference(ClassInfoServiceInterface::class),
          '$propertyAccessor' => $propertyAccessor
        ];
        $this->registerValidator($container, ArrayClassValidator::class, $arguments);
        $this->registerValidator($container, PropertyClassValidator::class, $arguments);

        $this->registerValidator($container, IdenticalToValidator::class, [
          '$propertyAccessor' => $propertyAccessor
        ]);
      }

      if (isset($bundles['DoctrineBundle']) && interface_exists(ManagerRegistry::class)) {
        $this->registerValidator($container, UniqueEntityValidator::class, [
          '$registry' => new Reference(ManagerRegistry::class),
          '$propertyAccessor' => $propertyAccessor
        ]);
      }
    }

    $this->registerValidator($container, AssertIfValidator::class, [
      '$translator' => new Reference('translator', ContainerInterface::NULL_ON_INVALID_REFERENCE)
    ]);

    if (isset($config['kickbox']['api_key'])) {
      $this->registerValidator($container, KickboxValidator::class, [
        '$apiKey' => $config['kickbox']['api_key'],
        '$logger' => new Reference(LoggerInterface::class)
      ]);
    }

    if (isset($config['phone']['default_region'])) {
      $this->registerValidator($container, PhoneValidator::class, [
        '$defaultRegion' => $config['phone']['default_region']
      ]);
      $this->registerValidator($container, PhoneNumberValidator::class, [
        '$defaultRegion' => $config['phone']['default_region']
      ]);
    }
  }

  /**
   * @param ContainerBuilder $container
   * @param string           $class
   * @param array            $arguments
   */
  private function registerValidator(ContainerBuilder $container, string $class, array $arguments): void {
    $validator = new Definition($class, $arguments);
    $validator->addTag('validator.constraint_validator', ['alias' => $class]);
    $container->setDefinition($class, $validator);
  }

}
