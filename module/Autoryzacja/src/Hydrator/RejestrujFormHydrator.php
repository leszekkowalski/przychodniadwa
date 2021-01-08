<?php

namespace Autoryzacja\Hydrator;

use Laminas\Crypt\Password\Bcrypt;

class RejestrujFormHydrator implements \Laminas\Hydrator\Strategy\StrategyInterface
{
    
    public function hydrate($value, ?array $dane)
    {
        
      $szyfrowanie=new Bcrypt();
      if($dane['powtorz_haslo']==$dane['mail_haslo_csrf_fieldset']['haslo']){
      
       $haslo=$szyfrowanie->create($dane['powtorz_haslo']);  
       
        }else{
            throw new \Exception('Niepoprawny dane do hasła, przesłąne do '.__CLASS__.' Powiadom administratora');   
            }
        $czas_wpisu= date('Y-m-d H:i:s');
        $status=1;
    
    return [
        'imie'=>$dane['imie_nazwisko_fieldset']['imie'],
        'nazwisko'=>$dane['imie_nazwisko_fieldset']['nazwisko'],
        'mail'=>$dane['mail_haslo_csrf_fieldset']['mail'],
        'status'=>$status,
        'data_powstania'=>$czas_wpisu,
        'haslo'=>$haslo,
            ];
    
    }
    
    public function extract($value, object $object = NULL) {
        
        return $object;
    }

}
