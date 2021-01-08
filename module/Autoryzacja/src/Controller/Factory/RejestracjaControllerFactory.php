<?php

namespace Autoryzacja\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Autoryzacja\Controller\RejestracjaController;
use Application\Polaczenie\UzytkownikPolaczenie;

class RejestracjaControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object 
    {
        $uzytkownikDb=$container->get(UzytkownikPolaczenie::class);
        return new RejestracjaController($uzytkownikDb);
    }

}

