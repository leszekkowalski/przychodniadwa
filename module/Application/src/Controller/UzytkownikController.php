<?php

namespace Application\Controller;


use Application\Polaczenie\UzytkownikPolaczenie;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Model\Uzytkownik;
use Laminas\View\Model\ViewModel;
use Application\Form\ZmienHasloForm;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;



class UzytkownikController extends AbstractController
{
    
    protected $dbUzytkownik;
    protected $dblekarz;
    protected $formZmienHaslo;
    public $flashMessenger; 

    public function __construct
            (
            UzytkownikPolaczenie $dbUzytkownik, 
            LekarzPolaczenie $dblekarz, 
            ZmienHasloForm $formZmienhaslo
            ) 
            {
        $this->dbUzytkownik=$dbUzytkownik;
        $this->dblekarz=$dblekarz;
        $this->formZmienHaslo=$formZmienhaslo;
        $this->flashMessenger = new FlashMessenger();
             }   
    
public function indexAction() {
    
    $paginator=$this->dbUzytkownik->paginatorUzytkownik(true);

    $lekarzArrayId=$this->dblekarz->pobierzWszystkoLekarzId();
  
   $roleArrayId=$this->dbUzytkownik->pobierzRoleUzytkownikaJakoArray();     
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
            'roleArrayId'=>$roleArrayId,
            'roleObject'=>$roleObject,
            'page'=>$page,
                ]);
    
}

public function dodajAction() {
    
}

public function dodajlekarzAction() {
    
}

public function zmienHasloAction() {
    
    $request=$this->getRequest();
    $flashMessenger=$this->flashMessenger;
    
  $idUzytkownik =(int) $this->params()->fromRoute('id',0);
        if (! $idUzytkownik)
        {
            $flashMessenger->addErrorMessage('Błąd pobrania id Użytkownika. Powiadom Administratora !!'); 
            return $this->redirect()->toRoute('uzytkownik');
        }
        
      try {
            $uzytkownik = $this->dbUzytkownik->znajdzJedenPoIdUzytkownik($idUzytkownik);
        } catch (InvalidArgumentException $ex) {
          
            $flashMessenger->addErrorMessage('Błąd pobrania Użytkownika. Powiadom Administratora !!');  
            return $this->redirect()->toRoute('uzytkownik');
        }   
    
    $imie_nazwisko=$uzytkownik->getImie().' '.$uzytkownik->getNazwisko();
    $viewModel = new ViewModel(['form' => $this->formZmienHaslo,'imie_nazwisko'=>$imie_nazwisko]);

        if (! $request->isPost()) {
            return $viewModel;
        }
        
        $this->formZmienHaslo->setData($request->getPost());
        
         if (! $this->formZmienHaslo->isValid()) {
            return $viewModel;
        }
        
        $szyfrowanie=new \Laminas\Crypt\Password\Bcrypt();
        $haslo=$szyfrowanie->create($request->getPost()['haslo']);
        $wynikUpdate=$this->dbUzytkownik->zmienHasloUzytkownik($haslo, $idUzytkownik);
        
        if($wynikUpdate)
        {
         $informacja='Hasło dla Użytkownika: '.$imie_nazwisko.' zostało zmienione.';
         $flashMessenger->addSuccessMessage($informacja); 
         return $this->redirect()->toRoute('uzytkownik');
        }else{
         $informacja='Błąd: Hasło dla Użytkownika: '.$imie_nazwisko.' NIE zostało zmienione.';
         $flashMessenger->addErrorMessage($informacja); 
         return $this->redirect()->toRoute('uzytkownik');   
        }
}

public function edytujAction() {
    
}

public function usunAction() {
    
}
public function pokazAction() {
    
}
 
 
}

