<?php

namespace Kalendarz\Controller;

use Application\Controller\AbstractController;
use Application\Polaczenie\LekarzPolaczenie;
use Kalendarz\Polaczenie\WydarzeniePolaczenie;
use Kalendarz\Entity\Wydarzenie;
use Application\Model\Lekarz;
use Laminas\View\Model\ViewModel;

class KalendarzController extends AbstractController
{
    protected $lekarzDb;
    
    protected $wydarzenieDb;

    public function __construct(LekarzPolaczenie $lekarzDb, WydarzeniePolaczenie $wydDb) 
    {
      $this->lekarzDb=$lekarzDb;
      $this->wydarzenieDb=$wydDb;
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
       
       
       
       $form=new \Kalendarz\Form\WydarzenieForm(
              'wydarzenie_form',
              [
               'wpisz_czy_edytuj'  => 'edytuj',
               'baseUrl'=>$this->baseUrl,
              ]
              );
       
   }
   
   public function wpiszAction()
   {
       
   }
   
   public function autocompleteAction()
   {
       
   }
}
