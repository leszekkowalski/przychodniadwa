<?php

namespace Autoryzacja\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Autoryzacja\Controller\OdzyskaniehaslaController;
use Application\Service\UzytkownikManager;

class OdzyskaniehaslaControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
        
       $uzytkownikManager=$container->get(UzytkownikManager::class);
        return new OdzyskaniehaslaController($uzytkownikManager);
    }

}

