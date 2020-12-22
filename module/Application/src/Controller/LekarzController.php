<?php

declare (strict_types=1);

namespace Application\Controller;

use Application\Controller\AbstractController;
use Application\Form\LekarzDodajForm;
use Laminas\View\Model\ViewModel;
use Application\Polaczenie\LekarzPolaczenie;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;
use InvalidArgumentException;
use Application\Service\ZdjecieManager;

class LekarzController extends AbstractController{
    
    protected $lekarzDb;

    protected $lekarzDodajForm;
    
    protected $managerZdjecie;


    public $flashMessenger; 
    
 public function __construct(LekarzPolaczenie $lekarzDb, LekarzDodajForm $lekarzDodajForm, ZdjecieManager $managerZdjecie ) {
     
     $this->lekarzDb=$lekarzDb;
     $this->lekarzDodajForm=$lekarzDodajForm;
     $this->managerZdjecie=$managerZdjecie;
     $this->flashMessenger = new FlashMessenger();
 }   
  
 public function indexAction() {
     

    $paginator=$this->lekarzDb->paginatorLekarz(true);
    
$ileNaStrone=4;    
        
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
     } catch (InvalidArgumentException $ex) {
         
        $flashMessenger=$this->flashMessenger;
        $flashMessenger->addErrorMessage('Błąd podczas poberania danych. Powiadom administratora !');
        $this->redirect()->toRoute('lekarz');
     }
     
     $this->lekarzDodajForm->bind($lekarz);
     
     $view=new ViewModel(['form'=>$this->lekarzDodajForm,'baseUrl'=>$this->baseUrl,'lekarz'=>$lekarz]);
     
     $request=$this->getRequest();
     if(!$request->isPost()){
         return $view;
     }
     
     $data= array_merge_recursive(
                 $request->getPost()->toArray(),
                 $request->getFiles()->toArray()
                  );
     
     $this->lekarzDodajForm->setData($data);
     
     if(! $this->lekarzDodajForm->isValid()){
         return $view;
     }
     
     $nazwaPlikuStara=$data['file']['name'];
     $nazwaPlikuStaraBaza=$lekarz->getZdjecie();
     if(!$nazwaPlikuStara){
         $lekarzPoWpisie=$this->lekarzDb->updateLekarz($lekarz);
         
         if($lekarzPoWpisie){ 
          $flashMessenger=$this->flashMessenger;
        $flashMessenger->addSuccessMessage('Zaktualizowano lekarza: '.$lekarz->getImie().' '.$lekarz->getNazwisko());
        $this->redirect()->toRoute('lekarz');   
         }else{
        $flashMessenger=$this->flashMessenger;
        $flashMessenger->addErrorMessage('Błąd podczas aktualizowania lekarza.');
        $this->redirect()->toRoute('lekarz'); 
             
         }
         
   
     }else{
         

        $nowaNazwa=$this->managerZdjecie->zmienNazwe($nazwaPlikuStara,$lekarz->getIdlekarz());
        
        if($nowaNazwa=='Błąd zmiany nazwy pliku'){
           $flashMessenger=$this->flashMessenger;
        $flashMessenger->addErrorMessage('Błąd podczas pobierania danych. Bład zmiany nazwy pliku. Powiadom administratora !');
        $this->redirect()->toRoute('lekarz'); 
        }
        
        $this->managerZdjecie->setLinkZdjecie($nowaNazwa);
        $szerokosc=(int)250;
        $wynik=$this->managerZdjecie->zmniejszZdjecie($szerokosc);
        if($wynik==0){
            $wynik=$this->managerZdjecie->usunPlik();
            if($wynik){
               $flashMessenger=$this->flashMessenger;
        $flashMessenger->addErrorMessage('Błąd podczas pobierania danych. Bład zmiany rozmiaru pliku. Powiadom administratora !');
        $this->redirect()->toRoute('lekarz');  
            }
        }
        
        
        $lekarzPoWpisie=$this->lekarzDb->updateLekarz($lekarz,$nowaNazwa);
         
         if($lekarzPoWpisie){
             
          if($nowaNazwa!=$nazwaPlikuStaraBaza){
              
              if(isset($nazwaPlikuStaraBaza)){
           if (file_exists($nazwaPlikuStaraBaza)) {
                unlink($nazwaPlikuStaraBaza);
           }
         }   
          }
          $flashMessenger=$this->flashMessenger;
        $flashMessenger->addSuccessMessage('Zaktualizowano lekarza: '.$lekarz->getImie().' '.$lekarz->getNazwisko());
        $this->redirect()->toRoute('lekarz'); 
        
         }else{
             
        $this->managerZdjecie->usunPlik();     
        $flashMessenger=$this->flashMessenger;
        $flashMessenger->addErrorMessage('Błąd podczas aktualizowania lekarza.');
        $this->redirect()->toRoute('lekarz'); 
             
         }   
     }   
 }
 /////////////////////////////////////////////////////////////////////////////
 public function pokazAction() {
     
     $id=(int)$this->params()->fromRoute('id',0);
     if(!$id){
         $this->redirect()->toRoute('lekarz');
     }
     try{
         $lekarz=$this->lekarzDb->pobierzJedenLekarz($id);
     } catch (InvalidArgumentException $ex) {
         
        $flashMessenger=$this->flashMessenger;
        $flashMessenger->addErrorMessage('Błąd podczas poberania danych. Powiadom administratora !');
        $this->redirect()->toRoute('lekarz');
     } 
     
      return new ViewModel([
            'baseUrl'=>$this->baseUrl,
            'lekarz'=>$lekarz,
                ]);
     
 }
 
}