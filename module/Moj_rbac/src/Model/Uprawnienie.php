<?php

namespace Moj_rbac\Model;

use Laminas\Stdlib\ArrayObject;

class Uprawnienie 
{
    protected $iduprawnienia;
    
    protected $name;
    
    protected  $opis;
            
   private $role;
            
   public function __construct()
   {
      $this->role= new ArrayObject(); 
   }
   
    public function getArrayCopy()
    {
       return [
           'iduprawnienia'=> $this->iduprawnienia,
           'name' => $this->name,
           'opis' => $this->opis,
       ];
    }
    
     public function exchangeArray($data)
     {
         $this->iduprawnienia = (isset($data['iduprawnienia'])) ? $data['iduprawnienia'] : null;
         $this->name = (isset($data['name'])) ? $data['name'] : null;
         $this->opis = (isset($data['opis'])) ? $data['opis'] : null;
     }
   
   
   public function getRoles() 
   {
      return $this->role; 
   }
   
   public function setIduprawnienia($param) 
   {
      $this->iduprawnienia=$param; 
   }
   public function getIduprawnienia()
   {
       return $this->iduprawnienia;
   }
   public function setName($param)
   {
     $this->name=$param; 
   }
   public function getName()
   {
     return $this->name;  
   }
   public function setOpis($param) 
   {
     $this->opis=$param;  
   }
   public function getOpis()
   {
   return $this->opis;    
   }
}
