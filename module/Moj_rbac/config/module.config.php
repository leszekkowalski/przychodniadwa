<?php
namespace Moj_rbac;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            Controller\RolaController::class => Controller\Factory\RolaControllerFactory::class,
            Controller\UprawnieniaController::class=> Controller\Factory\UprawnieniaControllerFactory::class,
        ],
    ],
    
    
       'service_manager' => [
        'aliases' => [
          
        ],
        'factories' => [
            Polaczenie\RbacPolaczenie::class=> Polaczenie\Factory\RbacPolaczenieFactory::class,
             Service\RbacManager::class=> Service\Factory\RbacManagerFactory::class,
            Service\KontrolaUprawnienIndywidualnychRbac::class=> Service\Factory\KontrolaUprawnienIndywidualnychRbacFactory::class,
            Service\RolaManager::class=> InvokableFactory::class,
        ],
    ],
    
    //klucz do konfiguracji RbacManager
    'rbac_manager' => [
        'assertions' => [Service\KontrolaUprawnienIndywidualnychRbac::class],
    ],
    
    
    'router' => [
        'routes' => [
            'rola' => [
                'type'    => Segment::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/rola[/:action[/:id]]',
                    'constraints' => [
                         'action' => '[a-zA-Z_-]*',
                         'id'     => '[1-9]\d*',  
                    ],
                    'defaults' => [
                        'controller'    => Controller\RolaController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
            ////////////////////////////////
            'uprawnienia' => [
                'type'    => Segment::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/uprawnienia[/:action[/:id]]',
                    'constraints' => [
                         'action' => '[a-zA-Z_-]*',
                         'id'     => '[1-9]\d*',  
                    ],
                    'defaults' => [
                        'controller'    => Controller\UprawnieniaController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
            ///////////////////////////
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Moj_rbac' => __DIR__ . '/../view',
        ],
    ],
    
    
    // klucz 'filtr_dostepu' jest stosowany dla uzytkowników w celu zastrzeżenia lub dostepu (wspólnnie np.
  // z kontrola dostepu przy pomocy seseji) do pewnych lub wszystkich akcji, w tym dla niezautoryzowanych
    'filtr_dostepu'=>[
        
        'controllers'=>[
            Controller\RolaController::class=>[
            //dostep jest dla kazdego
          ['actions' => ['index','dodaj','usun','edytuj','widok','test'], 'allow' => '*'] ,//strona rejestracji nowego uzytkownika
        //dostep tylko dla zalogowanych
          //  []        
            ],
            Controller\UprawnieniaController::class=>[
            //dostep jest dla kazdego
          ['actions' => ['index','dodaj','edytuj','usun','widok'], 'allow' => '*'] ,//strona rejestracji nowego uzytkownika
        //dostep tylko dla zalogowanych
          //  []        
            ],
        
          ],   
        
    ],
    
    
];
