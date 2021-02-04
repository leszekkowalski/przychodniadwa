<?php

namespace Moj_rbac\Controller;

use Application\Controller\AbstractController;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use Moj_rbac\Form\UprawnienieForm;
use Moj_rbac\Model\Uprawnienie;
use Moj_rbac\Service\RolaManager;


class UprawnieniaController extends AbstractController
{
    
    public $polaczenieRbac;
    
    public $fleshMessager;
    
    protected $roleManager;
    
    public function __construct(RbacPolaczenie $pol, RolaManager $roleManager) 
    {
       $this->polaczenieRbac=$pol; 
       $this->fleshMessager=new FlashMessenger();
       $this->roleManager=$roleManager;
    }
 
    public function indexAction()
    {
       $uprawnienia=$this->polaczenieRbac->pobierzWszystkieUprawnienia();
       
        $view=new ViewModel(['uprawnienia'=>$uprawnienia]);
      
        return $view;
    }
    
    public function dodajAction() 
    {
    
        $request   = $this->getRequest();
        
        $form=new UprawnienieForm($name='dodaj-uprawnienie');
        
        $viewModel = new ViewModel(['form' => $form]);

        if (! $request->isPost()) {
            return $viewModel;
        }

        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewModel;
        }

        $uprawnienie = $form->getData();

        try {
            $obiektUprawnienie = $this->polaczenieRbac->wpiszUprawnienie($uprawnienie);
        } catch (\Exception $ex) {
            
            $napis='Bład. Nie wpisano Uprawnienia o nazwie "'.$obiektUprawnienie->getName().'". Powiadom administratora';
            $this->fleshMessager->addErrorMessage($napis);
          return $this->redirect()->toRoute('uprawnienia');
        }
                
