<?php

namespace Kalendarz\Controller;

use Application\Controller\AbstractController;
use Application\Polaczenie\LekarzPolaczenie;
use Kalendarz\Polaczenie\WydarzeniePolaczenie;
use Kalendarz\Entity\Wydarzenie;
use Application\Model\Lekarz;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;

class KalendarzController extends AbstractController
{
    protected $lekarzDb;
    
    protected $wydarzenieDb;
    
     public $flashMessenger; 

    public function __construct(LekarzPolaczenie $lekarzDb, WydarzeniePolaczenie $wydDb) 
    {
      $this->lekarzDb=$lekarzDb;
      $this->wydarzenieDb=$wydDb;
      $this->flashMessenger=new FlashMessenger();
    }
       
    public function indexAction()
    {
        
      $idLekarz=(int)$this->params('id',0);
       if($idLekarz<1)
       {
           $this->getResponse()->setStatusCode(404);
           return;
       }else{
          $lekarz=$this->lekarzDb->pobierzJedenLekarz($idLekarz);
       }
        
       $data=$this->params('data', date('Y-m-d'));

       $kalendarz=new \Kalendarz\Entity\Kalendarz($data);
       
       $wydarzenia=$this->wydarzenieDb->pobierzWydarzeniaMiesiaca($kalendarz, $lekarz->getIdlekarz());

       $wydarzeniaArray=array();
       foreach ($wydarzenia as $wydarzenie)
       {
         $wydarzenie_data=$wydarzenie->getWydarzenie_data();
         $dzienWydarzenia=(int) substr($wydarzenie_data, 8, 2);
         $wydarzeniaArray[$dzienWydarzenia][$wydarzenie->getIdwydarzenie()]=$wydarzenie;
       }
       
        $naglowekKalendarza = strftime('%B %Y', strtotime($kalendarz->dataKalendarza));
        
       $kodHtmlKalendarz=$kalendarz->generujKodHTMLKalendarza($lekarz,$this->baseUrl,$wydarzeniaArray);
       
      
        return ['kodHTML'=>$kodHtmlKalendarz,'lekarz'=>$lekarz,'naglowekKalendarza'=>$naglowekKalendarza,'data'=>$data];
    }
    
    
   public function pokazAction()
   {
       $idLekarz=(int)$this->params('id',0);
       $data=$this->params('data',null);
       
       if($idLekarz < 1 || !$data)
       {
          $this->getResponse()->setStatusCode(404);
           return; 
       }
        
       $lekarz=$this->lekarzDb->pobierzJedenLekarz($idLekarz);
       if(!$lekarz instanceof Lekarz)
       {
           $this->getResponse()->setStatusCode(404);
           return;
       }
       
       $wydarzeniaDnia=$this->wydarzenieDb->pobierzWydarzeniaDzienPoIdlekarz($data, $idLekarz);
       
       $tablicaWydarzen=array();
       foreach ($wydarzeniaDnia as $wydarzenie)
       {
           $godzina= date('H', strtotime($wydarzenie->getWydarzenie_start()));
           $tablicaWydarzen[$godzina][]=$wydarzenie;
       }
       
       $widok=new ViewModel(['lekarz'=>$lekarz, 'tablicaWydarzen'=>$tablicaWydarzen,'data'=>$data]);
       
       return $widok;
   }
   
   public function pokazWydarzenieAction()
   {
     $idwydarzenie=$this->params('id',0); 
     $idlekarz=$this->params('idlekarz',0);
     $idwydarzenie=(int)$this->params()->fromQuery('id', $idwydarzenie);
     $idlekarz=$this->params()->fromQuery('idlekarz', $idlekarz);
     if(!$idlekarz || !$idwydarzenie)
     {
        $this->getResponse()->setStatusCode(404);
           return;  
     }
     
     $lekarz=$this->lekarzDb->pobierzJedenLekarz($idlekarz);
     $wydarzenie=$this->wydarzenieDb->pobierzWydarzeniePoId($idwydarzenie);
     
     $widok=new ViewModel(['lekarz'=>$lekarz,'wydarzenie'=>$wydarzenie]);
     
     return $widok;
   }
   
