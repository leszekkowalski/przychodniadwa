<?php

namespace Application\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Model\Lekarz;
use Laminas\Db\Adapter\AdapterInterface;


class LekarzFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, $requestedName, mixed $options = null): object {
        $adapter=$container->get(AdapterInterface::class);
        return new Lekarz();
        
    }

}

