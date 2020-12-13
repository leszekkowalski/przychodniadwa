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
    
    $uzytkownicy=$this->dbUzytkownik->paginatorUzytkownik();
}
 
 
}

