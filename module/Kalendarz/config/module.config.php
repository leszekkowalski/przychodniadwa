<?php
namespace Kalendarz;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;


return [
    'controllers' => [
        'factories' => [
            Controller\KalendarzController::class => Controller\Factory\KalendarzControllerFactory::class,
        ],
    ],
    
     'service_manager' => [
        'aliases' => [
          
        ],
        'factories' => [
            Polaczenie\WydarzeniePolaczenie::class=> Polaczenie\Factory\WydarzeniePolaczenieFactory::class,
        ],
    ],
    
    'router' => [
        'routes' => [
            'kalendarz' => [
                'type'    => Segment::class,
                'options' => [
                     'route' => '/kalendarz[/:action/:id[/:data][/:idlekarz]]',//parametr id jest obowiazkowy - bez nawiasów
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        'data'=>'[0-9]{4}-{1}[0-9]{2}-{1}[0-9]{2}',
                        'idlekarz'=>'[0-9]+',
                        
                    ],
                    'defaults' => [
                       'controller'    => Controller\KalendarzController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                  ///////////////////////////
                    'pokaz' => [     //$this->url('kalendarz/pokaz', ['data' => '2021-12-12','id'=>22]); - wynik-  "/kalendarz/2021-12-12/22"
                        'type' => 'segment',
                        'options' => [
                            'route' => '/[:data][/:id]',
                            'constraints' => [
                              'data'=>'[0-9]{4}-{1}[0-9]{2}-{1}[0-9]{2}',
                                 'id'     => '[0-9]+', 
                            ],
                            'defaults' => [
                                'action' => 'pokaz',
                                ],
                            ],
                         ],
                 /////////////////////////////////////   
                   'search_lekarza4xml' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/search_lekarza4xml',
                    'defaults' => [
                        'controller'    => Controller\KalendarzController::class,
                        'action'        => 'searchlekarza4xml',
                    ],
                ],
            ],  
                    
                //////////////////////////////////////    
                ],
              
            ],
              ////////////////////////////////////////////////
              'search_lekarzajson' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/search_lekarzajson',
                    'defaults' => [
                        'controller'    => \Application\Controller\LekarzjsonController::class,
                        'action'        => 'searchlekarzajson',
                    ],
                ],
            ],
            
            ////////////////////////////////////////////////
            'search_lekarza2json' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/search_lekarza2json',
                    'defaults' => [
                        'controller'    => \Application\Controller\LekarzjsonController::class,
                        'action'        => 'searchlekarza2json',
                    ],
                ],
            ],    

            ///////////////////////////////////////////////////
                ////////////////////////////////////////////////
            'search_lekarza3json' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/search_lekarza3json',
                    'defaults' => [
                        'controller'    => \Application\Controller\LekarzjsonController::class,
                        'action'        => 'searchlekarza3json',
                    ],
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
            ['actions' => ['index','pokaz','pokazWydarzenie','edytuj','wpisz','autocomplete','searchlekarza4xml'], 'allow' => '*'],
        //dostep tylko dla zalogowanych
            //   ['actions' => ['pokaz'], 'allow' => '+wszyscy.own.pokazsesje']  
            ] ,
                  
        ],
    ],
    
    
];
