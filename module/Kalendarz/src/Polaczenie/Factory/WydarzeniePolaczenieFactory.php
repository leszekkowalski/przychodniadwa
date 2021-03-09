<?php

namespace Kalendarz\Polaczenie\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Kalendarz\Polaczenie\WydarzeniePolaczenie;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Kalendarz\Entity\Wydarzenie;

class WydarzeniePolaczenieFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        $adapter=$container->get(AdapterInterface::class);
        
        return new WydarzeniePolaczenie($adapter, new ReflectionHydrator(), new Wydarzenie());
    }

}
