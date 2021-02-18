<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Session\Container;
use Laminas\Session\Validator;
use Laminas\Session\Config\SessionConfig;
use Laminas\Mvc\Controller\AbstractActionController;
use Autoryzacja\Service\LogowanieAuth;
use Autoryzacja\Controller\AutoryzacjaController;


class Module
{
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function onBootstrap(MvcEvent $e){
        //rejestruje metodę  dla eventu render
        $app=$e->getApplication();
        $app->getEventManager()->attach('render', [$this, 'ustawLayout'], 100);
        //pobieram event manager
        $eventManager        = $e->getApplication()->getEventManager();
        $sharedEventManager=$eventManager->getSharedManager();
        // rejestruje metodę słuchacza i podpinam do AbstractActionController
        // w celu działania dla całej aplikacji, na wszystkich stronach
        $sharedEventManager->attach(
                AbstractActionController::class, 
                MvcEvent::EVENT_DISPATCH,
                [$this,'onDispatch2' ],
                100
                );
        
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->SesjaNaBootstrap($e);
        
        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
        
        
    }
    /**
     * 
     * @param MvcEvent $e
     * @return type
     * funkcja ustawia odpowiednią strategię wyswietlania.
     * Jeśli w nazwie kontrollera znajduje się napis 'json' - ustawia strategię wyswietlania na ViewJsonStrategy
     * w pozostałych przpadkach bedzie zwykła strategia wyswietlania
     */
    public function ustawLayout(MvcEvent $e){
                  
         $przechwyt=$e->getRouteMatch(); 
      $controller= strtolower($przechwyt->getParam('controller'));        
       //jeśli w kontrolerze znajduje sie string 'json' ustawiam strategie na JSONStrategy
      
       if(strpos('json', $controller)!==false){
           $strategia='ViewJsonStrategy';
       }else{
           return;
       }
       //pobieram obiekt ServiceManagera i pobieramy z niego odpowiedni odpowiedni obiekt strategi widoku
       $app=$e->getTarget();
       $locator=$app->getServiceManager();
       $view=$locator->get(\Laminas\View\View::class);
       $jsonStrategy=$locator->get($strategia);
       
       //ustawiam nową strategie dla Laminas/View
       $view->getEventManager()->attach($jsonStrategy,100);
    }
    /**
     * 
     * @param MvcEvent $e
     * @return type
     * metoda inicjalizuje i ustawia sesję dla całej aplikacji
     */
    public function SesjaNaBootstrap(MvcEvent $e){
        
         $session = $e->getApplication()
            ->getServiceManager()
            ->get(SessionManager::class);
        $session->start();

        $container = new Container('initialized');

        if (isset($container->init)) {
            return;
        }

        $serviceManager = $e->getApplication()->getServiceManager();
        $request        = $serviceManager->get('Request');

        $session->regenerateId(true);
        $container->init          = 1;
        $container->remoteAddr    = $request->getServer()->get('REMOTE_ADDR');
        $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');

        $config = $serviceManager->get('Config');
        if (! isset($config['session'])) {
            return;
        }

        $sessionConfig = $config['session'];

        if (! isset($sessionConfig['validators'])) {
            return;
        }

        $chain   = $session->getValidatorChain();
        
        foreach ($sessionConfig['validators'] as $validator) {
            switch ($validator) {
                case Validator\HttpUserAgent::class:
                    $validator = new $validator($container->httpUserAgent);
                    break;
                case Validator\RemoteAddr::class:
                    $validator  = new $validator($container->remoteAddr);
                    break;
                default:
                    $validator = new $validator();
                    break;
            }

            $chain->attach('session.validate', array($validator, 'isValid'));
        }
        
    }
    /**
     * 
     * @return type
     * Metoda ustawia fabryke dla sesji oraz konfiguruje jego elementy
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                SessionManager::class => function ($container) {
                    $config = $container->get('config');
                    if (! isset($config['session'])) {
                        $sessionManager = new SessionManager();
                        Container::setDefaultManager($sessionManager);
                        return $sessionManager;
                    }

                    $session = $config['session'];

                    $sessionConfig = null;
                    if (isset($session['config'])) {
                        $class = isset($session['config']['class'])
                            ?  $session['config']['class']
                            : SessionConfig::class;

                        $options = isset($session['config']['options'])
                            ?  $session['config']['options']
                            : [];

                        $sessionConfig = new $class();
                        $sessionConfig->setOptions($options);
                    }

                    $sessionStorage = null;
                    if (isset($session['storage'])) {
                        $class = $session['storage'];
                        $sessionStorage = new $class();
                    }

                    $sessionSaveHandler = null;
                    if (isset($session['save_handler'])) {
                        // class should be fetched from service manager
                        // since it will require constructor arguments
                        $sessionSaveHandler = $container->get($session['save_handler']);
                    }

                    $sessionManager = new SessionManager(
                        $sessionConfig,
                        $sessionStorage,
                        $sessionSaveHandler
                    );

                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
            ],
        ];
    }
    // Metoda detektora zdarzeń dla zdarzenia „onDispatch”. Słuchamy wysyłki
     //zdarzenie, aby wywołać filtr dostępu. Filtr dostępu pozwala określić, czy
    // bieżący odwiedzający może oglądać stronę lub nie. Jeśli on / ona
    // nie jest uwierzytelniony i nie ma dostępu do strony, przekierowujemy użytkownika
    // do strony logowania.
    /**----------ponizej metody dla autoryzacji tylko przy pomocy sesji
    public function onDispatch2(MvcEvent $event)
    {
     $controller=$event->getTarget();
     
        //pobieram kontroller i akcie z HTTP, który został wysłany
     $controllerName=$event->getRouteMatch()->getParam('controller', null);
      $actionName=$event->getRouteMatch()->getParam('action',null);
        // zmieniam  dash-style action na  camel-case.
       $actionName= str_replace('-', '', lcfirst(ucwords($actionName,'-'))) ;
       
        // Pobieram instancję  LogowanieAuth service.
       $logowanieAuth=$event->getApplication()->getServiceManager()->get(LogowanieAuth::class);
       
       // realizujemy sprawdzenie kontroli dostepu dla wszystkich Controllerów, z wyjatkiem 
       // AutoryzacjaController 
       // (aby uniknąć nieskończonego przekierowania)
       
       if($controllerName!=AutoryzacjaController::class 
               && !$logowanieAuth->kontrolaDostepu($controllerName, $actionName)
         )
       {
        //Zapamietujemy adres URL z którego uzytkownik próbuje uzyskac dostep.
        //Potem przekierowyjemy go do niej po poprawnym logowaniu
        $uri = $event->getApplication()->getRequest()->getUri();  
        
        //Robimy adres URL realtywny(usuwamy user,schema,host and port)
        //unikamy przekierowania do innej domeny przez złozliwego uzytkownika
        //powstaje adres wzgledny
            $uri->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);
          $redirectUrl = $uri->toString();
          
        // przekierowujemy uzytkownika do strony logowania.
            return $controller->redirect()->toRoute('login', [], 
                    ['query'=>['redirectUrl'=>$redirectUrl]]);  
          
       }
        
         
    }
     * 
     * @param MvcEvent $event
     */
    //ponizej metoda autoryzacji przy pomocy uprawnien i zmienionej klasy LogowanieAuth
    public function onDispatch2(MvcEvent $event)
    {
        //pobieram kontroler i akcje z parametrow HTTP
       $controler=$event->getTarget(); 
       $nazwaControler=$event->getRouteMatch()->getParam('controller', null);
       $nazwaAkcja=$event->getRouteMatch()->getParam('action', null);
       
       $nazwaAkcja= str_replace('-','',lcfirst(ucwords($nazwaAkcja, '-')));
        
       // Pobieram service dla  LogowanieAuth
       $logowanieAuthService=$event->getApplication()->getServiceManager()->get(LogowanieAuth::class);
       
       // Wykonuje filtrowanie dla każdego kontrolera z wyjatkiem AutoryzacjaController
        // unikam nieskonczonej petli przekierowan
       
       if($nazwaControler!= AutoryzacjaController::class)
       {
           
          $wynikAkcji_KontrolaDostepu=$logowanieAuthService->kontrolaDostepu($nazwaControler,$nazwaAkcja);
      
          if($wynikAkcji_KontrolaDostepu==LogowanieAuth::DOSTEP_WYMAGANA_AUTORYZACJA)
          {
              //Zapamietuje adres URL strony na którą chce wejsc uzytkownik w celu
              //przekierowania go tam po pozytywnym zalogowaniu
              $uri=$event->getApplication()->getRequest()->getUri();
              

             // Wykonuje relatywny URL (usuwam dane) w celu unikniecia przekierowania
              //do innej domeny przez np. złośliwego uzytkownika
              $uri->setScheme(null)
                  ->setHost(null)
                  ->setPort(null)
                      ->setUserInfo(null);
              
              $przekierowanieUri=$uri->toString();
              
              //Przekierowuje na strone logowania
              return $controler->redirect()->toRoute('login',[],['query'=>['redirectUrl'=>$przekierowanieUri]]);
                
              
          }else{
              if($wynikAkcji_KontrolaDostepu==LogowanieAuth::DOSTEP_ZABRONIONY)
              {
                  return $controler->redirect()->toRoute('brak-autoryzacji');
              }
          }
        
       }
    }
    
}
