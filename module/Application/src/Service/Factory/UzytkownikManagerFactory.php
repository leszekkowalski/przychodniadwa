<?php

namespace Application\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Service\UzytkownikManager;
use Application\Polaczenie\UzytkownikPolaczenie;

class UzytkownikManagerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        
        $db=$container->get(UzytkownikPolaczenie::class);
        $viewRenderer = $container->get('ViewRenderer');
        $config = $container->get('Config');
        
        return new UzytkownikManager($db,$viewRenderer,$config);
    }

}

