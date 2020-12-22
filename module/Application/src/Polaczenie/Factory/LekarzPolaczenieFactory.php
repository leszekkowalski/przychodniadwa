<?php

namespace Application\Polaczenie\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Polaczenie\LekarzPolaczenie;
use Laminas\Hydrator\ReflectionHydrator;
use Application\Model\Lekarz;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Cache\Storage\StorageInterface;

class LekarzPolaczenieFactory implements FactoryInterface {
    
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
    $cache = $container->get('KonfiguracjaPamieciCache'); //konfiguracja w pliku global.php

     
        return new LekarzPolaczenie(
                $container->get(AdapterInterface::class),
                new ReflectionHydrator(),
               new Lekarz(),
                $cache,
                );
    }

}

