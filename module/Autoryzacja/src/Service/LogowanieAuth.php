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
use Moj_rbac\Service\RbacManager;


class LogowanieAuth
{
    // zawartosc wymagana dla akcji filtrdostepu.
    const DOSTEP_PRZYZNANY = 1; 
    const DOSTEP_WYMAGANA_AUTORYZACJA  = 2;
    const DOSTEP_ZABRONIONY  = 3; 
    
    
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
    
    //Moj_rbac\Service\RbacManager;
    protected $rbac;


    public function __construct(Adapter $dbAdapter, UzytkownikPolaczenie $dbUzytkownik, $filtr_dostepu, RbacManager $rbac)
    {
     $this->dbAdapter=$dbAdapter;
     $this->dbUzytkownik=$dbUzytkownik;
     $this->config=$filtr_dostepu;
     $this->rbac= $rbac;
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
*/
    
  public function kontrolaDostepu($controllerName, $actionName)
    {
        $userSession = new Session\Container('uzytkownik');
        
       $tryb= isset($this->config['options']['tryb']) ? $this->config['options']['tryb']:'zastrzezony';
        
       if($tryb!='zastrzezony' && $tryb!='pozwalajacy')
       {
           return new \Exception('Niepoprawny tryb dostepu !!!!!');
       }
       
       if(isset($this->config['controllers'][$controllerName]))
       {
           $nazwyControlerow=$this->config['controllers'][$controllerName];
           
           foreach ($nazwyControlerow as $controler)
           {
               $nazwyMetod=$controler['actions'];
               $pozwolenia=$controler['allow'];
               
               if(is_array($nazwyMetod) && in_array($actionName, $nazwyMetod) || $nazwyMetod=='@')
               {
                   if($pozwolenia=='*')
                   {
                       return self::DOSTEP_PRZYZNANY;
                   }else{
                       if(!$userSession->details)
                       {
                           return self::DOSTEP_WYMAGANA_AUTORYZACJA;
                       }
                       
                       if($pozwolenia=='@')//nie wystepuje u mnie - ale na przyszlosc np. dla zalogowanego ale bez uprawnienia
                       {
                          return self::DOSTEP_PRZYZNANY; 
                       }else{
                           if(substr($pozwolenia,0, 1)=='@')
                           //tylko uzytkownik ze specyficzną identyfikacja na wartość adresu mail w kluczu ma dostep np: @stepien@wp.pl zamiast +uprawnienie - na razie nie uzywam
                           {
                            $mailUzytkownika= substr($pozwolenia, 1);
                            
                            if($userSession->details->getMail()==$mailUzytkownika)
                            {
                               return  self::DOSTEP_PRZYZNANY;
                            }else{
                               return  self::DOSTEP_ZABRONIONY;
                            }
                            
                           }else{
                              if(substr($pozwolenia, 0, 1)=='+')
                              {
                              // Only the user with this permission is allowed to see the page.
                              $uprawnienie= substr($pozwolenia, 1) ;
                              
                             if($this->rbac->isGranted(null, $uprawnienie))
                             {
                                 return self::DOSTEP_PRZYZNANY;
                             }else{
                                 return self::DOSTEP_ZABRONIONY;
                             }
                              }else{
                                  throw new \Exception('Unexpected value for "allow" - expected ' .
                            'either "?", "@", "@mail" or "+permission"');
                              }
                           }
                           
                       }
   
                   }
               }
               
               
           }
       }
       // In zastrzezony mode, we require authentication for any action not 
    // listed under 'filtr_dostepu' key and deny access to authorized users 
    // (for security reasons).
    if ($tryb=='zastrzezony') {
        if(!$userSession->details)
            return self::DOSTEP_WYMAGANA_AUTORYZACJA;
        else
            return self::DOSTEP_ZABRONIONY;
    }
    
    // Permit access to this page.- kiedy nie ma kontrolera na liscie np. Logowanie
    return self::DOSTEP_PRZYZNANY;
       
       
    }
       
}