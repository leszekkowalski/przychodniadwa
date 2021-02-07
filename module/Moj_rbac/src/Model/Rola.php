<?php

namespace Moj_rbac\Model;

use Laminas\Stdlib\ArrayObject;


class Rola 
{
   static public $tablicaRol=['Testowa','Użytkownik','Lekarz','Zarządca','Administrator','Super Administrator'];
    /**
     *
     * @var type int  
     * pole uzytkownik_iduzytkownik w tabeli rola_has_uzytkownik
     */
   protected $uzytkownik_iduzytkownik;

   protected $idrola;
   
   protected $name;
   
   protected $opis;
   /**
    *
    * @var type arrayObject
    */
   protected $rodzicRola;
   /**
    *
    * @var type arrayObject
    */
   protected $dzieckoRola;
   /**
    *
    * @var type arrayObject
    */
   protected $uprawnienia;
   
  public function __construct() 
  {
   $this->rodzicRola=new ArrayObject();
   $this->dzieckoRola=new ArrayObject();
   $this->uprawnienia=new ArrayObject();
  }
  
  
  public function getArrayCopy()
    {
       return [
           'uzytkownik_iduzytkownik'=>$this->uzytkownik_iduzytkownik,
           'idrola'=> $this->idrola,
           'name' => $this->name,
           'opis' => $this->opis,
       ];
    }
    
     public function exchangeArray($data)
     {
         $this->uzytkownik_iduzytkownik = (isset($data['uzytkownik_iduzytkownik'])) ? $data['uzytkownik_iduzytkownik'] : null;
         $this->idrola = (isset($data['idrola'])) ? $data['idrola'] : null;
         $this->name = (isset($data['name'])) ? $data['name'] : null;
         $this->opis = (isset($data['opis'])) ? $data['opis'] : null;
     }
  
  
  public function getUzytkownik_iduzytkownik() {
      
      return (int)$this->uzytkownik_iduzytkownik;
  }
  
  public function setUzytkownik_iduzytkownik($id) {
      
      $this->uzytkownik_iduzytkownik=$id;
  }
  
   public function getIdrola() 
   {
       return $this->idrola;
   }
   public function setIdrola($idrola) 
   {
       $this->idrola=$idrola;
   }
   public function getName()
   {
       return $this->name;
   }
   public function setName($name)
   {
       if(in_array($name, self::$tablicaRol)){
          $this->name=$name; 
       }else{
           throw new \Exception('Taka rola nie istnieje');
       }
       
   }
   public function getOpis()
   {
       return $this->opis;
   }
   public function setOpis($opis)
   {
       $this->opis=$opis;
   }

   public function addDziecko(Rola $rola)
   {
      // $this->dzieckoRola->append($rola);
      // $this->dzieckoRola[$rola->getIdrola()]->append($rola);
       //$this->dzieckoRola->offsetSet($rola->getIdrola(), $rola);
     if ($this->getIdrola() == $rola->getIdrola()) {
            return false;  
     }
     if(!$this->hasDziecko($rola))
     {
          $this->dzieckoRola[$rola->getIdrola()]=$rola;
          return true;
     }
     
      return false;
   }
   
   public function getDzieciRola()
   {
       return $this->dzieckoRola;
   }
   
   public function hasDziecko(Rola $rola) 
   {
      if($this->dzieckoRola->offsetExists($rola->getIdrola()))
      {
           return true;
      }
           return false;     
   }
   
   public function usunDzieciRola()
   {
       $this->dzieckoRola=new ArrayObject();
   }
  
   public function getUprawnienia() 
   {
       return $this->uprawnienia;
   }
}

