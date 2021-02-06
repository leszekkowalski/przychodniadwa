<?php

namespace Application\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\JsonModel;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Polaczenie\UzytkownikPolaczenie;

class LekarzjsonController extends AbstractController{
    
  protected $polaczenieDb; 
  protected $polaczenieUzytkownikDb;


  public function __construct(LekarzPolaczenie $polaczenie, UzytkownikPolaczenie $polaczenieUzytkownik) {
     $this->polaczenieDb=$polaczenie;
     $this->polaczenieUzytkownikDb=$polaczenieUzytkownik;
 }   
 
 public function dodajjsonpeselAction() {
     
  if($this->params()->fromRoute('action')=='dodajjsonpesel'){
     
      if ($this->getRequest()->isPost()){ 
      
    $pesel = $this->params()->fromPost('pesel', 1);  
    $idlekarz = $this->params()->fromPost('idlekarz', 0);  
      if($pesel==1){
          $this->redirect()->toRoute('lekarz');
      }
      
      $wynik=$this->polaczenieDb->sprawdzPeselJson($pesel,$idlekarz);
      if(!$wynik){
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
 
  public function dodajjsonmailAction() {
     
  if($this->params()->fromRoute('action')=='dodajjsonmail'){
     
      if ($this->getRequest()->isPost()){ 
      
    $mail = $this->params()->fromPost('mail', 1);  
    $idlekarz=$this->params()->fromPost('idlekarz',0);
      if($mail==1){
          $this->redirect()->toRoute('lekarz');
      }
      
      $wynik=$this->polaczenieDb->sprawdzMailJson($mail,$idlekarz);
      if($wynik){
          $odpowiedz='true';
      }else{
          $odpowiedz='false';
      }
        $viewModel = new JsonModel();
       $viewModel->setVariable('items', $odpowiedz);
        return $viewModel;
      }else{
            $this->redirect()->toRoute('lekarz');
      }
  }else{
        $this->redirect()->toRoute('lekarz');
  }
     
 }
 
 public function lekarzindexjsonAction() {
     
    if ($this->getRequest()->isPost()){  
       
      $page = $this->params()->fromPost('page', 0);   
       $licznik = $this->params()->fromPost('licznik', 0);   
       $ileNaStrone = $this->params()->fromPost('ilenastrone', 0);   
        
       if($page==0 || $licznik==0 || $ileNaStrone==0){
           
           $this->redirect()->toRoute('lekarz'); 
       }
       
       $lekarze=$this->polaczenieDb->lekarzindexjson($ileNaStrone,$page);
       
       
       
   $lekarzearray=(iterator_to_array($lekarze, true));
   
 
   
   
   $lekarzePosortowani=$this->sortujPoNazwisku($lekarzearray);
   //echo $lekarzePosortowani[0]->getNazwisko().' '.$lekarzePosortowani[0]->getPesel();
   //exit();
   
   $jsonData = array(); 
      $idx = 0; 
      foreach($lekarzePosortowani as $sampledata) { 
          if($sampledata->getZdjecie()===null){
              $sampledata->setZdjecie('./public/img/Anonymous_male.jpg');
          }
         $temp = array( 
            'idlekarz'=>$sampledata->getIdlekarz(), 
            'imie' => $sampledata->getImie(), 
            'nazwisko' => $sampledata->getNazwisko(), 
            'pesel' => $sampledata->getPesel(),
             'zdjecie'=>$sampledata->getZdjecie(),
             'specjalnosc'=>$sampledata->getSpecjalnosc(),
          
         );  
         $jsonData[$idx++] = $temp; 
      } 
   
   
     $iterator = new \RecursiveArrayIterator($jsonData);
   
  // var_dump($lekarzearray[0]);exit();
    //   $b=$lekarze->current();
     //  echo $nazwisko=$b->getNazwisko();
      // echo $wynik= strcmp($nazwisko, 'Oożek');exit();
   
     // $d=( iterator_to_array($combinedIterator, false) ); 
     //  echo $lekarzearray[0]->getNazwisko();
     // exit();
    $jsonLekarze= \Laminas\Json\Json::encode($iterator);
   
        
     //$items=null;
        $viewModel = new JsonModel();
        $viewModel->setVariable('jsonLekarze', $jsonLekarze);
      // $viewModel->setVariable('jsonLekarze', $odpowiedz);
        //$viewModel->setTerminal(true);
       return $viewModel;
       
       
       
        
    }else{
        $this->redirect()->toRoute('lekarz');
  } 
     
 }
 
 protected function sortujPoNazwisku($tablica) {
     
    $dlugosc= count($tablica) ; 
    
    for($i=0;$i<$dlugosc;$i++){
        $znacznik=false;
        for($j=0;$j<$dlugosc-1;$j++){
            if(strcmp($tablica[$j]->getNazwisko(),$tablica[$j+1]->getNazwisko())>0){
                $tmp=$tablica[$j+1]->getNazwisko();
                $tablica[$j+1]->setNazwisko($tablica[$j]->getNazwisko());
                $tablica[$j]->setNazwisko($tmp);
                $tmp=$tablica[$j+1]->getImie();
                $tablica[$j+1]->setImie($tablica[$j]->getImie());
                $tablica[$j]->setImie($tmp);
                $tmp=$tablica[$j+1]->getPesel();
                $tablica[$j+1]->setPesel($tablica[$j]->getPesel());
                $tablica[$j]->setPesel($tmp);
                $tmp=$tablica[$j+1]->getIdlekarz();
                $tablica[$j+1]->setIdlekarz($tablica[$j]->getIdlekarz());
                $tablica[$j]->setIdlekarz($tmp);
                $tmp=$tablica[$j+1]->getZdjecie();
                $tablica[$j+1]->setZdjecie($tablica[$j]->getZdjecie());
                $tablica[$j]->setZdjecie($tmp);
                $tmp=$tablica[$j+1]->getSpecjalnosc();
                $tablica[$j+1]->setSpecjalnosc($tablica[$j]->getSpecjalnosc());
                $tablica[$j]->setSpecjalnosc($tmp);
                
                $znacznik=true;
            }
        }
        if(!$znacznik)break;
    }
    return $tablica; 
 }
 
 public function uzytkownikindexjsonAction() {
    
     if ($this->getRequest()->isPost()){  
         
      $page = $this->params()->fromPost('page', 0);   
      $licznik = $this->params()->fromPost('licznik', 0);   
      $ileNaStrone = $this->params()->fromPost('ileNaStrone', 0);   
        
       if($page==0 || $licznik==0 || $ileNaStrone==0){
           
           $this->redirect()->toRoute('uzytkownik'); 
       }
       
       $uzytkownicy=$this->polaczenieUzytkownikDb->uzytkownikindexjson($ileNaStrone, $page);
      
       $uzytkownicyarray=(iterator_to_array($uzytkownicy, true));
       
       $uzytkownicyPosortowani=$this->sortujUzytkownikPoNazwisku($uzytkownicyarray);
       
       $lekarzeArrayId=$this->polaczenieDb->pobierzWszystkoLekarzId();
       
       $roleArrayId=$this->polaczenieUzytkownikDb->pobierzRoleUzytkownikaJakoArray();
       
        $jsonData = array(); 
      $idx = 0; 
      foreach($uzytkownicyPosortowani as $sampledata) { 
          if($sampledata->getLekarz()!=null){
              
    $imie_i_nazwisko_lekarz=$lekarzeArrayId[$sampledata->getLekarz()]['imie'].' '.$lekarzeArrayId[$sampledata->getLekarz()]['nazwisko'];
    $idlekarz=$lekarzeArrayId[$sampledata->getLekarz()]['idlekarz'];
          }else{
           $imie_i_nazwisko_lekarz='';
           $idlekarz=0;
          }
          
         if(isset($roleArrayId[$sampledata->getIduzytkownik()])) 
         {
           $rola=$roleArrayId[$sampledata->getIduzytkownik()]['name'];  
         }else{
             $rola='';
         }
          
          
          
         $temp = array( 
            'iduzytkownik'=>$sampledata->getIduzytkownik(), 
            'imie_i_nazwisko' => $sampledata->getImie().' '.$sampledata->getNazwisko(), 
            'mail' => $sampledata->getMail(),
            'status'=>$sampledata->getStatus(),
            'imie_nazwisko_lekarz'=>$imie_i_nazwisko_lekarz,
            'idlekarz'=>$idlekarz,
             'rola'=>$rola,
            
         ); 
        
         $jsonData[$idx++] = $temp; 
      } 
       
       $iterator = new \RecursiveArrayIterator($jsonData);
 
    $jsonUzytkownicy= \Laminas\Json\Json::encode($iterator);
   
        
     //$items=null;
        $viewModel = new JsonModel();
        $viewModel->setVariable('jsonUzytkownicy', $jsonUzytkownicy);

       return $viewModel;
       
       
        }else{
            $this->redirect()->toRoute('uzytkownik');
        }

 }
 
  protected function sortujUzytkownikPoNazwisku($tablica) {
     
    $dlugosc= count($tablica) ; 
    
    for($i=0;$i<$dlugosc;$i++){
        $znacznik=false;
        for($j=0;$j<$dlugosc-1;$j++){
            if(strcmp($tablica[$j]->getNazwisko(),$tablica[$j+1]->getNazwisko())>0){
                $tmp=$tablica[$j+1]->getNazwisko();
                $tablica[$j+1]->setNazwisko($tablica[$j]->getNazwisko());
                $tablica[$j]->setNazwisko($tmp);
                $tmp=$tablica[$j+1]->getImie();
                $tablica[$j+1]->setImie($tablica[$j]->getImie());
                $tablica[$j]->setImie($tmp);
                $tmp=$tablica[$j+1]->getMail();
                $tablica[$j+1]->setMail($tablica[$j]->getMail());
                $tablica[$j]->setMail($tmp);
                $tmp=$tablica[$j+1]->getIduzytkownik();
                $tablica[$j+1]->setIduzytkownik($tablica[$j]->getIduzytkownik());
                $tablica[$j]->setIduzytkownik($tmp);
                $tmp=$tablica[$j+1]->getStatusId();
                $tablica[$j+1]->setStatus($tablica[$j]->getStatusId());
                $tablica[$j]->setStatus($tmp);
                $tmp=$tablica[$j+1]->getLekarz();
                $tablica[$j+1]->setLekarz($tablica[$j]->getLekarz());
                $tablica[$j]->setLekarz($tmp);
                
                $znacznik=true;
            }
        }
        if(!$znacznik)break;
    }
    return $tablica; 
 }
 
   public function openjsonmailAction() {
     
  if($this->params()->fromRoute('action')=='openjsonmail'){
     
      if ($this->getRequest()->isPost()){ 
      
    $mail = $this->params()->fromPost('mail', 1);  
    
      $wynik=$this->polaczenieUzytkownikDb->sprawdzOpenMailJson($mail);
      
      if($wynik){
          $odpowiedz='true';
      }else{
          $odpowiedz='false';
      }
        $viewModel = new JsonModel();
       $viewModel->setVariable('items', $odpowiedz);
        return $viewModel;
      }else{
            $this->redirect()->toRoute('home');
      }
  }else{
        $this->redirect()->toRoute('home');
  }
     
 }
 
 
 
}

