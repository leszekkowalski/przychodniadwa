<?php

namespace Application\Polaczenie;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\HydratorInterface;
use Application\Model\Uzytkownik;
use Laminas\Paginator\Paginator;
use Laminas\Db\Sql\Sql;
use RuntimeException;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Cache;


class UzytkownikPolaczenie {
    
    protected $adapter;
    
    protected $hydrator;
    
    protected $uzytkownikPrototype;
    
    protected static $cache;


    public function __construct(AdapterInterface $adapter, HydratorInterface $hydrator, Uzytkownik $uzytkownikPrototype) {
     
        $this->adapter=$adapter;
        $this->hydrator=$hydrator;
        $this->uzytkownikPrototype=$uzytkownikPrototype;
          
        if(empty(self::$cache)){
            self::$cache= \Laminas\Cache\StorageFactory::factory([
                 'adapter' => [
                    'name' => 'filesystem',
                    'options' => [
                        'cache_dir' => 'data/cache',
                        'ttl' => 600,
                        'namespace' => 'uzytkownik',
                     //  'writable'=>true,
                    ]
                ],
                'plugins' => ['serializer'],
            ]);
             Paginator::setCache(self::$cache);
        };
        
        
    }
    
    public function paginatorUzytkownik($paginated = false){
    
   if ($paginated) {
            return $this->fetchPaginatedResults();
        } 
        
    $sql=new Sql($this->adapter);
      $select=$sql->select();
      $select->from('uzytkownik',array('iduzytkownik','imie','nazwisko','mail','status','lekarz_idlekarz2'));
    //  $select->join('lekarz', 'uzytkownik.lekarz_idlekarz2=lekarz.idlekarz', array('lekarz_imie'=>'imie','lekarz_nazwisko'=>'nazwisko'),'inner');
    //  $select->join('rola_has_uzytkownik','uzytkownik.iduzytkownik=rola_has_uzytkownik.uzytkownik_iduzytkownik');
     
   // $select->join('rola','rola_has_uzytkownik.rola_idrola=rola.idrola',array('rola'=>'name'),'right');
    
      $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        $wynikSet=new HydratingResultSet($this->hydrator, $this->uzytkownikPrototype);
        $wynikSet->initialize($wynik);
        return $wynikSet;       
    
}

private function fetchPaginatedResults()
    {
        // Create a new Select object for the table:
        $sql=new Sql($this->adapter);
       //$select=$sql->select('uzytkownik');
       $select=$sql->select();
       $select->from('uzytkownik',array('iduzytkownik','imie','nazwisko','mail','status','lekarz_idlekarz2'));
     //  $select->join('lekarz', 'uzytkownik.lekarz_idlekarz2=lekarz.idlekarz', array('lekarz_imie'=>'imie','lekarz_nazwisko'=>'nazwisko'));
     // $select->join('rola_has_uzytkownik','uzytkownik.iduzytkownik=rola_has_uzytkownik.uzytkownik_iduzytkownik');
   // $select->join('rola','rola_has_uzytkownik.rola_idrola=rola.idrola',array('rola'=>'name'),'inner');
   // $select->where->isNull('rola.name');
  
        $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
        
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        $wynikSet=new HydratingResultSet($this->hydrator, $this->uzytkownikPrototype);
        $wynikSet->initialize($wynik);
        
        // Create a new pagination adapter object:
        $paginatorAdapter = new DbSelect(
            // our configured select object:
            $select,
            // the adapter to run it against:
            $this->adapter,
            // the result set to hydrate:
            $wynikSet
        );

        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }
    
    public function findAllUzytkownicy(){
        
        $wynikPobrania=self::$cache->getItem('wynikSetArray');
        
        if(!$wynikPobrania){
        $sql=new Sql($this->adapter);
        $select=$sql->select('uzytkownik');
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
       // $uzytkownik=array();
      //  foreach ($wynik as $a=>$b){
      //      echo 'Informacja o uzytkowniku nr'.($a+1)."\n";
         //   foreach ($b as $key=>$value){
          //     $uzytkownik[$a][$key]=$value; 
          //  }
      //  }
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
       
         $wynikSet = new HydratingResultSet($this->hydrator, $this->uzytkownikPrototype);
        $wynikSet->initialize($wynik);
        $wynikSetArray=$wynikSet->toArray();
        $wynikaSetArrrayId=array();
        foreach ($wynikSetArray as $tablica){
           $wynikaSetArrrayId[$tablica['iduzytkownik']] =$tablica;
        }
        
        
        self::$cache->setItem('wynikSetArray',$wynikSetArray);
        }else{
           $wynikSetArray=$wynikPobrania; 
        }
        
        //var_dump($wynikPobrania);exit();
        
        return $wynikSetArray;
    }  
    
}

