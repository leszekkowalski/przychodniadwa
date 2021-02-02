<?php

namespace Moj_rbac\Polaczenie;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Delete;
use Moj_rbac\Model\Rola;
use Moj_rbac\Model\Uprawnienie;
use RuntimeException;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Cache\Storage\StorageInterface;

class RbacPolaczenie
{
    private $polaczenieRbac;
    
    protected $hydrator;
    
    protected $cache;

    public function __construct(AdapterInterface $poleczenie, HydratorInterface $hydrator, StorageInterface $cache) 
    {
     $this->polaczenieRbac=$poleczenie;
     $this->hydrator=$hydrator;
     $this->cache=$cache;
    }
    
        public function getAdapter()
    {
        return $this->polaczenieRbac;
    }
    
    
    public function pobierzWszystkieRole()
    {
      $sql=new Sql($this->polaczenieRbac);
        $select=$sql->select('rola');
        $select->columns(['idrola','name','opis']);
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult())
        {
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
        $wynikSet=new HydratingResultSet($this->hydrator, new Rola());
        $wynikSet->initialize($wynik);
        return $wynikSet;  
    }
    
    public function wpiszRola(array $dane) : Rola
    {
        if(!$dane['multi_checkbox']){
            
         $wpisz=new Insert('rola');
        $wpisz->values
        ([
            'name'=>$dane['name'],
            'opis'=>$dane['opis'],
        ]);
        
        $sql=new Sql($this->polaczenieRbac);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface)
        {
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
        $idRola=$wynik->getGeneratedValue();
        
        }else{

         $wpisz=new Insert('rola');
        $wpisz->values
         ([
            'name'=>$dane['name'],
            'opis'=>$dane['opis'],
        ]);
        
        $sql=new Sql($this->polaczenieRbac);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface)
        {
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
        $idRola=$wynik->getGeneratedValue();  

        $tablicaIdWpisanychDzieckoRolaId=array();
        
            foreach ($dane['multi_checkbox'] as $id)
            {
                $id_dziecko=(int) $id;
                $tablicaIdWpisanychDzieckoRolaId[]=$id_dziecko;
                $this->wpiszRola_hierarchiaPoWpisieRola($idRola, $id_dziecko);
            }  
          
          $roleDzieci=$this->znajdzRoleZRola_hierarchia($tablicaIdWpisanychDzieckoRolaId);

        }
        
        $rola=new Rola();
        $rola->setIdrola($idRola);
        $rola->setName($dane['name']);
        $rola->setOpis($dane['opis']);
        
        if($roleDzieci)
        {
            foreach ($roleDzieci as $jednaRola)
            {
                $rola->dodajDzieckoRola($jednaRola);
            }
        }
        return $rola;
    }
    
    protected function wpiszRola_hierarchiaPoWpisieRola($rodzic_rola_id,$dziecko_rola_id): int
    {
      
         $wpisz=new Insert('rola_hierarchia');
        $wpisz->values([
            'rodzic_rola_id'=>$rodzic_rola_id,
            'dziecko_rola_id'=>$dziecko_rola_id,
        ]);
        
        $sql=new Sql($this->polaczenieRbac);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
   
        return $wynik->getGeneratedValue();
    }
    
    protected function znajdzRoleZRola_hierarchia(array $tablica) 
    {
       $sql=new Sql($this->polaczenieRbac);
        $select=$sql->select('rola');
        $select->columns(['idrola','name','opis']);
        $select->where([ new \Laminas\Db\Sql\Predicate\In('idrola',$tablica)]);
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
        $wynikSet=new HydratingResultSet($this->hydrator, new Rola());
        $wynikSet->initialize($wynik);
        return $wynikSet;   
    }
    
    public function pobierzRole($id) 
    {
        $sql=new Sql($this->polaczenieRbac);
       $select=$sql->select('rola');
       $select=$select->columns(['idrola','name','opis']);
       $select=$select->where(['idrola=?'=>$id])->limit(1);
       $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
       
       $wynikSet=new HydratingResultSet($this->hydrator, new Rola());
       $wynikSet->initialize($wynik);
       
       $rola=$wynikSet->current();
       
       if(!$rola){
            throw new InvalidArgumentException(
                    sprintf('Nastapił bład podczas pobierania danych z bazy danych o identifikatorze %s',$id)
                    );
        }
        return $rola;  
    }
    
