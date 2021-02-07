<?php

namespace Moj_rbac\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Moj_rbac\Service\RbacManager;

class RbacManagerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
       $polaczenie=$container->get(\Moj_rbac\Polaczenie\RbacPolaczenie::class);
       
       $polUzyt=$container->get(\Application\Polaczenie\UzytkownikPolaczenie::class);
       
       $cache=$container->get('KonfiguracjaPamieciCache');
       
       $config=$container->get('Config');
       $indywidualneUprawnieniaManager=[];
       
       if(isset($config['rbac_manager']['assertions']))
       {
           foreach ($config['rbac_manager']['assertions'] as $serviceName)
           {
               $indywidualneUprawnieniaManager[$serviceName]=$container->get($serviceName);
           }
       }
       
       
       return new RbacManager($polaczenie,$polUzyt,$cache,$indywidualneUprawnieniaManager);
       
       
    }

}

