<?php

namespace Application\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Controller\LekarzjsonController;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Polaczenie\UzytkownikPolaczenie;

class LekarzjsonControllerFactory implements FactoryInterface{
    
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
        $lekarzDb=$container->get(LekarzPolaczenie::class);
        $uzytkownikDb=$container->get(UzytkownikPolaczenie::class);
        
        return new LekarzjsonController($lekarzDb,$uzytkownikDb);
        
    }

}