    public function pobierzRoleZRole_hierarchia($idRola) : array
    {
        $sql=new Sql($this->polaczenieRbac);
        $select=$sql->select('rola_hierarchia');
        $select->columns(['dziecko_rola_id']);
        $select->where(['rodzic_rola_id=?'=>$idRola]);
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
        //$wynikSet=new HydratingResultSet($this->hydrator, new Rola());
       // $wynikSet->initialize($wynik);
        
        $dzieckoRolaId=array();      
       foreach ($wynik as $p)
       {
          // $dzieckoRolaId[$p['dziecko_rola_id']]=$p['dziecko_rola_id'];
           $dzieckoRolaId[]=$p['dziecko_rola_id'];
       }
       
        return $dzieckoRolaId;  
    }
    
    
    public function aktualizujRola_bezRola_hierarchia($dane, Rola $rola) : Rola 
    {
        $update=new Update('rola');
        $update->set([
            'name'=>$dane['name'],
            'opis'=>$dane['opis'],
        ])->where(['idrola=?'=>$dane['idrola']]);
        
           $sql=new Sql($this->polaczenieRbac);
           
          $statement = $sql->prepareStatementForSqlObject($update);
          $result = $statement->execute();  
          
          if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Błąd bazy danych podczas wprowadzania danych !!'
            );
        }
         $rola->setName($dane['name']);
         $rola->setOpis($dane['opis']);
         
         return $rola;
    }
    
    public function aktualizujRola_zRola_hierarchia( $dane , Rola $rola) 
    {
        $delete=new Delete('rola_hierarchia');
        $delete->where(['rodzic_rola_id'=>$rola->getIdrola()]);
        
        $sql=new Sql($this->polaczenieRbac);
           
          $statement = $sql->prepareStatementForSqlObject($delete);
          $result = $statement->execute();  
          
          if (! $result instanceof ResultInterface) {
            throw new RuntimeException(
                'Błąd bazy danych podczas usuwania danych !!'
            );
        }
        
        $insert=new Insert('rola_hierarchia');
        $tablicaDanych = array();
        foreach ($dane as $pojDana){
            $tablicaDanych=[
            'rodzic_rola_id'  => $rola->getIdrola(),
            'dziecko_rola_id' =>$pojDana,
            ];
          $insert->values($tablicaDanych);   
          $statement2 = $sql->prepareStatementForSqlObject($insert);
        $result2 = $statement2->execute();
        
         if (! $result2 instanceof ResultInterface) {
            throw new RuntimeException(
                'Błąd bazy danych podczas wprowadzania danych !!'
            );
        }  
        }
       
        return $rola;
    }
    
    public function usunRola(Rola $rola): bool
    {
        if(!$rola->getIdrola()){
            throw new RuntimeException('Nie mozna usunąć Roli. Błąd w pobraniu identyfikatra !');
        }
        
        $delete=new Delete('rola');
        $delete->where(['idrola=?'=>$rola->getIdrola()]);
        
        $sql=new Sql($this->polaczenieRbac);
        $statement=$sql->prepareStatementForSqlObject($delete);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            return false;
        }
        return true;   
    }
    
    public function pobierzWszystkieUprawnienia()
    {
        $cacheWszystkieUprawnienia= $this->cache->getItem('wszystkieUprawnienia');
        $wynikSetArrayId=array();
        
        if(!$cacheWszystkieUprawnienia) {
            
        $sql=new Sql($this->polaczenieRbac);
        $select=$sql->select('uprawnienia');
        $select->columns(['iduprawnienia','name','opis']);
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult())
        {
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
        $wynikSet=new HydratingResultSet($this->hydrator, new Uprawnienie());
        $wynikSet->initialize($wynik);
        $wynikSetArray=$wynikSet->toArray();
        
         $wynikSetArrayId=array();
        
        foreach ($wynikSetArray as $tablica)
        {
           $wynikSetArrayId[$tablica['iduprawnienia']]=$tablica;
           // $wynikSetArrayId[]=$tablica;
        }
        
        
        $this->cache->addItem('wszystkieUprawnienia', $wynikSetArrayId);
        
        }else{
            $wynikSetArrayId=$cacheWszystkieUprawnienia;
        }
        // print_r($wynikSetArrayId);exit();
        return $wynikSetArrayId; 
    }
    
    public function pobierzJednoUprawnienieId($id)
    {
        $sql=new Sql($this->polaczenieRbac);
       $select=$sql->select('uprawnienia');
       $select=$select->columns(['iduprawnienia','name','opis']);
       $select=$select->where(['iduprawnienia=?'=>$id])->limit(1);
       $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
       
       $wynikSet=new HydratingResultSet($this->hydrator, new Uprawnienie());
       $wynikSet->initialize($wynik);
       
       $uprawnienie=$wynikSet->current();
       
       if(!$uprawnienie){
            throw new InvalidArgumentException(
                    sprintf('Nastapił bład podczas pobierania danych z bazy danych o identifikatorze %s',$id)
                    );
        }
        return $uprawnienie; 
    }
    
    public function edytujUprawnienie(Uprawnienie $uprawnienie)
{
    if (! $uprawnienie->getIduprawnienia())
    {
        throw new RuntimeException('Nie można zaktualizowac Uprawnienia; zagubiony identyfikator');
    }

    $update = new Update('uprawnienia');
    $update->set([
            'name' => $uprawnienie->getName(),
            'opis' => $uprawnienie->getOpis(),
    ]);
    $update->where(['iduprawnienia = ?' => $uprawnienie->getIduprawnienia()]);

    $sql = new Sql($this->polaczenieRbac);
    $statement = $sql->prepareStatementForSqlObject($update);
    $result = $statement->execute();

    if (! $result instanceof ResultInterface) {
        throw new RuntimeException(
            'Database error occurred during blog post update operation'
        );
    }

    return $uprawnienie;
}

