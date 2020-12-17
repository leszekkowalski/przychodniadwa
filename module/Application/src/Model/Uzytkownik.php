<?php

declare(strict_types=1);
namespace Application\Model;

use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Filter;
use Application\Model\Lekarz;


class Uzytkownik implements InputFilterAwareInterface{
    
public $inputFilter;

protected $iduzytkownik;
protected $imie;
protected $nazwisko;
protected $mail;
protected $status;
protected $data_powstania;
protected $haslo;
protected $pwd_sol;
protected $pwd_sol_data;
protected $lekarz_idlekarz2;
/**
 *
 * @var type string
 * pole 'name' z tabeli rola
 */

protected $rola;
/**
 *
 * @var type string
 * pola 'imie' i 'nazwisko' z tabeli lekarz
 */
protected $lekarz_imie;
protected $lekarz_nazwisko;

    public function __construct() {
        
    }
    
    public function getIduzytkownik() {
        return $this->iduzytkownik;
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
    public function getStatus() {
        if($this->status==1){
            return 'AKTYWNY';
        }else{
            if($this->status==0){
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
        return $this->pwd_sol_data;
    }
    public function setPwdSolData(\DateTime $data) {
        $this->pwd_sol_data=$data;
    }
    public function getLekarz() {
        return $this->lekarz_idlekarz2;
    }
    public function setLekarz($idlekarz) {
        $this->lekarz_idlekarz2=$idlekarz;
    }
    
    public function getRola() {
        return $this->rola;
    }
    
    public function getLekarzImie() {
        return $this->lekarz_imie;
    }
    
    public function getLekarzNazwisko() {
        return $this->lekarz_nazwisko;
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
