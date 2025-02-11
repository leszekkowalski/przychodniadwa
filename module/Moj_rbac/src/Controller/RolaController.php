<?php

namespace Moj_rbac\Controller;

use Application\Controller\AbstractController;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Application\Polaczenie\UzytkownikPolaczenie;
use Moj_rbac\Model\Rola;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use Moj_rbac\Service\RolaManager;
use InvalidArgumentException;
use Moj_rbac\Service\RbacManager;
use Laminas\Session;


class RolaController extends AbstractController
{
    private $polaczenieRbac;
    
    private $polaczenieUzytkownik;
    
    public $fleshMessager;
    
    protected $roleManager;
    
    protected $rbacManager;


    public function __construct(RbacPolaczenie $polaczenie, UzytkownikPolaczenie $pol,RolaManager $rolaManager,RbacManager $rbacManager)
    {
      $this->polaczenieRbac=$polaczenie;
      $this->polaczenieUzytkownik=$pol;
      $this->fleshMessager=new FlashMessenger();
      $this->roleManager=$rolaManager;
      $this->rbacManager=$rbacManager;
    }
    
    
    public function indexAction()
    {

        $role=$this->polaczenieRbac->pobierzWszystkieRole();
        
        if(!count($role))
        {
          echo 'brak ról';          exit();  
        }
        
        $view=new ViewModel(['role'=>$role]);

        return $view;
    }
    
    public function edytujAction() 
    {
       $id=$this->params()->fromRoute('id', 0);
       if(!$id)
       {
          $this->fleshMessager->addErrorMessage('Wystapił bład. Brak parametru. Powiadom administratora');
          return $this->redirect()->toRoute('rola');  
       }
       
       try{
           $tablicaRole=$this->polaczenieRbac->pobierzWszystkieRole();
       } catch ( InvalidArgumentException $ex) {
           $this->fleshMessager->addErrorMessage('Wystapił bład pobrania danych. Powiadom administratora');
          return $this->redirect()->toRoute('rola');  
       }
       
       $tablicaRole->buffer();
       
       foreach ($tablicaRole as $pojedynczaRola)
       {
           if($pojedynczaRola->getIdrola()==$id)
           {
               $rola=$pojedynczaRola;break;
           }
       }
       
       $form=new \Moj_rbac\Form\RoleFormEdit('role-form-edit',$rola);
       
      //$form->bind($rola);

      $dzieckoRoleId=$this->polaczenieRbac->pobierzRoleZRole_hierarchia($rola->getIdrola());
       
      $tablicaOpcji=$this->roleManager->zamienRoleNatabliceOptions($tablicaRole, $dzieckoRoleId, $rola);
      
      $viewModel = new ViewModel(['form' => $form,'tablicaOpcji'=>$tablicaOpcji]); 
       
      $request=$this->getRequest();
      
       $form->setData(array(
               'idrola'=>$rola->getIdrola(),
               'name'=>$rola->getName(),
               'opis'=>$rola->getOpis()
           ));
       
       if(!$request->isPost())
       {
           return $viewModel;
       }
       
       $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewModel;
        }
        
      
        $dane=$form->getData();
       // print_r($dane); exit();
        
       $wynik=$this->roleManager->updateRola($this->polaczenieRbac, $dane, $rola);
        
