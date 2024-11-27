<?php

// src/Kernel.php

namespace App;

use DateTime;
use Psr\Log\LoggerAwareInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function process(ContainerBuilder $container): void
    {
        $definitions = $container->getDefinitions();
        foreach ($definitions as $definition) {
            if (!$this->isAware($definition, LoggerAwareInterface::class)) {
                continue;
            }
            $definition->addMethodCall(
                 'setLogger',
                [$container->getDefinition('monolog.logger')]
             );
        }
    }

 private function isAware(Definition $definition, string $awarenessClass): bool
 {
     $serviceClass = $definition->getClass();
     if ($serviceClass === null) {
         return false;
     }
     $implementedClasses = @class_implements($serviceClass, false);
     if (empty($implementedClasses)) {
         return false;
     }
     if (\array_key_exists($awarenessClass, $implementedClasses)) {
         return true;
     }

     return false;
 }

}
