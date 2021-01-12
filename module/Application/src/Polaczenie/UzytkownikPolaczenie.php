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
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Insert;

class UzytkownikPolaczenie {
    
    protected $adapter;
    
    protected $hydrator;
    
    protected $uzytkownikPrototype;
    
    protected  $cache;


    public function __construct(AdapterInterface $adapter, HydratorInterface $hydrator, Uzytkownik $uzytkownikPrototype, StorageInterface $cache) {
     
        $this->adapter=$adapter;
        $this->hydrator=$hydrator;
        $this->uzytkownikPrototype=$uzytkownikPrototype;
        $this->cache=$cache;
        /**  
        if(empty(self::$cache2)){
            self::$cache2= \Laminas\Cache\StorageFactory::factory([
                 'adapter' => [
                    'name' => 'filesystem',
                    'options' => [
                        'cache_dir' => 'data/cache/uzytkownik',
                        'ttl' => 600,
                        'namespace' => 'uzytkownik',
                     //  'writable'=>true,
                    ]
                ],
                'plugins' => ['serializer'],
            ]);
             Paginator::setCache(self::$cache2);
        };
         */
        
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
        
       // $wynikPobrania=self::$cache->getItem('wynikSetArray');
        $wynikPobrania= $this->cache->getItem('wynikSetArray');
        
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
        
        
        $this->cache->setItem('wynikSetArray',$wynikSetArray);
        }else{
           $wynikSetArray=$wynikPobrania; 
        }
        
        //var_dump($wynikPobrania);exit();
        
