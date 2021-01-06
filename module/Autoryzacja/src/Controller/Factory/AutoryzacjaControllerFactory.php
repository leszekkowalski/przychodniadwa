<?php

namespace Autoryzacja\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Autoryzacja\Controller\AutoryzacjaController;
use Autoryzacja\Service\AutoryzacjaManager;
use Laminas\Authentication\AuthenticationService;

class AutoryzacjaControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        $logowanieAuth=$container->get(\Autoryzacja\Service\LogowanieAuth::class);
        
        return new AutoryzacjaController($logowanieAuth);
    }

}
