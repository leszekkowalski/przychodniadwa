<?php

namespace Moj_rbac\Service;

use Laminas\Permissions\Rbac\Role as RbacRole;

use Moj_rbac\Model\Rola;
use Moj_rbac\Model\Uprawnienie;
use Application\Model\Uzytkownik;
use Moj_rbac\Polaczenie\RbacPolaczenie;
use Application\Polaczenie\UzytkownikPolaczenie;
use Moj_rbac\Service\KontrolaUprawnienIndywidualnychRbac;
use Laminas\Permissions\Rbac\Rbac;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Session\Container;

class RbacManager
{
    
    private $rbacPolaczenie;
    
    private $uzytkownikPolaczenie;
    /**
     *
     * @var type Laminas\Permissions\Rbac\Rbac;
     */
    private $rbac;
    
    private $cache;
    
    private $userSession;
    /**
     *
     * @var type array 
     */
    private $indywidualneUprawnieniaManager=[];
    
public function __construct(
        RbacPolaczenie $pol, 
        UzytkownikPolaczenie $polUzyt,
        StorageInterface $cache,  
        $uprawnieniaIndywidualne)
{
    $this->rbacPolaczenie=$pol;
    $this->uzytkownikPolaczenie=$polUzyt;
    $this->cache=$cache;
    $this->userSession=new \Laminas\Session\Container('uzytkownik');
    $this->indywidualneUprawnieniaManager=$uprawnieniaIndywidualne;
} 
 /**
  * Funkcja odpowiada za załadowanie parametrów (ról i uprawnień) do kontenera rbac.
  * patametr domyslny $tworz=false tworzy rbac jest go nie ma, jesli wpiszemy true
  * usuwamy z pamieci rbac aktualny i łądujemy go od nowa (np. po dodaniu /usunieciu/ roli czy uprawnienia
  * @param type $tworz
  * @return type null
  */ 
 public function init($tworz=false)
 {
     if($this->rbac!=null && !$tworz)
     {
         //wlasnie zostalo zainicjowane - nic nie robimy
         return;
     }
     if($tworz)
     {
         //uzytkownik chce przeładować rbac, wiec czyscimy pamoec cache
         $this->cache->removeItem('rbac_pamiec');
     }
     
     //probujemy załadowac rbac z pamieci
     $this->rbac=$this->cache->getItem('rbac_pamiec',$rezultat);
     if(!$rezultat)
     {
         //tworzymy kontener rbac
         $rbac=new Rbac();
         $this->rbac=$rbac;
         
         //Konstrujemy rbac kontener poprzez załadowanie ról i uprawnien z bazy danych
        
         $rbac->setCreateMissingRoles(true);
         
         $role=$this->rbacPolaczenie->pobierzWszystkieRole();
         
         foreach ($role as $rola)
         {
             $nazwaRoli=$rola->getName();
             $roleDzieci=[];
             $dzieci=$this->rbacPolaczenie->pobierzRoleJakoDzieci_z_rola_hierarchia($rola);
             if($dzieci)
             {
                 foreach ($dzieci as $dziecko)
                 {
                    
                     $roleDzieci[]=$dziecko->getName();
                     $r=$dziecko->getName();
                     echo '<br/>';
                     echo 'Do roli '.$nazwaRoli.' dodaje '.$r.'<br/>';
                 }
                  
                 $rbac->addRole($nazwaRoli, $roleDzieci);
                 
             }else{
                 $rbac->addRole($nazwaRoli);
             }
             //pobieram uprawnienia Roli
             $uprawnieniaRoli=$this->rbacPolaczenie->pobierzRola_Uprawnienia($rola->getIdrola());
             
             foreach ($uprawnieniaRoli as $uprawnienie)
             {
                 $rbac->getRole($nazwaRoli)->addPermission($uprawnienie['name2']);
             }
     
         }
         $this->cache->setItem('rbac_pamiec', $rbac);
     }
 }
 /**
  * funkcja odpoawiada za sparwdzenie czy dany uzytkownik ma posiadane uprawnienia 
  */
 public function isGranted($uzytkownik, $uprawnienie,$parametry=null)
 {
     if($this->rbac==null)
     {
        $this->init(); 
     }
     
     if($uzytkownik==null)
     {
       $idUzytkownik=$this->userSession->details->getIduzytkownik();
         
         if($idUzytkownik==null)
         {
             return false;
         }
        $uzytkownik=$this->uzytkownikPolaczenie->znajdzJedenPoIdUzytkownik($idUzytkownik);
               
        if($uzytkownik==null)
        {
            throw new \Exception('There is no user with such identity');
        }
        }
               
        $roleArrayId=$this->uzytkownikPolaczenie->pobierzRoleUzytkownikaJakoArray(); 
        
         if(isset($roleArrayId[$uzytkownik->getIduzytkownik()]))
         {
             
          $nazwaRoliUzytkownika=$roleArrayId[$uzytkownik->getIduzytkownik()]['name'];
          
        }else{
            return false;
        }
         
        if($this->rbac->isGranted($nazwaRoliUzytkownika, $uprawnienie))
        {
           
           if($parametry==null) return true;
           
            foreach ($this->indywidualneUprawnieniaManager as $indUprawnienie)
            {
                if($indUprawnienie->assert($this->rbac,$uprawnienie,$parametry))
                {
                    return true;
                }
            }
            
            $rola=new Rola();
            $rola->setIdrola($roleArrayId[$uzytkownik->getIduzytkownik()]['idrola']);
            
            
            $dzieciRoli=$this->rbacPolaczenie->pobierzRoleJakoDzieci_z_rola_hierarchia($rola);
            
            foreach ($dzieciRoli as $dziecko)
            {
                if($this->rbac->isGranted($dziecko->getName(), $uprawnienie))
                {
                    return true;
                }
            }
            
            return false;
        }
     
     
     
     
 }
 
 
 
}