   public function edytujAction() 
   {
       
      $idWydarzenie=$this->params('id',0);
       if(!$idWydarzenie)
       {
          $this->getResponse()->setStatusCode(404);
           return; 
       }
       $wydarzenie=$this->wydarzenieDb->pobierzWydarzeniePoId($idWydarzenie);
       if($wydarzenie->getIdLekarz())
       {
           $lekarz=$this->lekarzDb->pobierzJedenLekarz($wydarzenie->getIdLekarz());
       }else{
           $lekarz=null;
       }
       
       $form=new \Kalendarz\Form\WydarzenieForm(
              'wydarzenie_form',
              [
               'wpisz_czy_edytuj'  => 'edytuj',
               'baseUrl'=>$this->baseUrl, 
              ]
              );
       
       $wydarzenie->zmienWydarzenie_start();
       $wydarzenie->zmienWydarzenie_koniec();
       
       $form->bind($wydarzenie);
       
       $request=$this->getRequest();
       

        $view=new ViewModel(['form'=>$form,'lekarz'=>$lekarz, 'wpisz_czy_edytuj'=>'edytuj']);
        
       $request = $this->getRequest();
       
        if (! $request->isPost()) {
            return $view;
        }
       
      $form->setData($request->getPost());
       
       if (! $form->isValid())
       {
        return $view;
    }
       
    $wydarzenie=$form->getData();
    
       $flashMessenger=$this->flashMessenger;  
     try {
         $wydarzenie=$this->wydarzenieDb->edytujWydarzenie($wydarzenie); 
         
         $flashMessenger->addSuccessMessage('Wydarzenie zostało zaktualizowane !!');
    } catch (\Exception $ex) {
        $flashMessenger->addErrorMessage('Wydarzenie nie zostało zaktualizowane !!. Powiadom administratora');
      //  var_dump($ex);exit();
        }

    if($wydarzenie->getIdLekarz()){
    return $this->redirect()->toRoute(
        'kalendarz',
        ['id' => $wydarzenie->getIdLekarz()] 
            ); 
    }else{
        return $this->redirect()->toRoute
                ('lekarz'); 
    }
   }
   
   public function wpiszAction()
   {
       
       $request=$this->getRequest();
       
      $idLekarz=$this->params('id',0);
       if(!$idLekarz)
       {
          $this->getResponse()->setStatusCode(404);
           return; 
       }
       $lekarz=$this->lekarzDb->pobierzJedenLekarz($idLekarz);
       
       
       
       $form=new \Kalendarz\Form\WydarzenieForm(
              'wydarzenie_form',
              [
               'wpisz_czy_edytuj'  => 'wpisz',
               'baseUrl'=>$this->baseUrl,
                'lekarz'=>$lekarz,
              ]
              );
       
       $view=new ViewModel(['form'=>$form,'lekarz'=>$lekarz,'wpisz_czy_edytuj'=>'wpisz']);
       
       if(!$request->isPost())
       {
           return $view;
       }
       
       $form->setData($request->getPost());
       
       if (! $form->isValid())
       {
        return $view;
    }
       
    $wydarzenie=$form->getData();
    
    $checkbox=$this->request->getPost('checkbox');
    $flashMessenger=$this->flashMessenger;
    
     try {
         $wydarzenie=$this->wydarzenieDb->wpiszNoweWydarzenie($wydarzenie, $checkbox); 
         $flashMessenger->addSuccessMessage('Wydarzenie zostało wpisane !!');

         
    } catch (\Exception $ex) {
       $flashMessenger->addErrorMessage('Błąd: Wydarzenie nie zostało wpisane !!.'); 
    }
     return $this->redirect()->toRoute(
        'kalendarz',
        ['id' => $idLekarz] 
            ); 
     
       
   }
   
