<?php

namespace Moj_rbac\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Moj_rbac\Controller\RolaController;


class RolaControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        $polaczenie=$container->get(\Moj_rbac\Polaczenie\RbacPolaczenie::class);
        
        return new RolaController($polaczenie);
    }

}

