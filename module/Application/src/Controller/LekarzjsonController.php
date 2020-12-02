<?php

namespace Application\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\JsonModel;
use Application\Polaczenie\LekarzPolaczenie;

class LekarzjsonController extends AbstractController{
    
  protected $polaczenieDb;  
    
    public function __construct(LekarzPolaczenie $polaczenie) {
     $this->polaczenieDb=$polaczenie;
 }   
 
 public function dodajjsonAction() {
     
  if($this->params()->fromRoute('action')=='dodajjson'){
     
      if ($this->getRequest()->isPost()){ 
      
    $pesel = $this->params()->fromPost('pesel', 1);  
      if($pesel==1){
          $this->redirect()->toRoute('lekarz');
      }
      
      $wynik=$this->polaczenieDb->sprawdzPeselJson($pesel);
      if($wynik){
          $odpowiedz='true';
      }else{
          $odpowiedz='false';
      }
     
   //  $jsonObject= \Laminas\Json\Json::encode($jsonData);
     //$items=null;
        $viewModel = new JsonModel();
     //   $viewModel->setVariable('items', $jsonObject);
       $viewModel->setVariable('items', $odpowiedz);
        //ustawia lauout jako niwwidoczny
      //  $viewModel->setTerminal(true);
        return $viewModel;
      }else{
            $this->redirect()->toRoute('lekarz');
      }
  }else{
        $this->redirect()->toRoute('lekarz');
  }
     
 }
 
}