        return $wynikSetArray;
    } 
    
    public function uzytkownikindexjson($ileNaStrone,$page) {
        
        if($page==1){
            $offset=0;
        }else{
            $offset=(int)(($page-1)*$ileNaStrone);
        }
        $sql=new Sql($this->adapter);
        
        $select=$sql->select('uzytkownik');
        $select=$select->columns(['iduzytkownik','imie','nazwisko','mail','status','lekarz_idlekarz2']);
        $select->limit($ileNaStrone)->offset($offset);

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
    
     public function znajdzJedenPoMailUzytkownik(string $mail)
     {
       
        $select=new \Laminas\Db\Sql\Select('uzytkownik');
        $select=$select->columns(['iduzytkownik','imie','nazwisko','mail','haslo','status','pwd_sol','pwd_sol_date','lekarz_idlekarz2']);
        $select->where(['mail'=>$mail]);
        $select->limit(1);
        
        $sql=new Sql($this->adapter);
        
        $rezultat = $sql->prepareStatementForSqlObject($select);
        $wynik    = $rezultat->execute();
        
         if (! $wynik instanceof ResultInterface) {
        throw new RuntimeException(
            'Błąd bazy danych podczas pobrania Uzytkownika'
        );
    }
  
        $wynikSet=new HydratingResultSet($this->hydrator, $this->uzytkownikPrototype);
        $wynikSet->initialize($wynik);
        $odbior=$wynikSet->current();

        return $odbior;
          
    }
    
      public function znajdzJedenPoMailPwd_solUzytkownik(string $mail)
     {
       
        $select=new \Laminas\Db\Sql\Select('uzytkownik');
        $select=$select->columns(['pwd_sol','pwd_sol_date']);
        $select->where(['mail'=>$mail]);
        $select->limit(1);
        
        $sql=new Sql($this->adapter);
        
        $rezultat = $sql->prepareStatementForSqlObject($select);
        $wynik    = $rezultat->execute();
        
         if (! $wynik instanceof ResultInterface) {
        throw new RuntimeException(
            'Błąd bazy danych podczas pobrania Uzytkownika'
        );
    }
  
        $wynikSet=new HydratingResultSet($this->hydrator, $this->uzytkownikPrototype);
        $wynikSet->initialize($wynik);
        $odbior=$wynikSet->current();

        return $odbior;
          
    }
    
    public function znajdzJedenPoIdUzytkownik(int $id)
     {
        
        $select=new \Laminas\Db\Sql\Select('uzytkownik');
        $select=$select->columns(['iduzytkownik','imie','nazwisko','mail','status','lekarz_idlekarz2']);
        $select->where(['iduzytkownik'=>$id]);
        $select->limit(1);
        
        $sql=new Sql($this->adapter);
        
        $rezultat = $sql->prepareStatementForSqlObject($select);
        $wynik    = $rezultat->execute();
        
        $wynikSet=new HydratingResultSet($this->hydrator, $this->uzytkownikPrototype);
        $wynikSet->initialize($wynik);
        $odbior=$wynikSet->current();
    
        return $odbior;
          
    }
    
    public function zmienHasloUzytkownik(string $haslo, int $id) : bool
    {
        if((!(is_int($id))) || (!(is_string($haslo))))
        {
            throw new RuntimeException('Nie mozna zaktualizowac danych. Brak hasła lub identifikatora');
        }
        
        $update=new Update('uzytkownik');
        $update->set([
            'data_powstania'=> date("Y-m-d H:i:s"),
            'haslo'=>$haslo
        ]);
        $update->where(['iduzytkownik'=>$id]);
        
        $sql=new Sql($this->adapter);
        $statement = $sql->prepareStatementForSqlObject($update);
        
        $result = $statement->execute();

        if (! $result instanceof ResultInterface) {
        throw new RuntimeException(
            'Błąd bazy danych podczas aktualizacji Uzytkownika'
        );
    }

    return true;
        
    }
    
        public function wpiszUzytkownikPorejestracji(Uzytkownik $uzytkownik) : Uzytkownik
        {
        
       // $lekarzeId=$this->pobierzWszystkoLekarzId();
       // $ids= array_keys($lekarzeId);
        
        $wpisz=new Insert('uzytkownik');
        $wpisz->values([
            'imie'=>$uzytkownik->getImie(),
            'nazwisko'=>$uzytkownik->getNazwisko(),
            'mail'=>$uzytkownik->getMail(),
            'status'=>$uzytkownik->getStatusId(),
            'data_powstania'=>$uzytkownik->getDataPowstania(),
            'haslo'=>$uzytkownik->getHaslo(),
        ]);
        
        $sql=new Sql($this->adapter);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
        $idUzytkownik=$wynik->getGeneratedValue();
        $uzytkownik->setIduzytkownik($idUzytkownik);
        
       // $lekarzeId[$idLekarz]['idlekarz']=$idLekarz;
       // $lekarzeId[$idLekarz]['imie']=$lekarz->getImie();
       // $lekarzeId[$idLekarz]['nazwisko']=$lekarz->getNazwisko();
       // $this->cache->replaceItem('pobierzWszystkoLekarzId',$lekarzeId);
        
        return $uzytkownik;
        
    }
    
    public function wpiszPwd_sol_pwd_sol_date(Uzytkownik $uzytkownik) : Uzytkownik
    {
       
        $wpisz=new Update('uzytkownik');
        $wpisz->set([
            'pwd_sol'=>$uzytkownik->getPwdSol(),
            'pwd_sol_date'=>$uzytkownik->getPwdSolData(),
        ]);
        $wpisz->where(['iduzytkownik'=>$uzytkownik->getIduzytkownik()]);
        
        $sql=new Sql($this->adapter);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
        return $uzytkownik;
    }
    
    public function wpiszResetHaslo($noweHaslo,$idUzytkownik)
    {
       
        $wpisz=new Update('uzytkownik');
        $wpisz->set([
            'data_powstania'=>date("Y-m-d H:i:s"),
            'haslo'=>$noweHaslo,
            'pwd_sol'=>null,
            'pwd_sol_date'=>null,
        ]);
        $wpisz->where(['iduzytkownik'=>$idUzytkownik]);
        
        $sql=new Sql($this->adapter);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
        return true;
    }
}

