<?php

namespace Application\View\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\View\Helper\AccessHelper;
use Moj_rbac\Service\RbacManager;

class AccessHelperFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        return new AccessHelper($container->get(RbacManager::class));
    }

}