   public function autocompleteAction()
   {
       
   }
   public function searchlekarza4xmlAction()
   {

     $term=$this->params()->fromQuery('wpis');
       
     $lekarze=$this->lekarzDb->searchlekarzejson($term);

     $xml=new \DOMDocument('1.0', 'UTF-8');
     
     $lekarzeElement=$xml->appendChild(new \DOMElement('lekarze'));
     
     foreach ($lekarze as $lekarz2)
     {
         $lekarzElement=$lekarzeElement->appendChild(new \DOMElement('lekarz'));
         $name=new \DOMElement('name',$lekarz2->getNazwisko().' '.$lekarz2->getImie());
         $mail=new \DOMElement('mail',$lekarz2->getMail());
         $id=new \DOMElement('id',$lekarz2->getIdlekarz());
         $lekarzElement->appendChild($name);
         $lekarzElement->appendChild($mail);
         $lekarzElement->appendChild($id);
     }
     
     $xmlString=$xml->saveXML();
     
      $response=new \Laminas\Http\Response();
      $response->getHeaders()->addHeaderLine('Content-Type', 'text/xml; charset=utf-8');
      
      $response->setContent($xmlString);
      
      
      return $response;
      
     
   }
   
   public function wpiszjqueryAction() 
   { 
      $this->layout()->setTemplate('layout/layout_posty'); 
      
      $request=$this->getRequest();
       
    $idLekarz=$this->params('id',0);  
       if(!$idLekarz)
       {
          $this->getResponse()->setStatusCode(404);
           return; 
       }
       $lekarz=$this->lekarzDb->pobierzJedenLekarz($idLekarz);
       
       
       
       $form=new \Kalendarz\Form\WydarzenieForm(
              'wydarzenie_form',
              [
               'wpisz_czy_edytuj'  => 'wpisz',
               'baseUrl'=>$this->baseUrl,
               'lekarz'=>$lekarz,
              ]
              );
      
       $view=new ViewModel(['form'=>$form,'lekarz'=>$lekarz,'wpisz_czy_edytuj'=>'wpisz']);
       
       
       if(!$request->isPost())
       {
           return $view;
       }
       
       $form->setData($request->getPost());
       
       if (!$form->isValid())
       {
           
        return $view;
    }
  
    
   }
   
      public function wpiszjquery2Action() 
   { 
      $this->layout()->setTemplate('layout/layout_posty'); 
      
      $request=$this->getRequest();
       
    $idLekarz=$this->params('id',0);  
       if(!$idLekarz)
       {
          $this->getResponse()->setStatusCode(404);
           return; 
       }
       $lekarz=$this->lekarzDb->pobierzJedenLekarz($idLekarz);
       
       
       
       $form=new \Kalendarz\Form\WydarzenieForm(
              'wydarzenie_form',
              [
               'wpisz_czy_edytuj'  => 'wpisz',
               'baseUrl'=>$this->baseUrl,
               'lekarz'=>$lekarz,
              ]
              );
      
       $view=new ViewModel(['form'=>$form,'lekarz'=>$lekarz,'wpisz_czy_edytuj'=>'wpisz']);
       
       
       if(!$request->isPost())
       {
           return $view;
       }
       
       $form->setData($request->getPost());
       
       if (!$form->isValid())
       {
           
        return $view;
    }
  
    
   }
  
