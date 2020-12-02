<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Laminas\Mvc\MvcEvent;

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
    }
    
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
}
