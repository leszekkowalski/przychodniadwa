<?php

namespace Autoryzacja\Service\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Autoryzacja\Service\LogowanieAuth;
use Laminas\Db\Adapter\Adapter;
use Application\Polaczenie\UzytkownikPolaczenie;

class LogowanieAuthFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): object
    {
        //pobieram instancję 'filtr_dostepu' z klucza konfiguracyjnego module.config.php
        //w celu kontroli dostepu w akcji kontrolaDostepu
        $config=$container->get('Config');
        if(isset($config['filtr_dostepu'])){
            $config=$config['filtr_dostepu'];
        }else{
            $config=[];
        }
        $dbAdapter=$container->get(Adapter::class);
        $dbUzytkownik=$container->get(UzytkownikPolaczenie::class);
        $rbac=$container->get(\Moj_rbac\Service\RbacManager::class);
        return new LogowanieAuth($dbAdapter,$dbUzytkownik,$config,$rbac);
    }

}