      if($wynik===1){
          $this->rbacManager->init(true);
          $napis='OK. Rola o nazwie '.$rola->getName().' została zaktualizowana';
           $this->fleshMessager->addSuccessMessage($napis);
            return $this->redirect()->toRoute('rola');
      }else{

          $napis='BŁĄD !!!. Rola o nazwie '.$rola->getName().' NIE została zaktualizowana';
           $this->fleshMessager->addErrorMessage($napis);
            return $this->redirect()->toRoute('rola'); 
      }
       
       
    }
    
    public function dodajAction()
    {
        $request=$this->getRequest();
      
        $form=new \Moj_rbac\Form\RolaForm('rola-form');
        
        $tablicaRole=$this->polaczenieRbac->pobierzWszystkieRole();
        
        $tablicaOpcji=$this->roleManager->zamienObiektyRolaNaTabliceOptions($tablicaRole);
        
        $viewParametry=new ViewModel(['form'=>$form,'tablicaOpcji'=>$tablicaOpcji]);
        
        if(!$request->isPost())
        {
            return $viewParametry;
        }
        
        $form->setData($request->getPost());
        
        if(!$form->isValid())
        { 
            return $viewParametry; 
        }
               
        $danePost=$form->getData();
  
        $transakcja=$this->polaczenieRbac->getAdapter();
        $transakcja->getDriver()->getConnection()->beginTransaction();

        try {
            $rola = $this->polaczenieRbac->wpiszRola($danePost);
            if($rola)
            {
                $this->rbacManager->init(true);
            }
        } catch (\Exception $ex) {
            // An exception occurred; we may want to log this later and/or
            // report it to the user. For now, we'll just re-throw.
          
            $transakcja->getDriver()->getConnection()->rollback();
             $this->fleshMessager->addErrorMessage('Wystapił bład. Rola nie została wpisana');
            return $this->redirect()->toRoute('rola');     
        }
        
        $transakcja->getDriver()->getConnection()->commit();
        
        $this->fleshMessager->addSuccessMessage('OK. Rola została wpisana !!');
            return $this->redirect()->toRoute('rola');  
        
        
    }
    
    public function usunAction() 
    {
     $id=$this->params()->fromRoute('id', 0);
       if(!$id)
       {
          $this->fleshMessager->addErrorMessage('Wystapił bład. Brak parametru. Powiadom administratora');
          return $this->redirect()->toRoute('rola');  
       }
       
       try{
           $rola=$this->polaczenieRbac->pobierzRole($id);
       } catch (Exception $ex) {
             $this->fleshMessager->addErrorMessage('Wystapił bład parametru. Powiadom administratora');
          return $this->redirect()->toRoute('rola');
       }
       
       $viewModel=new ViewModel(['rola'=>$rola]);
       
       $request=$this->getRequest();
       
       if(!$request->isPost())
       {
          return $viewModel; 
       }
       
       if ($id != $request->getPost('id')
            || 'Usuwam' !== $request->getPost('confirm', 'no')
        ) {
           $this->fleshMessager->addErrorMessage('Nie usunieto ROLI !!. Tak zdecydowałęś');
            return $this->redirect()->toRoute('rola');
        }
        
        $wynik=$this->polaczenieRbac->usunRola($rola);
        
        if($wynik){
            $this->rbacManager->init(true);
            $napis='OK. Rola '.$rola->getName().' została usunięta';
             $this->fleshMessager->addSuccessMessage($napis);
            return $this->redirect()->toRoute('rola'); 
        }else{
           $this->fleshMessager->addErrorMessage('Nie usunieto ROLI !!. Wystapił bład. Powiadom administratora');
            return $this->redirect()->toRoute('rola');  
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

          $rola=$this->polaczenieRbac->pobierzRole($idRola);
 
      $uprawnieniaAll=$this->polaczenieRbac->pobierzWszystkieUprawnienia();
       
      $view=new ViewModel(['uprawnieniaAll'=>$uprawnieniaAll,'rolaUprawnienia'=>$uprawnieniaRol,'rola'=>$rola,'tablicaIdrola'=>$tablicaIdrola]);
      
        return $view;
      
    }
    
    public function testAction() 
    {
      
      // $this->rbacManager->init();
      // $this->rbacManager->isGranted($this->uzytkownik, 'uprawnienia');
        $userSession = new Session\Container('uzytkownik');
        
       if($userSession->details){ 
            return [
            'uzytkownik' => $userSession->details
            ]; 
       }
        
        
    
   //    print_r($tablica);
     // $jeden=$tablica[0]->getDzieciRola();
      // echo $jeden[1];
       //echo $jeden[3]->getName();
     //  $tablica->buffor();
     //  exit();
     //  foreach ($tablica as $rola)
    //   {
          // echo $rola->getName();
         //  print_r($key);
       //    $dzieci=$rola->getDzieciRola();
           
        //   foreach ($dzieci as $dziecko)
         //  {
             //  echo $dziecko->getName();
         //  }
         //  echo '<br />';
      // }
      
       
     //  exit();
       
       
       
       
       
       
      
     
    
    }
}
