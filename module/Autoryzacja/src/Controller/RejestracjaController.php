<?php

namespace Autoryzacja\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\ViewModel;
use Laminas\Session;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;

class RejestracjaController extends AbstractController{
   
public $fleshMessager;  
    
    
  public function __construct() 
  {
      $this->fleshMessager=new FlashMessenger();
  }
    
  public function indexAction()
  {
      
      return [];
  } 
  
  public function loginprogressuzytkownikAction() 
  {
      
      $prefix = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$this->baseUrl;
    
    // if ($_SERVER['HTTP_REFERER'] !== $prefix.'/rejestruj' &&
       if( $_SERVER['HTTP_REFERER'] !== $prefix.'/login'
        ) {
        return $this->redirect()->toUrl($_SERVER['HTTP_REFERER'], 302);
        }
     
    // $this->redirect()->toRoute('uzytkownik');
     return $this->redirect()->toRoute('rejestruj', ['action' => 'sesja']);
       // exit();
      
  }
  
  public function sesjaAction()
  {
        $flashInfo=$this->fleshMessager;
         $userSession = new Session\Container('uzytkownik');
        
       if($userSession->details){ 
            return [
            'uzytkownik' => $userSession->details
            ]; 
       }else{
           $flashInfo->addErrorMessage('NIE JESTEŚ ZALOGOWANY !!');
           $this->redirect()->toRoute('login');
       }
      
  }
    
}

