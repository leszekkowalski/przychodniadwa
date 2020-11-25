<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\EventManager\EventManagerInterface;

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
        
      
      $events->attach('dispatch', function ($e) {
    //przekazuje baseUrl do szablonu layout.phtml
    $e->getViewModel()->setVariable('baseUrl', $this->getRequest()->getBasePath());
                    
        }, 100);
        
    parent::setEventManager($events);
    
    }
    
    
    
}

