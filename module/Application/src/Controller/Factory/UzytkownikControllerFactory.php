<?php

namespace Application\Controller\Factory;


use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Polaczenie\UzytkownikPolaczenie;
use Application\Controller\UzytkownikController;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Form\ZmienHasloForm;

class UzytkownikControllerFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
       
        $formManager = $container->get('FormElementManager');
        
        return new UzytkownikController(
                $container->get(UzytkownikPolaczenie::class),
                $container->get(LekarzPolaczenie::class),
                $formManager->get(ZmienHasloForm::class)
                );
         
    }

}
