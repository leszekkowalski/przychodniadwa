<?php

namespace Application\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Form\LekarzFieldset;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Application\Polaczenie\PolaczenieAdapter;

class LekarzFieldsetFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
        $adapter=$container->get(AdapterInterface::class);
        return new LekarzFieldset();
        
    }
    
    public function createService(ServiceLocatorInterface $formManager)
    {
        return $this(
            $formManager->getServiceLocator() ?: $formManager,
            LekarzFieldset::class
        );
    }

}

