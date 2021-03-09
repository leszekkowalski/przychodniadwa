<?php

namespace Kalendarz\Polaczenie;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\HydratorInterface;
use RuntimeException;
use InvalidArgumentException;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Cache\Storage\StorageInterface;
use Kalendarz\Entity\Wydarzenie;
use Laminas\Db\Sql\Sql;
use Kalendarz\Entity\Kalendarz;

class WydarzeniePolaczenie
{
    private $polaczenieWydarzenie;
    
    private $hydrator;
    
    private $prototypWydarzenie;
    
    public function __construct(AdapterInterface $wydInterface, HydratorInterface $hydrator, Wydarzenie $prototypWydarzenie)
    {
      $this->polaczenieWydarzenie=$wydInterface;  
      $this->hydrator=$hydrator;
      $this->prototypWydarzenie= $prototypWydarzenie;
    }
    
    public function pobierzWydarzeniaMiesiaca($kalendarz,$idLekarz)
    {
      $sql=new Sql($this->polaczenieWydarzenie);
      $select=$sql->select()->from('wydarzenie');
      $select->where->nest()->isNull('wydarzenie_idlekarz')->OR->equalTo('wydarzenie_idlekarz', $idLekarz)->unnest();
      
      $pierwszyDzienMiesiaca=strftime('%F',mktime(0, 0, 0, $kalendarz->miesiac,  1,   $kalendarz->rok)); 
      $ostatniDzienMiesiaca=strftime('%F',mktime(0, 0, 0, $kalendarz->miesiac,  $kalendarz->dniMiesiaca, $kalendarz->rok)); 
      
      $select->where->between('wydarzenie_data', $pierwszyDzienMiesiaca, $ostatniDzienMiesiaca);
      $select->order('wydarzenie_data','wydarzenie_start');
      
       $rezultat = $sql->prepareStatementForSqlObject($select);
       $wynik    = $rezultat->execute();
       
       if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
       
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
       
    $resultSet = new HydratingResultSet(
        $this->hydrator,
        $this->prototypWydarzenie,
    );
        
        $resultSet->initialize($wynik);
        return $resultSet;
    }
    
    public function pobierzWydarzeniaDzienPoIdlekarz($data, $idLekarz) 
    {
      $sql=new Sql($this->polaczenieWydarzenie);
      $select=$sql->select()->from('wydarzenie');
      $select->where->nest()->isNull('wydarzenie_idlekarz')->OR->equalTo('wydarzenie_idlekarz', $idLekarz)->unnest();  
   
      $select->where(['wydarzenie_data'=>$data]);
      $select->order('wydarzenie_start');
      
       $rezultat = $sql->prepareStatementForSqlObject($select);
       $wynik    = $rezultat->execute();
       
       if(! $wynik instanceof ResultInterface || ! $wynik->isQueryResult() ){
       
            throw new RuntimeException(sprintf(
            'Nastapił błąd podczas pobierania danych z bazy danych. Nieznany bład. Powiadom administratora.'));
        }
       
    $resultSet = new HydratingResultSet(
        $this->hydrator,
        $this->prototypWydarzenie,
    );
        
        $resultSet->initialize($wynik);
        return $resultSet;
    }
    
    public function pobierzWydarzeniePoId($id): Wydarzenie
    {
       $sql=new Sql($this->polaczenieWydarzenie);
       $select=$sql->select('wydarzenie');
       $select=$select->where(['idwydarzenie'=>$id]);
       $rezultat=$sql->prepareStatementForSqlObject($select);
       $wynik=$rezultat->execute();
       
       $wynikSet=new HydratingResultSet($this->hydrator, $this->prototypWydarzenie);
       $wynikSet->initialize($wynik);
       
       $wydarzenie=$wynikSet->current();
       
       if(!$wydarzenie){
            throw new InvalidArgumentException(
                    sprintf('Nastapił bład podczas pobierania danych z bazy danych wydarzenia o identifikatorze %s',$id)
                    );
        }
        return $wydarzenie;  
    }
    
    
}