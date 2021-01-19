<?php
namespace Moj_rbac;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            Controller\RolaController::class => Controller\Factory\RolaControllerFactory::class,
        ],
    ],
    
    
       'service_manager' => [
        'aliases' => [
          
        ],
        'factories' => [
            Polaczenie\RbacPolaczenie::class=> Polaczenie\Factory\RbacPolaczenieFactory::class,
        ],
    ],
    
    
    
    
    'router' => [
        'routes' => [
            'rbac' => [
                'type'    => Segment::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/rbac[/:action[/:id]]',
                    'constraints' => [
                         'action' => '[a-zA-Z_-]*',
                         'id'     => '[0-9]+',  
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
          ['actions' => ['index'], 'allow' => '*'] ,//strona rejestracji nowego uzytkownika
        //dostep tylko dla zalogowanych
          //  []        
            ],
        
          ],   
        
    ],
    
    
];
