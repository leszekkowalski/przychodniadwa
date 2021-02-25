<?php

namespace Moj_rbac\Service;

use Laminas\Permissions\Rbac\Rbac;
use Application\Polaczenie\UzytkownikPolaczenie;
use Laminas\Session;
/**
 * Sprawdza uprawnienia indywidualne przy uzyciu metody isGranted() RbacManager
 */

class KontrolaUprawnienIndywidualnychRbac 
{
    
    protected $polaczenieUzytkownik;


    public function __construct(UzytkownikPolaczenie $polaczenie) 
    {
        $this->polaczenieUzytkownik=$polaczenie;
    }
    
    public function assert(Rbac $rbac, string $uprawnienia, array $parametry, $nazwaRoli=null): bool
    {
    
        $sesjaUzytkownika=new Session\Container('uzytkownik');
     
        if($uprawnienia=='wszyscy.own.pokazsesje' && $parametry['layout']=='pokaz')
        {
            return true;
        }
  
         if($uprawnienia=='rbac.zarzadzaj' && $parametry['layout']=='pokaz')
        {
            return true;
        }
        
         if($uprawnienia=='uzytkowniklekarz.zarzadzaj' && $parametry['layout']=='pokaz')
        {
            return true;
        }
        
        if(($uprawnienia=='lekarz.own.zmianahasla' && 
            $sesjaUzytkownika->details->getLekarz()==$parametry['lekarz']->getIdlekarz()) ||
            $nazwaRoli=='Super Administrator')
        {
            return true;
        }
        
        if(($uprawnienia=='lekarz.own.zmianazdjecia' && 
            $sesjaUzytkownika->details->getLekarz()==$parametry['lekarz']->getIdlekarz()) ||
            $nazwaRoli=='Super Administrator')
        {
            return true;
        }
 
         if(($uprawnienia=='uzytkownik.own.zmianahaslo' && 
            $sesjaUzytkownika->details->getIduzytkownik()==$parametry['uzytkownik']->getIduzytkownik()) ||
            $nazwaRoli=='Super Administrator')
        {
            return true;
        }
        
        return false;
    }

}

