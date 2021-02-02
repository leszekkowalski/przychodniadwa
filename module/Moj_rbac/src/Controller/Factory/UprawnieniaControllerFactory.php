<?php

namespace Moj_rbac\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Moj_rbac\Controller\UprawnieniaController;
use Moj_rbac\Polaczenie\RbacPolaczenie;

class UprawnieniaControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
        $polaczenie=$container->get(RbacPolaczenie::class);
        
        return new UprawnieniaController($polaczenie);
    }

}

