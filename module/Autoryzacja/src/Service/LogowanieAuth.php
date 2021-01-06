<?php

namespace Autoryzacja\Service;

use Laminas\Authentication\Adapter\DbTable\CallbackCheckAdapter as LogujAdapter;
use Laminas\Db\Adapter\Adapter;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Application\Polaczenie\UzytkownikPolaczenie;
use Application\Model\Uzytkownik;
use Laminas\Session;


class LogowanieAuth
{
    /**
     *
     * @var type Laminas\Db\Adapter\Adapter;
     */
    private $dbAdapter;
    
  //  private $adapter;
    
    private $dbUzytkownik;
    
    protected $kontrolahaslo;
    
    /**
     * Zawartość klucza 'kontrola_dostepu' z klucza Configuracyjnego 
     * @var array 
     */
    private $config;


    public function __construct(Adapter $dbAdapter, UzytkownikPolaczenie $dbUzytkownik, $filtr_dostepu)
    {
     $this->dbAdapter=$dbAdapter;
     $this->dbUzytkownik=$dbUzytkownik;
     $this->config=$filtr_dostepu;
   //  $this->adapter=new LogujAdapter
   //          (
    //         $this->dbAdapter, 
    //         'uzytkownik', 
    //         'mail', 
    //         'haslo', 
    //         $this->kontrolahaslo
     //        );
     
     
    }

    public function authenticate($mail, $haslo): Result
    {
      
        if(empty($mail)|| empty($haslo))
        {
             return new Result(
                 Result::FAILURE, 
                 null, 
                 ['Brak hasla lub maila']
                 );
        }
        //pobieram hasło i status z bazy dla wskazanego maila (jesli istnieje)
        $uzytkownik=$this->dbUzytkownik->znajdzJedenPoMailUzytkownik($mail);
        
        if(!$uzytkownik instanceof Uzytkownik)
        {
            return new Result(
                 Result::FAILURE_IDENTITY_NOT_FOUND, 
                 null, 
                 ['Dane logowania/mail jest niepoprawny']
                 );
         }
             
       //  if($uzytkownik->getStatusId()!=1){
       //      return new Result(
       //          Result::SUCCESS, 
        //         null, 
         //        ['Uzytkownik istnieje, lecz jest nieaktywny']
          //       );
       //  }
         
         $hasloUzytkownik=$uzytkownik->getHaslo();
        

           $szyfrowanie=new Bcrypt(); 

           $wynik=$szyfrowanie->verify($haslo,$hasloUzytkownik);
           
         if(!$wynik){
            return new Result(
                 Result::FAILURE_IDENTITY_NOT_FOUND, 
                 null, 
                 ['Dane logowanie są niepoprawne']
                 ); 
         }else{
             
             if($uzytkownik->getStatusId()==0 || $uzytkownik->getStatusId()==2){
             return new Result(
                 Result::SUCCESS, 
                 null, 
                 ['Uzytkownik istnieje, lecz jest nieaktywny']
                 );
         } 
             
             $uzytkownik->setHaslo(null);
            return new Result(
                 Result::SUCCESS, 
                 $uzytkownik, 
                 ['OK']
                 );  
         }
         
       //  $this->adapter->setCredentialValidationCallback($this->kontrolahaslo);
         
        // $this->adapter->setIdentity($mail);
        // $this->adapter->setCredential($haslo);

    }
    /**
* funkcja do filtrowania kontroli dostepu. Pozwala uzytkownikom dostep do pewnych akcji
     * a dla pozstałych wymagana jest logowanie (zaloazenie sesji);
 * Metoda uzywa klucza 'kontrola_dostepu' w kluczu Config. Sprawdza czy biezacy uzytkownik ma 
     * pozwolenie do obejrzenia akcji w danym kontrolerze.
     * Zwraca 'true' jesli jest zgoda, lub 'false' - brak zgody.
 */
    public function kontrolaDostepu($controllerName, $actionName)
    {
        $userSession = new Session\Container('uzytkownik');
        
        $tryb=$this->config['options']['tryb']?$this->config['options']['tryb']:'zastrzezony';
        
        if($tryb!='zastrzezony' && $tryb!='pozwalajacy'){
            throw new Exception('Niewłasciwy tryb filtru dostepu.');
        }
       
       
        if (isset($this->config['controllers'][$controllerName])) {
        $kontrolery = $this->config['controllers'][$controllerName];
        
        foreach ($kontrolery as $kontroler) {
            $actionList = $kontroler['actions'];
            $allow = $kontroler['allow'];
            if (is_array($actionList) && in_array($actionName, $actionList) ||
                $actionList=='*') {
                if ($allow=='*')
                    return true; // kazdy ma dostep
                else if ($allow=='@' && $userSession->details) {
                    return true; // tylko zalogowani w sesji
                } else {                    
                    return false; // dostep zabroniony.
                }
            }
        }            
    }
        
      if($tryb=='zastrzezony' && !$userSession->details)  
      {
          return false;
      }
        
      return true;
    
      
      
      
      
        

}

}