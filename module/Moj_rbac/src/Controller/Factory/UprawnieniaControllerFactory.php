<?php

namespace Moj_rbac\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Moj_rbac\Controller\UprawnieniaController;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Moj_rbac\Service\RolaManager;
use Moj_rbac\Service\RbacManager;

class UprawnieniaControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
        $polaczenie=$container->get(RbacPolaczenie::class);
        $roleManager=$container->get(RolaManager::class);
        $rbacManager=$container->get(RbacManager::class);
        
        return new UprawnieniaController($polaczenie,$roleManager,$rbacManager);
    }

}

