<?php

namespace Application\Service;

use Exception;
use PHPThumb\GD;//composer require masterexploder/phpthumb
use phpthumb_functions as nowephpthump_function;//composer require james-heinrich/phpthumb

class ZdjecieManager {
    /*
     * link do pliku ze zdjeciem;
     */
    private $linkZdjecie=null;
    
  
    public function setLinkZdjecie($link) {
        $this->linkZdjecie=$link;
    }
    
    public function getLinkZdjecie(){
        if($this->linkZdjecie){
        return $this->linkZdjecie;
        }else{
            return new \Exception('Błąd. Brak pliku');
        }
            
    }
    
    public function zmniejszZdjecie($szerokosc){
       
       $zmniejszanie=new \PHPThumb\GD($this->getLinkZdjecie()) ;
       $zmniejszanie->resize($szerokosc);
       $wynikZapisu=$zmniejszanie->save($this->getLinkZdjecie());
       if($wynikZapisu instanceof \PHPThumb\GD){
           return 1;
       }else{
           return 0;
       }
   
    }
    
    public function zmienNazwe($staraNazwa,$id): string {
        $staraNazwa_podzial=explode(".", $staraNazwa);
      $nowaNazwa='./public/upload/'.$id.'.'.$staraNazwa_podzial[1];
      $s=(string)'./public/upload/nowa_nazwa.'.$staraNazwa_podzial[1];
      
      try{
          rename($s, $nowaNazwa);
      } catch (Exception $ex) {
          if (file_exists($s)) {
    $success = unlink($s);
    
    if (!$success) {
         return ("Błąd zmiany nazwy pliku");
    }
}
          return 'Błąd zmiany nazwy pliku';
      }
     
      return (string)$nowaNazwa;
    }
    
    
    public function usunPlik(){
          if (file_exists($this->getLinkZdjecie())) {
              $e=$this->getLinkZdjecie();
    $success = unlink($s);
    
    if (!$success) {
         return 0;
    }else{
        return 1;
    }
}
    }
    
    public function pobierzPlikiFolder($sciezkaFolder) {
        
        $obiekt=new nowephpthump_function();
        $plikiFolder=$obiekt->GetAllFilesInSubfolders($sciezkaFolder);
        
    }
    
    
}
