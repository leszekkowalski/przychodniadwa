<?php

namespace Application\Controller;


use Application\Polaczenie\UzytkownikPolaczenie;
use Application\Model\Uzytkownik;
use Laminas\View\Model\ViewModel;

class UzytkownikController extends AbstractController{
    
    protected $dbUzytkownik;


    public function __construct(UzytkownikPolaczenie $dbUzytkownik) {
     
        $this->dbUzytkownik=$dbUzytkownik;
        
 }   
    
public function indexAction() {
    
    $paginator=$this->dbUzytkownik->paginatorUzytkownik(false);
    $array=$paginator->toArray();
    $ileNaStrone=8;    
     /**    
    $page = (int) $this->params()->fromQuery('page', 1);
    $page = ($page < 1) ? 1 : $page;
    
    $paginator->setCurrentPageNumber($page);
    // Set the number of items per page to 2:
    $paginator->setItemCountPerPage($ileNaStrone);   
    */    
        return new ViewModel([
            'baseUrl'=>$this->baseUrl,
            'paginator'=>$array,
            'ileNaStrone'=>$ileNaStrone,
            'page'=>$page,
                ]);
    
}
 
 
}

