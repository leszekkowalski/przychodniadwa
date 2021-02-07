<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Application\Form\Element\Telefon;
use Application\Form\LekarzDodajForm;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Model\Lekarz;
use Application\Model\Factory\LekarzFactory;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Form\LekarzFieldset;
use Application\Form\Factory\LekarzFieldsetFactory;
use Application\Service;

return [
    
     'service_manager' => [
        'aliases' => [
          
        ],
        'factories' => [
            Lekarz::class=> LekarzFactory::class,
            LekarzPolaczenie::class=> Polaczenie\Factory\LekarzPolaczenieFactory::class,
            Service\ZdjecieManager::class => InvokableFactory::class,
            Polaczenie\UzytkownikPolaczenie::class=> Polaczenie\Factory\UzytkownikPolaczenieFactory::class,
            Service\UzytkownikManager::class=> Service\Factory\UzytkownikManagerFactory::class,
        ],
    ],
    
     'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\LekarzController::class=> Controller\Factory\LekarzControllerFactory::class,
            Controller\LekarzjsonController::class=> Controller\Factory\LekarzjsonControllerFactory::class,
            Controller\UzytkownikController::class=> Controller\Factory\UzytkownikControllerFactory::class,
        ],
    ],

    'form_elements' => [
        'aliases' => [
           'telefon' => Telefon::class,
        ],
        'factories' => [
            Telefon::class => InvokableFactory::class,
            LekarzFieldset::class=> LekarzFieldsetFactory::class,
            Form\ZmienHasloForm::class=> InvokableFactory::class,
        ],
    ],
    
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
             'pokaz' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/pokaz',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'pokaz',
                    ],
                ],
            ],
            'lekarze' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/lekarze[page/:page]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                         'page' => 1,
                    ],
                ],
            ],
            ////////////////////////////////////////////
             'check_pesel' => [
                'type'    => 'Literal',
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/check_pesel',
                    'defaults' => [
                        'controller'    => Controller\LekarzjsonController::class,
                        'action'        => 'dodajjsonpesel',
                    ],
                ],
            ],
            
             'check_mail' => [
                'type'    => 'Literal',
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/check_mail',
                    'defaults' => [
                        'controller'    => Controller\LekarzjsonController::class,
                        'action'        => 'dodajjsonmail',
                    ],
                ],
            ],
           //////////////////////////////////////////////////////////////
              'check_lekarz_index' => [
                'type'    => 'Literal',
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/check_lekarz_index',
                    'defaults' => [
                        'controller'    => Controller\LekarzjsonController::class,
                        'action'        => 'lekarzindexjson',
                    ],
                ],
            ],
            ////////////////////////////////////////////////
             ////////////////////////////////////////////////
              'check_uzytkownik_index' => [
                'type'    => Literal::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/check_uzytkownik_index',
                    'defaults' => [
                        'controller'    => Controller\LekarzjsonController::class,
                        'action'        => 'uzytkownikindexjson',
                    ],
                ],
            ],
            
            ////////////////////////////////////////////////
            'lekarz' => [
                'type'    => Segment::class,
                'options' => [
                     'route' => '/lekarz[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        
                    ],
                    'defaults' => [
                        'controller'    => Controller\LekarzController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    ///////////////////////////////////////
                     'dodaj' => [
                        'type' => Literal::class,
                        'options' => [
                           'route'    => '/dodaj',
                           'defaults' => [
                                'controller' => Controller\LekarzController::class,
                                'action'     => 'dodaj',
                                        ],
                                     ],
                                ],
                    ///////////////////////////////////////////////////
                    
                    ///////////////////////////////////////////////////
                ],
            ],
            //////////////////////////////////////////////
              ////////////////////////////////////////////////
            'uzytkownik' => [
                'type'    => Segment::class,
                'options' => [
                     'route' => '/uzytkownik[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        
                    ],
                    'defaults' => [
                        'controller'    => Controller\UzytkownikController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                     
                ],
                           
            ],
            
        ],
    ],
   
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'base_path' => '/przychodniadwa/public/'
    ],
    
    
    
    'view_helper_config' => [
    'flashmessenger' => [
        'message_open_format'      => '<div%s><button type="button" class="close"
data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
        'message_close_string'     => '</li></ul></div>',
        'message_separator_string' => '</li><li>',
    ],
],
    
 // klucz 'filtr_dostepu' jest stosowany dla uzytkowników w celu zastrzeżenia lub dostepu (wspólnnie np.
  // z kontrola dostepu przy pomocy seseji) do pewnych lub wszystkich akcji, w tym dla niezautoryzowanych
    'filtr_dostepu'=>[
  // 'filtr_dostepu' moze pracować w trybie 'zastrzezony' (ten tryb jest rekomendowany) lub 'pozwalajacy'
   //W trybie 'zastrzezony' wszystkie akcje kontrolera musza być wpisane w kluczu 'filtr_dostepu' - jesli nie jest 
  //wpisany dostep do niego jest niemozliwy bez zalogowania sie.
 //W trybie 'pozwalajacym' jest odwrotnie, jeśli nie jest jawnie wpisany w klucz 'filtr_dostepu'
  //dostep do niego bedzie dla każdego 
        'options'=>[
           'tryb' =>'zastrzezony'
         //'tryb'=>'pozwalajacy'   
        ],
        'controllers'=>[
            Controller\IndexController::class=>[
            //dostep jest dla kazdego
            ['actions' => ['index'], 'allow' => '*'],
        //dostep tylko dla zalogowanych
            ['actions' => ['pokaz'], 'allow' => '@']        
            ] ,
            Controller\LekarzController::class=>[
                ['actions'=>['index','dodaj','edytuj','pokaz','haslo','przeslijzdjecie','kalendarz','usun'],'allow'=>'@'],
            ],
            Controller\UzytkownikController::class=>[
                  ['actions' => ['pokaz'], 'allow' => '*'],
                  ['actions'=>['index','dodaj','dodajlekarz','zmienHaslo','edytuj','usun'],'allow'=>'@'],  
            ],
            Controller\LekarzjsonController::class=>[
                  ['actions' => ['openjsonmail'], 'allow' => '*'],
                  ['actions'=>['dodajjsonmail','dodajjsonpesel','lekarzindexjson','uzytkownikindexjson'],'allow'=>'@'],  
            ],
        ],
    ],
    
];