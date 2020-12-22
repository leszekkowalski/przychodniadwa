<?php

namespace Application\Controller;


use Application\Polaczenie\UzytkownikPolaczenie;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Model\Uzytkownik;
use Laminas\View\Model\ViewModel;



class UzytkownikController extends AbstractController{
    
    protected $dbUzytkownik;
    protected $dblekarz;

    public function __construct(UzytkownikPolaczenie $dbUzytkownik, LekarzPolaczenie $dblekarz) {
     
        $this->dbUzytkownik=$dbUzytkownik;
        $this->dblekarz=$dblekarz;
 }   
    
public function indexAction() {
    
    $paginator=$this->dbUzytkownik->paginatorUzytkownik(true);

    $lekarzArrayId=$this->dblekarz->pobierzWszystkoLekarzId();
    
    
    $ileNaStrone=4;    
    

         
    $page = (int) $this->params()->fromQuery('page', 1);
    $page = ($page < 1) ? 1 : $page;
    
    $paginator->setCurrentPageNumber($page);
    // Set the number of items per page to 2:
    $paginator->setItemCountPerPage($ileNaStrone);   
        
        return new ViewModel([
            'baseUrl'=>$this->baseUrl,
            'paginator'=>$paginator,
            'ileNaStrone'=>$ileNaStrone,
            'lekarzArrayId'=>$lekarzArrayId,
            'page'=>$page,
                ]);
    
}

public function dodajAction() {
    
}
 
 
}

