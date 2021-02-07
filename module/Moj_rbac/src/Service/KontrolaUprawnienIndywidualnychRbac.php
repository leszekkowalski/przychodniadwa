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
    
    public function assert(Rbac $rbac, string $uprawnienia, array $parametry): bool
    {
     
        
        return true;
    }

}