   public function wpiszjquerykontrolawynikowAction()
   {
      
       if(!$this->request->isXmlHttpRequest()){
           $this->getResponse()->setStatusCode(404);
        return;
       }
       $this->layout()->setTemplate('layout/layout_posty_1'); 
      $request=$this->getRequest();
       
    $idLekarz=$this->params('id',0);  
       if(!$idLekarz)
       {
           return['wynik'=>'Błąd przy pobieraniu danych Lekarza !!']; 
       }
       
       $lekarz=$this->lekarzDb->pobierzJedenLekarz($idLekarz);

       $form=new \Kalendarz\Form\WydarzenieForm(
              'wydarzenie_form',
              [
               'wpisz_czy_edytuj'  => 'wpisz',
               'baseUrl'=>$this->baseUrl,
               'lekarz'=>$lekarz,
              ]
              );
      
      
       if(!$request->isPost())
       {
          return['wynik'=>'Błąd przy pobieraniu danych Post !!']; 
       }
       
       $form->setData($request->getPost());
       
       if (!$form->isValid())
       {
         $bledy=$form->getMessages();  
       return['wynik'=>'Błąd przy wprowadzonych danych !!','bledy'=>$bledy]; 
    }
  
    $wydarzenie=$form->getData();

   $checkbox=$this->request->getPost('checkbox');

   $idwydarzenie=0;
     try {
         $wydarzenie=$this->wydarzenieDb->wpiszNoweWydarzenie($wydarzenie, $checkbox); 
         $idwydarzenie=$wydarzenie->getIdwydarzenie();
         
        return['wynik'=>'Wydarzenie zostało wpisane !!','idwydarzenie'=>$idwydarzenie];
         
    } catch (\Exception $ex) {

       return['idwydarzenie'=>$idwydarzenie];
    } 
   
   
   }
   
    public function wpiszjquerykontrolawynikow2Action()
   {
        
        if(!$this->request->isXmlHttpRequest()){
           $this->getResponse()->setStatusCode(404);
        return;
       }
       
      $this->layout()->setTemplate('layout/layout_posty_1'); 
      
      $request=$this->getRequest();
       
    $idLekarz=$this->params('id',0);  
       if(!$idLekarz)
       {
           return['wynik'=>'Błąd przy pobieraniu danych Lekarza !!']; 
       }
       
       $lekarz=$this->lekarzDb->pobierzJedenLekarz($idLekarz);

       $form=new \Kalendarz\Form\WydarzenieForm(
              'wydarzenie_form',
              [
               'wpisz_czy_edytuj'  => 'wpisz',
               'baseUrl'=>$this->baseUrl,
               'lekarz'=>$lekarz,
              ]
              );
      
     
       
       
       if(!$request->isPost())
       {
          return['wynik'=>'Błąd przy pobieraniu danych Post !!']; 
       }
       
       $form->setData($request->getPost());
       
       if (!$form->isValid())
       {
         $bledy=$form->getMessages();  
       return['wynik'=>'Błąd przy wprowadzonych danych !!','bledy'=>$bledy]; 
    }
  
    $wydarzenie=$form->getData();

   $checkbox=$this->request->getPost('checkbox');

  $idwydarzenie=0;
     try {
         $wydarzenie=$this->wydarzenieDb->wpiszNoweWydarzenie($wydarzenie, $checkbox); 
         $idwydarzenie=$wydarzenie->getIdwydarzenie();
         
        return['wynik'=>'Wydarzenie zostało wpisane !!','idwydarzenie'=>$idwydarzenie];
         
    } catch (\Exception $ex) {

       return['idwydarzenie'=>$idwydarzenie];
    }   
   
   }
   
   public function usunjqueryAction() 
   {
        if(!$this->request->isXmlHttpRequest()){
           $this->getResponse()->setStatusCode(404);
        return;
       }
       
      $this->layout()->setTemplate('layout/layout_posty');  
      
      if($this->request->isGet() && $this->request->isXmlHttpRequest())
      {
      $idWydarzenie=$this->params()->fromQuery('id',0);
      
      $wynik=$this->wydarzenieDb->usunWydarzenie($idWydarzenie);
      
      if($wynik){
          return ['wynik'=>'Wydarzenie zostało usunięte'];
      }else{
         return ['wynik'=>'Błąd ']; 
      }
      
      }else{
          return ['wynik'=>'Błąd '];
      }
       
   }
   
   
}
