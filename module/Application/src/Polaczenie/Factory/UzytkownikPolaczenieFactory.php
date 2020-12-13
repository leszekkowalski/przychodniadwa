<?php

namespace Application\Polaczenie\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Polaczenie\UzytkownikPolaczenie;
use Application\Model\Uzytkownik;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Db\Adapter\AdapterInterface;


class UzytkownikPolaczenieFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
        return new UzytkownikPolaczenie(
                $container->get(AdapterInterface::class),
                new ReflectionHydrator(),
                new Uzytkownik(),
                );  
    }

}