public function wpiszUprawnienie(array $uprawnienie): Uprawnienie
{
  $wpisz=new Insert('uprawnienia');
        $wpisz->values([
            'name'=>$uprawnienie['name'],
            'opis'=>$uprawnienie['opis'],
        ]);
        
        $sql=new Sql($this->polaczenieRbac);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
   
        $id=$wynik->getGeneratedValue(); 
        $obiektUprawnienie=new Uprawnienie();
        $obiektUprawnienie->setIduprawnienia($id);
        $obiektUprawnienie->setName($uprawnienie['name']);
        $obiektUprawnienie->setOpis($uprawnienie['opis']);
        
        return $obiektUprawnienie;
}

public function usunUprawnienie(Uprawnienie $uprawnienie): bool
{
   if(!$uprawnienie->getIduprawnienia()){
            throw new RuntimeException('Nie mozna usunąć Uprawnienia. Błąd w pobraniu identyfikatra !');
        }
        
        $delete=new Delete('uprawnienia');
        $delete->where(['iduprawnienia=?'=>$uprawnienie->getIduprawnienia()]);
        
        $sql=new Sql($this->polaczenieRbac);
        $statement=$sql->prepareStatementForSqlObject($delete);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            return false;
        }
        return true;  
}

public function pobierzRola_Uprawnienia($idRola) :array
{
  $sql=new Sql($this->polaczenieRbac);
  
  $select=$sql->select();
  $select->from('rola',array('idrola','name','opis'));
  $select->join('rola_has_uprawnienia', 'rola.idrola=rola_has_uprawnienia.rola_idrola');
  $select->join('uprawnienia', 'rola_has_uprawnienia.uprawnienia_iduprawnienia=uprawnienia.iduprawnienia', array('iduprawnienia','name2'=>'name','opis2'=>'opis'));
  $select->where(['rola.idrola=?'=>$idRola]);
  
   $rezultat=$sql->prepareStatementForSqlObject($select);
    $wynik=$rezultat->execute();
        
    if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
          throw new RuntimeException(sprintf(
          'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
     }
  
      $uzytkownik=array();
        foreach ($wynik as $a=>$b){
            foreach ($b as $key=>$value){
             $uzytkownik[$a][$key]=$value; 
   
            }  
        }  
        
       // var_dump($uzytkownik[1]['opis2']);
     return $uzytkownik;
  
}

public function pobierzRola_Uprawnienia_tablica(array $tablica) :array

{
  
 $sql=new Sql($this->polaczenieRbac);
  
  $select=$sql->select();
  $select->from('rola',array('idrola','name','opis'));
  $select->join('rola_has_uprawnienia', 'rola.idrola=rola_has_uprawnienia.rola_idrola');
  $select->join('uprawnienia', 'rola_has_uprawnienia.uprawnienia_iduprawnienia=uprawnienia.iduprawnienia', array('iduprawnienia','name2'=>'name','opis2'=>'opis'));
 // $select->where(['rola.idrola=?'=>$idRola]);   
 $select->where([ new \Laminas\Db\Sql\Predicate\In('rola.idrola',$tablica)]);  

$rezultat=$sql->prepareStatementForSqlObject($select);
    $wynik=$rezultat->execute();
        
    if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
          throw new RuntimeException(sprintf(
          'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
     }
  
      $uzytkownik=array();
        foreach ($wynik as $a=>$b){
            foreach ($b as $key=>$value){
             $uzytkownik[$a][$key]=$value; 
   
            }  
        }  
        
       // var_dump($uzytkownik[1]['opis2']);
     return $uzytkownik;

 
}


}
    

    

