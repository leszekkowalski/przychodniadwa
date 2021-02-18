<?php

namespace Application\Controller\Plugin\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Controller\Plugin\AccessPlugin;
use Moj_rbac\Service\RbacManager;


class AccessPluginFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
       return new AccessPlugin($container->get(RbacManager::class)); 
    }

}

