<?php

namespace Application\Polaczenie\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Polaczenie\LekarzPolaczenie;
use Laminas\Hydrator\ReflectionHydrator;
use Application\Model\Lekarz;
use Laminas\Db\Adapter\AdapterInterface;

class LekarzPolaczenieFactory implements FactoryInterface {
    
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
        return new LekarzPolaczenie(
                $container->get(AdapterInterface::class),
                new ReflectionHydrator(),
               new Lekarz(),
                );
    }

}

