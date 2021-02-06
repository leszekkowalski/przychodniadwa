<?php

declare(strict_types=1);
namespace Application\Model;

use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Filter;
use Application\Model\Lekarz;
use Laminas\Stdlib\ArrayObject;
use Moj_rbac\Model\Rola;


class Uzytkownik implements InputFilterAwareInterface{

const STATUS_AKTYWNY=1;
const STATUS_NIEAKTYWNY=0;
const STATUS_NIEOKRESLONY=2;
    
public $inputFilter;

protected $iduzytkownik;
protected $imie;
protected $nazwisko;
protected $mail;
protected $status;
protected $data_powstania;
protected $haslo;
protected $pwd_sol;
protected $pwd_sol_date;
protected $lekarz_idlekarz2;
/**
 *
 * @var type arrayObject
 * 
 */

protected $role;


    public function __construct()
    {
       $this->role=new ArrayObject(); 
    }
    
    public function exchangeArray($row) {
    $this->iduzytkownik = (!empty($row['iduzytkownik'])) ? $row['iduzytkownik'] : null;
    $this->imie = (!empty($row['imie'])) ? $row['imie'] : null;
    $this->nazwisko = (!empty($row['nazwisko'])) ? $row['nazwisko'] : null;
    $this->mail = (!empty($row['mail'])) ? $row['mail'] : null;
    $this->status = (!empty($row['status'])) ? $row['status'] : 2;
    $this->data_powstania = (!empty($row['data_powstania'])) ? $row['data_powstania'] : null;
    $this->haslo = (!empty($row['haslo'])) ? $row['haslo'] : null;  
    $this->pwd_sol = (!empty($row['pwd_sol'])) ? $row['pwd_sol'] : null;
    $this->pwd_sol_data = (!empty($row['pwd_sol_data'])) ? $row['pwd_sol_data'] : null;
    $this->lekarz_idlekarz2 = (!empty($row['lekarz_idlekarz2'])) ? $row['lekarz_idlekarz2'] : null;  
    }
    
    public function getIduzytkownik() {
        return (int) $this->iduzytkownik;
    }
    public function setIduzytkownik($iduzytkownik) {
        $this->iduzytkownik=$iduzytkownik;
    }
    public function getImie(){
        return $this->imie;
    }
    public function setImie($imie){
        $this->imie=$imie;
    }
    public function getNazwisko() {
        return $this->nazwisko;
    }
    public function setNazwisko($nazwisko){
        $this->nazwisko=$nazwisko;
    }
    public function getMail() {
        return $this->mail;
    }
    public function setMail($mail) {
       $this->mail=$mail; 
    }
    
    public function getStatusId() {
        return $this->status;
    }
    public function getStatus() {
        if($this->getStatusId()==1){
            return 'AKTYWNY';
        }else{
            if($this->getStatusId()==0){
                return 'NIE AKTYWNY';
            }else{
                return 'NIEZNANY';
            }
        }
    }
    public function setStatus($status) {
        $this->status=$status;
    }
    public function getDataPowstania() {
        return $this->data_powstania;
    }
    public function setDataPowstania(\DateTime $data) {
        $this->data_powstania=$data;
    }
    public function getHaslo() {
        return $this->haslo;
    }
    public function setHaslo($haslo) {
        $this->haslo=$haslo;
    }
    public function getPwdSol() {
        return $this->pwd_sol;
    }
    public function setPwdSol($pwdsol) {
        $this->pwd_sol=$pwdsol;
    }
    public function getPwdSolData() {
        return $this->pwd_sol_date;
    }
    public function setPwdSolData($data) {
        $this->pwd_sol_date=$data;
    }
    public function getLekarz() {
        return $this->lekarz_idlekarz2;
    }
    public function setLekarz($idlekarz) {
        $this->lekarz_idlekarz2=$idlekarz;
    }
    
    public function getRole() {
        return $this->role;
    }
    
   public function addRole(Rola $rola)
    {
     $this->role->offsetSet($rola->getIdrola(), $rola);
    }
    
