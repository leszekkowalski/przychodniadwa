<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Kalendarz;

use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    /**
     * Metoda init() wywoływana przez aplikację na samym poczatku i 
     * pozwala rejestrować tzw: "słuchacza" 
     * @param ModuleManager $manager
     */
    public function init(ModuleManager $manager) 
    {
        $eventManager=$manager->getEventManager();
        $sheredEventManager=$eventManager->getSharedManager();
        
        //rejestruje słuchaczy
        $sheredEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR, [$this,'onError'],100);
        $sheredEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_RENDER_ERROR, [$this,'onError'],100);
    }
    
    public function onError(MvcEvent $event) 
    {
        //pobieram dane o bledzie
        $bladAplikacji=$event->getParam('exception');
        
        if($bladAplikacji!=null)
        {
            $nazwaBledu=$bladAplikacji->getMessage();
            $plik=$bladAplikacji->getFile();
            $linia=$bladAplikacji->getLine();
            $stackTrace=$bladAplikacji->getTraceAsString();
        }
        
        $errorMassage=$event->getError();
        $nazawaControlera=$event->getController();
     
         // Przygotowanie maila.
        $to = 'admin@localhost.com';
        $subject = 'Your Website Exception';
        
        $body = '';
        if(isset($_SERVER['REQUEST_URI'])) {
            $body .= "Request URI: " . $_SERVER['REQUEST_URI'] . "\n\n";
        }
        $body .= "Controller: $nazawaControlera\n";
        $body .= "Error message: $errorMassage\n";
        if ($bladAplikacji!=null) {
            $body .= "Exception: $nazwaBledu\n";
            $body .= "File: $plik\n";
            $body .= "Line: $linia\n";
            $body .= "Stack trace:\n\n" . $stackTrace;
        }
        
        $body = str_replace("\n", "<br>", $body);
        
        echo $body;
        // Wysyłka maila po bledzie
        // 
       // mail($to, $subject, $body);
        
    }
    
    
}
