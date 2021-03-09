<?php

namespace Kalendarz\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Kalendarz\Controller\KalendarzController;
use Application\Polaczenie\LekarzPolaczenie;
use Kalendarz\Polaczenie\WydarzeniePolaczenie;

class KalendarzControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        $polaczenieLekarz=$container->get(LekarzPolaczenie::class);
        $polaczenieWydarzenie=$container->get(WydarzeniePolaczenie::class);
        
        
        return new KalendarzController($polaczenieLekarz, $polaczenieWydarzenie);
    }

}

