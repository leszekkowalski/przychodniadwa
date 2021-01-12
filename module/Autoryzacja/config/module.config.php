<?php
namespace Autoryzacja;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Router\Http\Segment;
use Laminas\Router\Http\Literal;
use Autoryzacja\Service\Factory\AutoryzacjaAdapterFactory;
use Autoryzacja\Service\AutoryzacjaAdapter;
use Autoryzacja\Service\Factory\AutoryzacjaServiceFactory;


return [
    
    'service_manager' => [
        'aliases' => [
          
        ],
        'factories' => [
            AutoryzacjaAdapter::class=> AutoryzacjaAdapterFactory::class,
            \Laminas\Authentication\AuthenticationService::class=> AutoryzacjaServiceFactory::class,
            Service\AutoryzacjaManager::class=> Service\Factory\AutoryzacjaManagerFactory::class,
            Service\LogowanieAuth::class=> Service\Factory\LogowanieAuthFactory::class,
        ],
    ],
      
    'controllers' => [
        'factories' => [
            Controller\AutoryzacjaController::class=> Controller\Factory\AutoryzacjaControllerFactory::class,
            Controller\RejestracjaController::class=> Controller\Factory\RejestracjaControllerFactory::class,
            Controller\OdzyskaniehaslaController::class=> Controller\Factory\OdzyskaniehaslaControllerFactory::class,
        ],
    ],
    
    
    
    'router' => [
        'routes' => [
            'login' => [
                'type'    => Segment::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/login[/:action]',
                    'defaults' => [
                        'controller'    => Controller\AutoryzacjaController::class,
                        'action'        => 'loguj',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
            ////////////////////////////////////
              'rejestruj' => [
                'type'    => Segment::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/rejestruj[/:action]',
                    'defaults' => [
                        'controller'    => Controller\RejestracjaController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
          ////////////////////////////////////////////////////////////////////  
             ////////////////////////////////////
              'odzyskaj-haslo' => [
                'type'    => Literal::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/odzyskaj-haslo',
                    'defaults' => [
                        'controller'    => Controller\OdzyskaniehaslaController::class,
                        'action'        => 'odzyskaj',
                    ],
                ],
            ],
          //////////////////////////////////////////////////////////////////// 
          ////////////////////////////////////
              'ustaw-haslo' => [
                'type'    => Literal::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/ustaw-haslo',
                    'defaults' => [
                        'controller'    => Controller\OdzyskaniehaslaController::class,
                        'action'        => 'ustawHaslo',
                    ],
                ],
            ],
          ////////////////////////////////////////////////////////////////////     
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Autoryzacja' => __DIR__ . '/../view',
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
            Controller\RejestracjaController::class=>[
            //dostep jest dla kazdego
          ['actions' => ['index'], 'allow' => '*'] ,//strona rejestracji nowego uzytkownika
        //dostep tylko dla zalogowanych
            ['actions' => ['sesja','loginprogressuzytkownik'], 'allow' => '@']        
            ],
             Controller\OdzyskaniehaslaController::class=>[
            //dostep jest dla kazdego
          ['actions' => ['odzyskaj','ustawHaslo'], 'allow' => '*'] ,//strona odzyskania hasła
        //dostep tylko dla zalogowanych
           // ['actions' => ['sesja','loginprogressuzytkownik'], 'allow' => '@']        
            ] ,
          ],   
        
    ],
    
    
];
