<?php

namespace Autoryzacja\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\ViewModel;
use Laminas\Session;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use Autoryzacja\Form\RejestrujForm;
use Application\Model\Lekarz;

class RejestracjaController extends AbstractController{
   
public $fleshMessager;  
    
    
  public function __construct() 
  {
      $this->fleshMessager=new FlashMessenger();
  }
    
  public function indexAction()
  {
      $form=new RejestrujForm(
              'rejestruj_form',
              [
               'dbAdapter'  => Lekarz::pobierzAdapter(),
                'baseUrl'=>$this->baseUrl,
              ]
              );
      
      $viewParametry=['form'=>$form];
      
      if($this->request->isPost())
      {
          $form->setData($this->request->getPost());
          
          if($form->isValid())
          {
              
          }else{
              return ['messages'=> $form->getMessages(),'form'=>$form];
              //form zle zwalidowany
          }
          
      }else{
          return $viewParametry;
      }
      
      
      
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

