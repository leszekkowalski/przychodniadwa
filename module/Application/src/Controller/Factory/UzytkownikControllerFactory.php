<?php

namespace Application\Controller\Factory;


use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Polaczenie\UzytkownikPolaczenie;
use Application\Controller\UzytkownikController;

class UzytkownikControllerFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
       
        return new UzytkownikController($container->get(UzytkownikPolaczenie::class));
        
        
    }

}
