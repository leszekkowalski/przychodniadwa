<?php
namespace Kalendarz;

use Laminas\ServiceManager\Factory\InvokableFactory;


return [
    'controllers' => [
        'factories' => [
            Controller\KalendarzController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'kalendarz' => [
                'type'    => 'Literal',
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/kalendarz',
                    'defaults' => [
                        'controller'    => Controller\KalendarzController::class,
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
            'Kalendarz' => __DIR__ . '/../view',
        ],
    ],
    
      'filtr_dostepu'=>[
  // 'filtr_dostepu' moze pracować w trybie 'zastrzezony' (ten tryb jest rekomendowany) lub 'pozwalajacy'
   //W trybie 'zastrzezony' wszystkie akcje kontrolera musza być wpisane w kluczu 'filtr_dostepu' - jesli nie jest 
  //wpisany dostep do niego jest niemozliwy bez zalogowania sie.
 //W trybie 'pozwalajacym' jest odwrotnie, jeśli nie jest jawnie wpisany w klucz 'filtr_dostepu'
  //dostep do niego bedzie dla każdego 

        'controllers'=>[
            Controller\KalendarzController::class=>[
            //dostep jest dla kazdego
            ['actions' => ['index'], 'allow' => '*'],
        //dostep tylko dla zalogowanych
            //   ['actions' => ['pokaz'], 'allow' => '+wszyscy.own.pokazsesje']  
            ] ,
                  
        ],
    ],
    
    
];
