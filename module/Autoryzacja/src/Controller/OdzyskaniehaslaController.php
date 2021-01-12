<?php

namespace Autoryzacja\Controller;

use Application\Controller\AbstractController;
use Autoryzacja\Form\OdzyskajHasloForm;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use Application\Service\UzytkownikManager;

class OdzyskaniehaslaController extends AbstractController
{
 
    public $fleshMessager;
   public  $uzytkownikManager;
    
    public function __construct(UzytkownikManager $m) 
    {
        $this->uzytkownikManager=$m;
        $this->fleshMessager=new FlashMessenger();
    }
    
    public function odzyskajAction()
    {
      
    $isLoginError=false;
    $fleshMessager=$this->fleshMessager;
     $form=new OdzyskajHasloForm(
              'odzyskaj-haslo',
              [
                'baseUrl'=>$this->baseUrl,
              ]
              );
     
    $viewModel=new ViewModel(['form'=>$form,'isLoginError'=>$isLoginError]); 
     
     if(!$this->getRequest()->isPost())
     {
        return $viewModel;
     };
        
     $form->setData($this->getRequest()->getPost());
     
     if(!$form->isValid()){
         $isLoginError=true;
         return $viewModel=new ViewModel(['form'=>$form,'isLoginError'=>$isLoginError]);
     }
     
     $mail=$this->getRequest()->getPost()['mail'];
     
     
     // sprawdzam czy wpisany mail istnieje w bazie
    $uzytkownik=$this->uzytkownikManager->znajdzUzytkownikPoMail($mail);
    
    // jesli nie istnieje - kieruje na strone logowania
     if(!$uzytkownik instanceof \Application\Model\Uzytkownik)
     {
        $fleshMessager->addErrorMessage('Założ konto uzytkownika !!');
        return $this->redirect()->toRoute('rejestruj'); 
     }
     //jesli isnieje ale ma status inny niz aktywny, wraca na strone i informuje o tym
     if($uzytkownik->getStatusId()!=1){
        $fleshMessager->addErrorMessage('Konto ma status NIEAKTYWNE. Napisz najpierw do administratora w celu aktywacji konta !!');
        return $this->redirect()->toRoute('odzyskaj-haslo');  
     }
     
     // wpisany mail (uzytkownik) istnieje, wiec działam dalej
     
     $this->uzytkownikManager->dodajUzytkownik($uzytkownik);
     
     // tworze szyfrowany token, zapisuje do bazy i wysysąłm do uzytkownika
     $this->uzytkownikManager->tworzPwd_sol_wyslijMaila($this->baseUrl);
     
      $fleshMessager->addErrorMessage('Na twojego maila została wysłana wiadomość z linkiem do zmiany hasła. Masz 24 godz. na zmiane hasła. Po tym czasie link będzie nieaktywny !!!');
        return $this->redirect()->toRoute('home');
 
    }
    
    public function ustawHasloAction()
    {
       $fleshMessager=$this->fleshMessager;
        
       $token=$this->params()->fromQuery('token',null);
       $mail=$this->params()->fromQuery('mail',null);
       
       if($token!=null && (!is_string($token) || strlen($token)!=32))
       {
           throw new \Exception('Błędny token ..........!!!!!!!!!');
       }
       
       $wynik=$this->uzytkownikManager->kontrolaHasloJakoToken($token,$mail);
       
       if($wynik==-1)
       {
        $fleshMessager->addErrorMessage('Błąd tokenów ....!!!!!');
        return $this->redirect()->toRoute('home');
       }
       if($wynik==-2)
       {
        $fleshMessager->addErrorMessage('Minął termin ważnosci tokenów. Musisz ponownie zresetować hasło');
        return $this->redirect()->toRoute('odzyskaj-haslo');
       }
       
       if($wynik==1){
           
           $form=new \Autoryzacja\Form\ResetujHasloForm();
           
           $request=$this->getRequest();
           $viewModel = new ViewModel(['form' => $form]);
           
           if(!$request->isPost()){
               return $viewModel;
           }
           $data=$request->getPost();
           $form->setData($data);
          
           if(!$form->isValid()){
             //  var_dump ($form->getMessages());
             //  exit();
             $fleshMessager->addErrorMessage('Niestety. Błąd podczas wsprowdazanie nowego hasła. Spróbuj ponownie !!');
             return $this->redirect()->toRoute('home');  
           }
           
           if($this->uzytkownikManager->wpiszNoweHasloPrzezToken($token, $mail, $data['powtorz_haslo']))
           {
           $fleshMessager->addErrorMessage('Twoje hasło zostało zmienione. Zaloguj się na swoje konto');
             return $this->redirect()->toRoute('login'); 
           }else
           {
             $fleshMessager->addErrorMessage('Niestety. Błąd podczas wsprowdazanie nowego hasła. Spróbuj ponownie !!');
             return $this->redirect()->toRoute('home');  
           }
 
       }
       
    }
    
}

