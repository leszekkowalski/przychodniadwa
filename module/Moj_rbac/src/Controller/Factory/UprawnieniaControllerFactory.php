<?php

namespace Moj_rbac\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Moj_rbac\Controller\UprawnieniaController;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Moj_rbac\Service\RolaManager;

class UprawnieniaControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
        $polaczenie=$container->get(RbacPolaczenie::class);
        $roleManager=$container->get(RolaManager::class);
        
        return new UprawnieniaController($polaczenie,$roleManager);
    }

}

