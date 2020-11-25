<?php

namespace Application\Polaczenie;

use Laminas\Db\Adapter\AdapterInterface;
use Application\Model\Lekarz;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use RuntimeException;
use Laminas\Db\Sql\Sql;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Cache\StorageFactory;
use Laminas\Paginator\Paginator;

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
    
    protected static $cache;

public function __construct(AdapterInterface $adapter, HydratorInterface $hydrator, Lekarz $lekarzPrototype) {
    
        $this->adapter=$adapter;
        $this->hydrator=$hydrator;
        $this->lekarzPrototype=$lekarzPrototype;
       
       if (empty(self::$cache)) { 
            // ustawiamy cache o typie plików tekstowych w katalogu data/cache oraz
            // stosujemy konwersję seliazierow'ą do przechowywania danych
            // nasza kopia zostanie usunięta po 10 minutach (600 sekund)
            self::$cache = StorageFactory::factory([
               'adapter' => [
                    'name' => 'filesystem',
                    'options' => [
                        'cache_dir' => 'data/cache',
                        'ttl' => 600
                    ]
                ],
                'plugins' => ['serializer'],
            ]);
            Paginator::setCache(self::$cache);
        }
             
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
   


    
}