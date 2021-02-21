<?php

namespace Moj_rbac\Polaczenie\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Db\Adapter\AdapterInterface;


class RbacPolaczenieFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
         $cache = $container->get('KonfiguracjaPamieciCache'); //konfiguracja w pliku global.php

        return new RbacPolaczenie(
                $container->get(AdapterInterface::class),
                new ReflectionHydrator(),
                $cache,
                );  
    }

}

