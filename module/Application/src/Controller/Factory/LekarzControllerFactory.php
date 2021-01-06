<?php

namespace Application\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Application\Controller\LekarzController;
use Application\Form\LekarzDodajForm;
use Application\Polaczenie\LekarzPolaczenie;

class LekarzControllerFactory implements FactoryInterface{
    
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object {
        
        $lekarzDb=$container->get(LekarzPolaczenie::class);
        $formManager = $container->get('FormElementManager');
        $managerZdjecie=$container->get(\Application\Service\ZdjecieManager::class);
        return new LekarzController($lekarzDb,$formManager->get(LekarzDodajForm::class),$managerZdjecie);
    }

}
