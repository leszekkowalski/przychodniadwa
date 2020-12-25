<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Session;

class AbstractController extends AbstractActionController  {
    
    protected $baseUrl;
    
    public function onDispatch(MvcEvent $e) {
        
      //przekazuje baseUrl tylko do kontrolera
      $this->baseUrl=$this->getRequest()->getBasePath();
      
        parent::onDispatch($e);
    }
    /*
     * funkcja jest wykonywana przed akcjami kontrolera
     */
    public function setEventManager(EventManagerInterface $events) {
        
      parent::setEventManager($events);
        
        
      $events->attach('dispatch', function ($e) {
          
      $controllerClass = $e->getRouteMatch()->getParam('controller', 'index');
       // przekazuje do szablonu wywoływany kontroler    
      $e->getViewModel()->setVariable('controller', $controllerClass);
       //przekazuje baseUrl do szablonu layout.phtml
    $e->getViewModel()->setVariable('baseUrl', $this->getRequest()->getBasePath());      
      
    $userSession = new Session\Container('uzytkownik');    
      if ($userSession->details) {
      $e->getViewModel()->setVariable('uzytkownik', $userSession->details);   
      }
   
                    
        }, 100);
        
    
    
    }
    
    
    
}

