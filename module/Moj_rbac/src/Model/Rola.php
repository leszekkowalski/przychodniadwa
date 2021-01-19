<?php

namespace Moj_rbac\Model;

use Laminas\Stdlib\ArrayObject;


class Rola 
{
   static public $tablicaRol=['Użytkownik','Lekarz','Zarządca','Administrator','Super Administrator'];
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


}

