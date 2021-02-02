<?php

namespace Moj_rbac\Controller;

use Application\Controller\AbstractController;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use Moj_rbac\Form\UprawnienieForm;
use Moj_rbac\Model\Uprawnienie;


class UprawnieniaController extends AbstractController
{
    
    public $polaczenieRbac;
    
    public $fleshMessager;
    
    public function __construct(RbacPolaczenie $pol) 
    {
       $this->polaczenieRbac=$pol; 
       $this->fleshMessager=new FlashMessenger();
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
}

