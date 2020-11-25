<?php

namespace Application\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Controller\IndexController;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Polaczenie\LekarzPolaczenie;

class IndexControllerFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
        $lekarzDb=$container->get(LekarzPolaczenie::class);
        
        return new IndexController($lekarzDb);
        
    }

}

