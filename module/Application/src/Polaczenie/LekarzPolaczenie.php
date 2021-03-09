<?php

namespace Application\Polaczenie;

use Laminas\Db\Adapter\AdapterInterface;
use Application\Model\Lekarz;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use RuntimeException;
use InvalidArgumentException;
use Laminas\Db\Sql\Sql;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Cache\StorageFactory;
use Laminas\Paginator\Paginator;
use Laminas\Db\Sql\Insert;
use Application\Validator\PeselValidator;
use Laminas\Db\Sql\Update;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Db\Sql\Where;


class LekarzPolaczenie{
    /**
     *
     * @var AdapetrInterface
     */
    protected $adapter;

     /**
     * @var HydratorInterface
     */
    private $hydrator;
    
    /**
     * @var Lekarz
     */
    private $lekarzPrototype;
    
    protected $cache;

public function __construct(AdapterInterface $adapter, HydratorInterface $hydrator, Lekarz $lekarzPrototype, StorageInterface $cache) {
    
        $this->adapter=$adapter;
        $this->hydrator=$hydrator;
        $this->lekarzPrototype=$lekarzPrototype;
       $this->cache=$cache;
       /**
       if (empty(self::$cache)) { 
            // ustawiamy cache o typie plików tekstowych w katalogu data/cache oraz
            // stosujemy konwersję seliazierow'ą do przechowywania danych
            // nasza kopia zostanie usunięta po 10 minutach (600 sekund)
            self::$cache = StorageFactory::factory([
               'adapter' => [
                    'name' => 'filesystem',
                    'options' => [
                        'cache_dir' => 'data/cache',
                        'ttl' => 600,
                        'namespace' => 'lekarz',
                     //  'writable'=>true,
                    ]
                ],
                'plugins' => ['serializer'],
            ]);
            Paginator::setCache(self::$cache);
            
        }
        */ 
       // if(($this->cache)){
            Paginator::setCache($this->cache);
       // }
}  

public function pobierzWszystkoLekarz(){
    
     $sql=new Sql($this->adapter);
        $select=$sql->select('lekarz');
        $select=$select->columns(['idlekarz','imie','nazwisko','plec','zdjecie','mail','specjalnosc','telefon','opis']);
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
        $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
        $wynikSet->initialize($wynik);
        return $wynikSet;
    
}

    public function pobierzWszystkoLekarzId(): array {
    
        $wynikSet= $this->cache->getItem('pobierzWszystkoLekarzId');
        $wynikSetId=array();
        if(!$wynikSet){
        $sql=new Sql($this->adapter);
        $select=$sql->select('lekarz');
        $select=$select->columns(['idlekarz','imie','nazwisko']);
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
        $wynikSet->initialize($wynik);
        $wynikSetArray=$wynikSet->toArray();
        
        foreach ($wynikSetArray as $lekarz){
            $wynikSetId[$lekarz['idlekarz']]['idlekarz']=$lekarz['idlekarz'];
            $wynikSetId[$lekarz['idlekarz']]['imie']=$lekarz['imie'];
            $wynikSetId[$lekarz['idlekarz']]['nazwisko']=$lekarz['nazwisko'];
        }
        $this->cache->setItem('pobierzWszystkoLekarzId',$wynikSetId);
        }else{
            
         $wynikSetId=$wynikSet;   
        }
        
        return $wynikSetId;
    
    }


public function paginatorLekarz($paginated = false){
    
   if ($paginated) {
            return $this->fetchPaginatedResults();
        } 
        
    $sql=new Sql($this->adapter);
        $select=$sql->select('lekarz');
        $select=$select->columns(['idlekarz','imie','nazwisko','plec','zdjecie','mail','specjalnosc','telefon','opis']);
        $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
        
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
        $wynikSet->initialize($wynik);
        return $wynikSet;       
    
}

private function fetchPaginatedResults()
    {
        // Create a new Select object for the table:
        $sql=new Sql($this->adapter);
        $select=$sql->select('lekarz');
        // Create a new result set based on the Album entity:
       // $resultSetPrototype = new ResultSet();
      //  $resultSetPrototype->setArrayObjectPrototype(new Lekarz('',''));

        
        $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
        
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
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
    
    public function wpiszLekarz(Lekarz $lekarz) : Lekarz {
        
        $lekarzeId=$this->pobierzWszystkoLekarzId();
       // $ids= array_keys($lekarzeId);
        
        
        $peselValidator=new PeselValidator();
        $peselValidator->setPesel($lekarz->getPesel());
        $plec=$peselValidator->getGender();
        if(!$plec){
            $plec=2;//wprowadzony bledny pesel - dla celów szkoleniowych
        }
        $wpisz=new Insert('lekarz');
        $wpisz->values([
            'imie'=>$lekarz->getImie(),
            'nazwisko'=>$lekarz->getNazwisko(),
            'pesel'=>$lekarz->getPesel(),
            'plec'=>$plec,
            'mail'=>$lekarz->getMail(),
            'specjalnosc'=>$lekarz->getSpecjalnosc(),
            'telefon'=>$lekarz->getTelefon(),
            'opis'=>$lekarz->getOpis(),
        ]);
        
        $sql=new Sql($this->adapter);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Lekarza.');
        }
        $idLekarz=$wynik->getGeneratedValue();
        $lekarz->setPlec($plec);
        $lekarz->setIdlekarz($idLekarz);
        
        $lekarzeId[$idLekarz]['idlekarz']=$idLekarz;
        $lekarzeId[$idLekarz]['imie']=$lekarz->getImie();
        $lekarzeId[$idLekarz]['nazwisko']=$lekarz->getNazwisko();
        $this->cache->replaceItem('pobierzWszystkoLekarzId',$lekarzeId);
        
        
        return $lekarz;
        
    }
   
