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
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Update;

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
    
    
    public function wpiszNoweWydarzenie(Wydarzenie $wydarzenie, string $string) : Wydarzenie
    {
       
        if($string=='no')
        {
          $wydarzenie->setIdLekarz(null); 
        }
       
         $wpisz=new Insert('wydarzenie');
        $wpisz->values([
            'wydarzenie_idlekarz'=>$wydarzenie->getIdLekarz(),
            'wydarzenie_data'=>$wydarzenie->getWydarzenie_data(),
            'wydarzenie_start'=>$wydarzenie->getWydarzenie_start(),
            'wydarzenie_koniec'=>$wydarzenie->getWydarzenie_koniec(),
            'wydarzenie_tytul'=>$wydarzenie->getWydarzenie_tytul(),
            'wydarzenie_opis'=>$wydarzenie->getWydarzenie_opis(),
        ]);
        
        $sql=new Sql($this->polaczenieWydarzenie);
        $statement=$sql->prepareStatementForSqlObject($wpisz);
        $wynik=$statement->execute();
        
        if(!$wynik instanceof ResultInterface){
            throw new RuntimeException('Błąd bazy danych podczas wprowadzenia danych Wydarzenie.');
        }
        $idWydarzenie=$wynik->getGeneratedValue();
        $wydarzenie->setIdwydarzenie($idWydarzenie);
        
        return $wydarzenie;
    }
    
    public function edytujWydarzenie(Wydarzenie $wydarzenie): Wydarzenie
    {
        if($wydarzenie->getIdLekarz()){
            $id=$wydarzenie->getIdLekarz();
        }else{
            $id=null;
        }
        
        
          $update = new Update('wydarzenie');
    $update->set([
            'wydarzenie_idlekarz' => $id,
            'wydarzenie_data' => $wydarzenie->getWydarzenie_data(),
            'wydarzenie_start' => $wydarzenie->getWydarzenie_start(),
            'wydarzenie_koniec' => $wydarzenie->getWydarzenie_koniec(),
          'wydarzenie_tytul' => $wydarzenie->getWydarzenie_tytul(),
            'wydarzenie_opis' => $wydarzenie->getWydarzenie_opis(),
    ]);
    
     $update->where(['idwydarzenie = ?' => $wydarzenie->getIdwydarzenie()]);

    $sql = new Sql($this->polaczenieWydarzenie);
    $statement = $sql->prepareStatementForSqlObject($update);
    $result = $statement->execute();

    if (! $result instanceof ResultInterface) {
        throw new RuntimeException(
            'Database error occurred during blog post update operation'
        );
    }
   
        return $wydarzenie;
    }
    
    public function usunWydarzenie($idWydarzenie)
    {
        $usun = new \Laminas\Db\Sql\Delete('wydarzenie');
        
        $usun->where(['idwydarzenie=?'=>$idWydarzenie]);
        
        $sql = new Sql($this->polaczenieWydarzenie);
    $statement = $sql->prepareStatementForSqlObject($usun);
    $result = $statement->execute();

    if (! $result instanceof ResultInterface) {
        return false;
    }

    return true;
    }
    
    
}