<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Autoryzacja\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\ViewModel;
use Autoryzacja\Form\LogujForm;
use Laminas\Authentication\Result;
use Autoryzacja\Service\LogowanieAuth;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use Laminas\Uri\Uri;
use Laminas\Session;



class AutoryzacjaController extends AbstractController
{
    /**
     *
     * @var type Autoryzacja\Service\AutoryzacjaManager;
     */
    private $autoryzacjaManager;
    /**
     *
     * @var type Laminas\Authentication\AuthenticationService;
     */
    private $autoryzacjaService;
    
    /**
     *
     * @var type Autoryzacja\Service\LogowanieAuth;
     */
    private $logowanieAuth;
    
    public $fleshMessager;


    public function __construct(LogowanieAuth $auth) 
    {
      $this->logowanieAuth=$auth; 
      $this->fleshMessager=new FlashMessenger();
    }
    
    
    
    public function logujAction()
    {
        
        $fleshMessager=$this->fleshMessager;
        $sessionUzytkownik=new Session\Container('uzytkownik');
        
      //przechwytuje param 'wyloguj' (jesli nie ma, ustawiam na 'nie') w celu pokazania komunikatu na stronie loguj o poprawnym wylogowaniu
     $wyloguj = (string)$this->params()->fromQuery('wyloguj', 'nie');
       if($wyloguj=='tak')
        { 
           $fleshMessager->addErrorMessage('Zostałeś wylogowany !!');
           return $this->redirect()->toRoute('login');
       }  
     
     
        if(isset($sessionUzytkownik->details))
        { 
           $fleshMessager->addErrorMessage('JESTEŚ JUŻ ZALOGOWANY !!');
           return $this->redirect()->toRoute('home');
       }
     
        
     //przechwutuje parametr redirectUrl w celu zabezpieczenia przea atakiem
     //konfiguracja nastepuje w pliku Module.php
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Zbyt długi argument \"redirectUrl\"");
        }
        
        
        
        $form=new LogujForm();
        $form->get('redirect_url')->setValue($redirectUrl);
        $isLoginError=false;
        
        if(!$this->getRequest()->isPost()){
          return new ViewModel([
            'form'=>$form,
            'isLoginError'=>$isLoginError,
            'redirectUrl' => $redirectUrl, 
              'wyloguj'=>$wyloguj
                ]);
        }
        
        $form->setData($this->getRequest()->getPost());
        
        if(!$form->isValid()){
            $isLoginError = true;
            
            return new ViewModel([
            'form'=>$form,
            'isLoginError'=>$isLoginError,
            'redirectUrl' => $redirectUrl,  
             'wyloguj'=>$wyloguj   
                ]);
        }
        
        $wynik=$this->logowanieAuth->authenticate
                (
            $form->get($form::LOGIN_FIELDSET)->get('mail')->getValue(),
            $form->get($form::LOGIN_FIELDSET)->get('haslo')->getValue()
                );
        
              //  var_dump($wynik->getIdentity());
        
        if($wynik->getCode()==Result::SUCCESS){
            
            if($wynik->getIdentity()===null){
              $fleshMessager->addErrorMessage('Użytkownik posiada status NIEAKTYWNY. Napisz maila do administratora w celu aktywacji konta');
            $isLoginError = false;
            
            return new ViewModel([
            'form'=>$form,
            'isLoginError'=>$isLoginError,
            'redirectUrl' => $redirectUrl, 
             'wyloguj'=>$wyloguj   
                ]);  
            
            }else{
              //zapisuje w sesji kontenera dane uzytkownika zapisane w obiekcie Uzytkownik  
              $uzytkownik=$wynik->getIdentity();
              
              $sessionUzytkownik->details=$uzytkownik;

    //zabezpieczenie przed atakiem przeadresownia - jeśli ktoś próbuje przekierować użytkownika do innej domeny)
             $redirectUrl = $this->params()->fromPost('redirect_url', ''); 
             
              if (!empty($redirectUrl)) {
                        // Poniżej sparwdzam mozliwy atak redirect
                        // jesli ktos chce przekierować uzytkownika do innej domeny.
                  $uri = new Uri($redirectUrl);
                if (!$uri->isValid() || $uri->getHost()!=null)
                   throw new \Exception('Nieprawidłowy adres przekierowania URL: ' . $redirectUrl);
                    }   
    //Jeśli podano adres URL przekierowania, przekieruj użytkownika do tego adresu;
    // w przeciwnym razie przekieruj na stronę z danymi sesji uzytkownika
            // echo $redirectUrl;             exit();
                    
                    if(empty($redirectUrl)) {
                //  return $this->redirect()->toRoute('home'); 
              return $this->redirect()->toRoute('rejestruj', ['action' => 'loginprogressuzytkownik']);     
                    } else {
                      $this->redirect()->toUrl($redirectUrl);
                   
                    }
                 
            }
               
        }else{
           $isLoginError = true;
            
            return new ViewModel([
            'form'=>$form,
            'isLoginError'=>$isLoginError,
            'redirectUrl' => $redirectUrl,  
             'wyloguj'=>$wyloguj   
                ]);
        }
        //    var_dump($wynik);exit();
           
    }
    
    public function wylogujAction()
    {
        
        $session = new Session\Container('uzytkownik');
        $fleshMessager=$this->fleshMessager;
        
        if (!$session->details) 
        {
        $fleshMessager->addErrorMessage('Użytkownik nie był zalogowany....');
        return $this->redirect()->toRoute('login'); 
        }else{
       $fleshMessager->addErrorMessage('Użytkownik został wylogowany');
       $session->getManager()->destroy();
       return $this->redirect()->toRoute('login',[], 
                        ['query'=>['wyloguj'=>'tak']]); 
        }
    }
    
    public function brakAutoryzacjiAction()
    {
       $this->getResponse()->setStatusCode(403);
    
       return new ViewModel(); 
    }
}
