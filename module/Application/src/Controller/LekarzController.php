<?php

declare (strict_types=1);

namespace Application\Controller;

use Application\Controller\AbstractController;
use Application\Form\LekarzDodajForm;
use Laminas\View\Model\ViewModel;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Model\Lekarz;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;


class LekarzController extends AbstractController{
    
    protected $lekarzDb;

    protected $lekarzDodajForm;
    
    public $flashMessenger; 
    
 public function __construct(LekarzPolaczenie $lekarzDb, LekarzDodajForm $lekarzDodajForm ) {
     
     $this->lekarzDb=$lekarzDb;
     $this->lekarzDodajForm=$lekarzDodajForm;
     $this->flashMessenger = new FlashMessenger();
 }   
  
 public function indexAction() {
     

    $paginator=$this->lekarzDb->paginatorLekarz(true);
    
$ileNaStrone=2;    
        
     // Set the current page to what has been passed in query string,
    // or to 1 if none is set, or the page is invalid:
    $page = (int) $this->params()->fromQuery('page', 1);
    $page = ($page < 1) ? 1 : $page;
    
    $paginator->setCurrentPageNumber($page);
    // Set the number of items per page to 2:
    $paginator->setItemCountPerPage($ileNaStrone);   
        
        return new ViewModel([
            'baseUrl'=>$this->baseUrl,
            'paginator'=>$paginator,
            'ileNaStrone'=>$ileNaStrone,
            'page'=>$page,
                ]);
 }
 
 public function dodajAction() {
     
    $request   = $this->getRequest();
    $imie=$this->lekarzDodajForm->get('lekarz_fieldset')->get('imie');
    $viewModel = new ViewModel([
        'form' => $this->lekarzDodajForm,
        'flashMessenger'=> $this->flashMessenger,
            ]);


    if (! $request->isPost() ){
       
        return $viewModel;
    }
    
    $this->lekarzDodajForm->setData($request->getPost());

    if (! $this->lekarzDodajForm->isValid()) {   
        return $viewModel;
    }
    
    $lekarz = $this->lekarzDodajForm->getData();

    try{
        $lekarz=$this->lekarzDb->wpiszLekarz($lekarz);
    } catch (Exception $ex) {
        throw $ex;
    }
    
    $flashMessenger=$this->flashMessenger;
    $flashMessenger->addSuccessMessage('Lekarz  '.$lekarz->getImie().' '.$lekarz->getNazwisko().' została wpisana !!');
    
    $this->redirect()->toRoute('lekarz');
    
 }
 
 public function edytujAction() {
     
     $id=(int)$this->params()->fromRoute('id',0);
     if(!$id){
         $this->redirect()->toRoute('lekarz');
     }
     try{
         $lekarz=$this->lekarzDb->pobierzJedenLekarz($id);
     } catch (\Exception $ex) {

         throw $ex;
     }
     
     
 }
 
 
 
}