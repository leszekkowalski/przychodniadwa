<?php

namespace Moj_rbac\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Moj_rbac\Service\KontrolaUprawnienIndywidualnychRbac;
use Application\Polaczenie\UzytkownikPolaczenie;

class KontrolaUprawnienIndywidualnychRbacFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        $polaczenie=$container->get(UzytkownikPolaczenie::class);
        
        return new KontrolaUprawnienIndywidualnychRbac($polaczenie);
    }

}