    public function pobierzJedenLekarz(int $id): Lekarz {
      
       $sql=new Sql($this->adapter);
       $select=$sql->select('lekarz');
       $select=$select->columns(['idlekarz','imie','nazwisko','pesel','plec','zdjecie','mail','specjalnosc','telefon','opis']);
       $select=$select->where(['idlekarz'=>$id]);
       $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
       
       $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
       $wynikSet->initialize($wynik);
       
       $lekarz=$wynikSet->current();
       
       if(!$lekarz){
            throw new InvalidArgumentException(
                    sprintf('Nastapił bład podczas pobierania danych z bazy danych lekarza o identifikatorze %s',$id)
                    );
        }
        return $lekarz;
        
    }
    
    public function sprawdzPeselJson($pesel,$idlekarz) {
        
        $sql=new Sql($this->adapter);
       
        if($idlekarz===0){
             $select=$sql->select('lekarz');
           $select=$select->where(['pesel'=>$pesel])->limit(1); 

        }else{
          $select=$sql->select('lekarz');
          //$select=$select->where(['pesel'=>$pesel,new \Laminas\Db\Sql\Predicate\NotIn('idlekarz',array( $idlekarz))])->limit(1) ; 
           $select=$select->where(['pesel'=>$pesel,new \Laminas\Db\Sql\Predicate\Operator('idlekarz', '!=', $idlekarz)])->limit(1) ;           
        }
        
        $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
       
       $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
       $wynikSet->initialize($wynik);
       
       $lekarz=$wynikSet->current();

       if($lekarz){
            //throw new InvalidArgumentException(
              //      sprintf('Nastapił bład podczas pobierania danych z bazy danych lekarza o identifikatorze %s',$id)
               //     );
           return true;
        }else{
           return false; 
        }
        
    }

     public function sprawdzMailJson($mail,$idlekarz) {
        
        $sql=new Sql($this->adapter);
        $select=$sql->select('lekarz');
        if($idlekarz==0){
        $select=$select->where(['mail'=>$mail])->limit(1);
        }else{
        $select=$select->where(['mail'=>$mail, new \Laminas\Db\Sql\Predicate\Operator('idlekarz', '!=', $idlekarz)])->limit(1);   
        }
        
        $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
       
       $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
       $wynikSet->initialize($wynik);
       
       $lekarz=$wynikSet->current();
       
       if(!$lekarz){
            //throw new InvalidArgumentException(
              //      sprintf('Nastapił bład podczas pobierania danych z bazy danych lekarza o identifikatorze %s',$id)
               //     );
           return true;
        }
        
        return false;
    }
    
    public function updateLekarz(Lekarz $lekarz, $nowyLinkZdjecia=null): Lekarz{
        
         if (! $lekarz->getIdlekarz()) {
        throw new RuntimeException('Nie można zaktualizować Lekarza. Błąd identyfikatora');
    }
    
        $peselValidator=new PeselValidator();
        $peselValidator->setPesel($lekarz->getPesel());
        $plec=$peselValidator->getGender();
        if(!$plec){
            $plec=2;//wprowadzony bledny pesel - dla celów szkoleniowych
        }
    $lekarz->setPlec($plec);
    $update = new Update('lekarz');
    $update->set([
            'imie' => $lekarz->getImie(),
            'nazwisko' => $lekarz->getNazwisko(),
            'pesel' => $lekarz->getPesel(),
            'plec' => $lekarz->getPlec(),
          'mail' => $lekarz->getMail(),
            'specjalnosc' => $lekarz->getSpecjalnosc(),
            'telefon' => $lekarz->getTelefon(),
            'opis' => $lekarz->getOpis(),
    ]);
    
   if($nowyLinkZdjecia){
       $lekarz->setZdjecie($nowyLinkZdjecia);
       $update->set([
            'zdjecie' => $lekarz->getZdjecie(),
    ]); 
   }

     $update->where(['idlekarz = ?' => $lekarz->getIdlekarz()]);

    $sql = new Sql($this->adapter);
    $statement = $sql->prepareStatementForSqlObject($update);
    $result = $statement->execute();

    if (! $result instanceof ResultInterface) {
        throw new RuntimeException(
            'Database error occurred during blog post update operation'
        );
    }

    return $lekarz;   
        
        
    }
    
    public function lekarzindexjson($ileNaStrone, $page) {
        if($page==1){
            $offset=0;
        }else{
            $offset=(int)(($page-1)*$ileNaStrone);
        }
        $sql=new Sql($this->adapter);
        
        $select=$sql->select('lekarz');
        $select=$select->columns(['idlekarz','imie','nazwisko','pesel','zdjecie','mail','specjalnosc','telefon','opis']);
        $select->limit($ileNaStrone)->offset($offset);

       $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
        $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
        $wynikSet->initialize($wynik);
        return $wynikSet;
        
    }
    
    public function searchlekarzejson($term)
    {
        $skrot = $term.'%';
        $noweWhere=new Where();
        $noweWhere->like('nazwisko', $term.'%');
        
        $sql=new Sql($this->adapter);
        $select=$sql->select('lekarz');
        //$select=$select->columns(['idlekarz','imie','nazwisko','plec','zdjecie','mail','specjalnosc','telefon','opis']);
       $select->where($noweWhere);
        
     // $select->where('nazwisko LIKE ?', 'Rożek');
        $rezultat=$sql->prepareStatementForSqlObject($select);
        $wynik=$rezultat->execute();
        if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
        
        $wynikSet=new HydratingResultSet($this->hydrator, $this->lekarzPrototype);
        $wynikSet->initialize($wynik);
        return $wynikSet; 
    }
    
} 