    public function getRoleJakoNapis() 
    {
        $role=$this->role;
       $napis=null;
        foreach ( $role as $rola){
            $napis=$napis.$rola->getName().', ';
        } 
        return $napis;
    }
    
    
    public function getInputFilter(): InputFilterInterface {
        if ($this->inputFilter) {
             return $this->inputFilter;
         }
         $inputFilter = new InputFilter(); 
       //////////////////////////////////////////////////////// 
         $inputFilter->add([
            'name' => 'iduzytkownik',
            'allow_empty' => true,   
            'filters' => [
              ['name' => Filter\ToInt::class],
              ['name'=> Filter\ToNull::class],
            ],
        ]);
    ////////////////////////////////////////////////////////////
        $inputFilter->add([
            'name'=>'imie',
            'require'=>true,
            'filters'=>[
                ['name'=> \Laminas\Filter\StringTrim::class],
            ],
            'validators'=>[
             ['name'=> \Laminas\Validator\NotEmpty::class],
               ['name'=> \Laminas\Validator\StringLength::class,
                'options'=>[
                   'min'=>2,
                    'max'=>30,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> \Laminas\Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\'\-]+$/",
                        'messages'=>[
                        \Laminas\Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
            ],
        ]);
       /////////////////////////////////////////
              $inputFilter->add([
            'name'=>'nazwisko',
            'require'=>true,
            'filters'=>[
                ['name'=> \Laminas\Filter\StringTrim::class],
            ],
            'validators'=>[
             ['name'=> \Laminas\Validator\NotEmpty::class],
               ['name'=> \Laminas\Validator\StringLength::class,
                'options'=>[
                   'min'=>2,
                    'max'=>30,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> \Laminas\Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\'\-]+$/",
                        'messages'=>[
                        \Laminas\Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
            ],
        ]);
       /////////////////////////////////////////
         if(!$this->mail){
         $inputFilter->add([
            'name' => 'mail',
            'required' => false,
            'filters' => [
               ['name' => StringTrim::class]  ,
               ['name'=> \Laminas\Filter\StripTags::class] ,
            ],
            'validators' => [
                  ['name' => \Laminas\Validator\EmailAddress::class],
                ['name'=> \Laminas\Validator\Db\NoRecordExists::class,
                    'options'=>[
                        'table'   => 'uzytkownik',
                         'field'   => 'mail',
                        'adapter' => Lekarz::pobierzAdapter(),
                    //  'adapter' => $this->pobierzAdapter(),
                        'messages' => array(
                         \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Taki adres email już istnieje w bazie'
                     )
                    ]
                    
                ]
            ],
        ]);
        }else{
            $inputFilter->add([
            'name' => 'mail',
            'required' => false,
            'filters' => [
               ['name' => StringTrim::class]  ,
               ['name'=> \Laminas\Filter\StripTags::class] ,
            ],
            'validators' => [
                  ['name' => \Laminas\Validator\EmailAddress::class],
                ['name'=> \Laminas\Validator\Db\NoRecordExists::class,
                    'options'=>[
                        'table'   => 'uzytkownik',
                         'field'   => 'mail',
                         'adapter' => Lekarz::pobierzAdapter(),
                       //  'adapter' => $this->pobierzAdapter(),
                        'exclude' => [
                              'field' => 'mail',
                               'value' => $this->mail,
                                      ],
                        'messages' => array(
                         \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Taki adres email już istnieje w bazie'
                     )
                    ]
                    
                ]
            ],
        ]);
        }
        /////////////////////////////////////////////////////      
            $inputFilter->add([
                'name'     => 'status',
                'required' => false,
                'filters'  => [                    
                    ['name' => \Laminas\Filter\ToInt::class],
                ],                
                'validators' => [
                    ['name'=>'InArray', 'options'=>['haystack'=>[1, 2]]]
                ],
            ]);  
        /////////////////////////////////     
              
              
         $this->inputFilter = $inputFilter;
         return $this->inputFilter;  
    }

    public function setInputFilter(InputFilterInterface $inputFilter): InputFilterAwareInterface {
       throw new DomainException(sprintf(
            '%s nie ma pozwolenia na użycie filtra',
            __CLASS__
        ));  
    }

}

