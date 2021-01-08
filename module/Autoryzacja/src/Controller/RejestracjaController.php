<?php

namespace Autoryzacja\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\ViewModel;
use Laminas\Session;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use Autoryzacja\Form\RejestrujForm;
use Application\Model\Lekarz;
use Application\Polaczenie\UzytkownikPolaczenie;

class RejestracjaController extends AbstractController{
   
public $fleshMessager;  
public $polaczenieUzytkownik;
    
    
  public function __construct(UzytkownikPolaczenie $db) 
  {
      $this->fleshMessager=new FlashMessenger();
      $this->polaczenieUzytkownik=$db;
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
             
             $dane=$form->getData();  
             $uzytkownik=new \Application\Model\Uzytkownik();
             $hydrator=new \Autoryzacja\Hydrator\RejestrujFormHydrator();
             $uzytkownik->exchangeArray($hydrator->hydrate(null,$dane));
             
             $uzytkownik=$this->polaczenieUzytkownik->wpiszUzytkownikPorejestracji($uzytkownik);
             
             $fleshMessager=$this->fleshMessager;
             
            if(is_int($uzytkownik->getIduzytkownik())){
                
                $fleshMessager->addSuccessMessage('Zostałeś zarejestrowany. Możesz sie zalogowac na swoje konto');
        return $this->redirect()->toRoute('login'); 
            }else{
           $fleshMessager->addErrorMessage('Nastapił błąd. Spróbuj ponownie za 10 minut lub powiadom mailem administratora');
        return $this->redirect()->toRoute('rejestruj'); 
            }
            
              
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