        $napis='OK. Wpisano Uprawnienie o nazwie "'.$obiektUprawnienie->getName().'".';
         $this->fleshMessager->addSuccessMessage($napis);
        return $this->redirect()->toRoute('uprawnienia', );
  
    }
    
    public function edytujAction()
    {
        $id=$this->params()->fromRoute('id', 0);
       if(!$id)
       {
          $this->fleshMessager->addErrorMessage('Wystapił bład. Brak parametru. Powiadom administratora');
          return $this->redirect()->toRoute('uprawnienia');  
       }
       try{
          $uprawnienie=$this->polaczenieRbac->pobierzJednoUprawnienieId($id); 
       } catch (Exception $ex)
       {
           $this->fleshMessager->addErrorMessage('Wystapił błąd parametru. Powiadom administratora');
          return $this->redirect()->toRoute('uprawnienia');
       }
       
       
       $form=new UprawnienieForm($name='uprawnienie-form', $uprawnienie);
       
       $form->bind($uprawnienie);
       
       $request=$this->getRequest();
       
       if(!$request->isPost())
       {
           return new ViewModel(['form'=>$form]);
       }
       
       $form->setData($request->getPost());
       
       if(!$form->isValid())
       {
         return new ViewModel(['form'=>$form]);
       }
       
       $uprawnienie=$this->polaczenieRbac->edytujUprawnienie($uprawnienie);
       
       $napis='OK. Uprawnienie "'.$uprawnienie->getName().'" zostało zaktualizowane';
       
        $this->fleshMessager->addSuccessMessage($napis);
        
        return $this->redirect()->toRoute('uprawnienia');
       
    }
    
    public function usunAction()
    {
        $id=$this->params()->fromRoute('id', 0);
       if(!$id)
       {
          $this->fleshMessager->addErrorMessage('Wystapił bład. Brak parametru. Powiadom administratora');
          return $this->redirect()->toRoute('uprawnienia');  
       }
       
       try{
           $uprawnienie=$this->polaczenieRbac->pobierzJednoUprawnienieId($id);
       } catch (Exception $ex) {
             $this->fleshMessager->addErrorMessage('Wystapił bład parametru. Powiadom administratora');
          return $this->redirect()->toRoute('uprawnienia');
       }
       
       $viewModel=new ViewModel(['uprawnienie'=>$uprawnienie]);
       
       $request=$this->getRequest();
       
       if(!$request->isPost())
       {
          return $viewModel; 
       }
       
       if ($id != $request->getPost('id')
            || 'Usuwam' !== $request->getPost('confirm', 'no')
        ) {
           $this->fleshMessager->addErrorMessage('Nie usunieto UPRAWNIENIA !!. Tak zdecydowałęś');
            return $this->redirect()->toRoute('uprawnienia');
        }
        
        $wynik=$this->polaczenieRbac->usunUprawnienie($uprawnienie);
        
        if($wynik){
            $napis='OK. Uprawnienie '.$uprawnienie->getName().' została usunięta';
             $this->fleshMessager->addSuccessMessage($napis);
            return $this->redirect()->toRoute('uprawnienia'); 
        }else{
           $this->fleshMessager->addErrorMessage('Nie usunieto Uprawnienia !!. Wystapił bład. Powiadom administratora');
            return $this->redirect()->toRoute('uprawnienia');  
        }  
    }
    
    public function widokAction() 
    {
     $idRola=$this->params()->fromRoute('id', 0);
       if(!$idRola)
       {
          $this->fleshMessager->addErrorMessage('Wystapił bład. Brak parametru. Powiadom administratora');
          return $this->redirect()->toRoute('rola');  
       } 
       try{
        $rolaGlowna=$this->polaczenieRbac->pobierzRole($idRola);
       } catch (Exception $ex) {
           $this->fleshMessager->addErrorMessage('Wystapił bład pobrania. Powiadom administratora');
           return $this->redirect()->toRoute('rola');
       }
       
       $tablicaIdrola=array();
       $tablicaIdrola[$idRola]['idrola']=$idRola;
       $tablicaIdrola[$idRola]['dziedziczony']='[własny]';
       
        $tablicaIdrola=$this->roleManager->wyliczRoleDzieci($idRola, $tablicaIdrola, $this->polaczenieRbac);
       
       $tablicaDoZapytania=array();
       foreach ($tablicaIdrola as $rola)
       {
           $tablicaDoZapytania[]=$rola['idrola'];
       }
       
       $uprawnieniaRol=$this->polaczenieRbac->pobierzRola_Uprawnienia_tablica($tablicaDoZapytania);

      
       
      $uprawnieniaAll=$this->polaczenieRbac->pobierzWszystkieUprawnienia();
       
      
      $form=new \Moj_rbac\Form\EdytujUprawnieniaForm();
      
      foreach ($uprawnieniaAll as $uprawnienie)
      {
         
          $wskaznik=0;
          if($uprawnieniaRol){
          foreach ($uprawnieniaRol as $jednoUprawnienie)
          {
               if($uprawnienie['iduprawnienia']===$jednoUprawnienie['iduprawnienia'] )
               {
                  if($wskaznik==0) 
                  {
                  $name=$uprawnienie['name'];
                  $label=$name.' - '.$tablicaIdrola[$jednoUprawnienie['idrola']]['dziedziczony'];
                  if($tablicaIdrola[$jednoUprawnienie['idrola']]['dziedziczony']==='[dziedziczony]')
                  {
                      $disabled=true;
                  }else{
                      $disabled=false;
                  }
                    $form->addUprawnienieRola($name, $label, $disabled); 
                    $wskaznik=1;
                  }
               }
               
               if(!$wskaznik)
               {
               $name=$uprawnienie['name'];
                $label=$name;
                $disabled=false;
                $form->addUprawnienieRola($name, $label, $disabled); 
               }   
          }
          }else{
              $name=$uprawnienie['name'];
                $label=$name;
                $disabled=false;
                $form->addUprawnienieRola($name, $label, $disabled); 
          }
      }

      //////////////////koniec tworzenia formularza
      
        if($this->request->isPost()){
            
          $data=$this->params()->fromPost();
          $form->setData($data) ; 
        
          if($form->isValid())
          {
            $dane=$form->getData(); 

            $wynik=$this->roleManager->updateUprawnieniaDlaRola($this->polaczenieRbac,$rolaGlowna,$dane['uprawnienie']);
            
            if($wynik==1){
                $napis='Uprawnienia dla Roli "'.$rolaGlowna->getName().'" zostału zaktualizowane';
                $this->fleshMessager->addErrorMessage($napis);
                return $this->redirect()->toRoute('rola');
            }else{
           $this->fleshMessager->addErrorMessage('Wystapił bład !!. Powiadom administratora');
           return $this->redirect()->toRoute('rola');
            }           
          }
            
        }else{
            $data=[];
        
            foreach ($uprawnieniaAll as $uprawnienie){
                 $wskaznik=0;
                 
                 foreach ($uprawnieniaRol as $jednaRolaUprawnienie)
                 {
                     if($uprawnienie['iduprawnienia']===$jednaRolaUprawnienie['iduprawnienia'] )
                     {
                 if($wskaznik===0)
                  {
                     if($tablicaIdrola[$jednaRolaUprawnienie['idrola']]['dziedziczony']==='[dziedziczony]'
                             ||  $tablicaIdrola[$jednaRolaUprawnienie['idrola']]['dziedziczony']==='[własny]')
                     {
                      $data['uprawnienie'][$uprawnienie['name']]=1;  $wskaznik=1; 
                     }

                   }         
                     }
                 }
                
            }
            
            $form->setData($data); 
        }
      
      $bledy=$form->getMessages();

      return new ViewModel([
          'form'=>$form,
           'rola' => $rolaGlowna,
        'uprawnieniaAll'=>$uprawnieniaAll,
          'uprawnieniaRol'=>$uprawnieniaRol,
          'tablicaIdrola'=>$tablicaIdrola,
          'bledy'=>$bledy,
            ]);
   
    }
}

