<?php

declare (strict_types=1);

namespace Application\Controller;

use Application\Controller\AbstractController;
use Application\Form\LekarzDodajForm;
use Laminas\View\Model\ViewModel;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Model\Lekarz;


class LekarzController extends AbstractController{
    
    protected $lekarzDb;

    protected $lekarzDodajForm;
    
 public function __construct(LekarzPolaczenie $lekarzDb, LekarzDodajForm $lekarzDodajForm ) {
     
     $this->lekarzDb=$lekarzDb;
     $this->lekarzDodajForm=$lekarzDodajForm;
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

    $viewModel = new ViewModel(['form' => $this->lekarzDodajForm]);


    if (! $request->isPost() ){
       
        return $viewModel;
    }
    
    // $lekarz=new Lekarz('','');
    // $this->lekarzDodajForm->setInputFilter($lekarz->getInputFilter());
    
    
    $this->lekarzDodajForm->setData($request->getPost());

    if (! $this->lekarzDodajForm->isValid()) {
        return $viewModel;
    }
    $lekarz = $this->lekarzDodajForm->getData();
    
    
    exit();
    
 }
 
}