<?php
declare(strict_types=1);
namespace Application\Model;

use  Laminas\Escaper\Escaper;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputFilter;
use Application\Validator\PeselValidator;
use Application\Validator\HasloValidator;
use Laminas\Filter\StringTrim;
use Laminas\Db\Adapter\AdapterInterface;
use DomainException;
use Laminas\Filter;


class Lekarz implements InputFilterAwareInterface{
    
    const Mężczyzna  = 1;
    
    const Kobieta = 0;
    
    public $inputFilter;
    
    protected $idlekarz;
    
    protected $imie;
    
    protected $nazwisko;
    
    protected $pesel;
    
    protected $plec;
    
    protected $haslo;
    
    protected $data_haslo;
    
    protected $zdjecie;
    
    protected $mail;
    
    protected $specjalnosc;
    
    protected $telefon;
    
    protected $opis;
    
    protected $adapterInterface;


    public function __construct($imie=null,$nazwisko=null) {
        $this->imie=$imie;
        $this->nazwisko=$nazwisko;
       $this->adapterInterface=$this->pobierzAdapter();
    }
   
   public function getIdlekarz(): int {
       return (int)$this->idlekarz;
   } 
   
   public function setIdlekarz($param) {
       $this->idlekarz=$param;
   }
    
   public function getImie(): string {
       return $this->imie;
   } 
   
   public function setImie($imie) {
       $this->imie=$imie;
   }
   
   public function getNazwisko(): string {
       return $this->nazwisko;
   } 
   
   public function setNazwisko($nazwisko) {
       $this->nazwisko=$nazwisko;
   }
   
   public function getPesel(): string {
       return $this->pesel;
   } 
   
   public function setPesel($pesel) {
       $this->pesel=$pesel;
   }
   
   public function getPlec() {
       return $this->plec;
   } 
   
   public function setPlec($plec) {
       $this->plec=$plec;
   }
   
   public function getPlecAsString():string {
       $plec=$this->getPlec();
       if($plec==1){
           return 'Mężczyzna';
       }else if($plec==0){
           return 'Kobieta';
       }else{
           return 'Nie zidentyfikowano - błędny';
       }
   }
   
   public function setHaslo($haslo) {
       $this->haslo=$haslo;
   }
   
   public function getHaslo(): string {
       return $this->haslo;
   }
   
   public function setData_haslo($data_haslo) {
       $this->data_haslo=$data_haslo;
   }
   
   public function getData_haslo() : string  {
       return $this->data_haslo;
   }
   
   public function setZdjecie($param) {
       $this->zdjecie=$param;
   }

    public function getZdjecie() {
        return $this->zdjecie;
    }
    
    public function setMail($param) {
        $this->mail=$param;
    }
    
     public function getMail() : string {
         return $this->mail;
     }  
     public function getSpecjalnosc() : string {
         return $this->specjalnosc;
     }
     
      public function setSpecjalnosc($param) {
        $this->specjalnosc=$param;
    }
       
      public function setTelefon($param) {
        $this->telefon=$param;
    }
       
     public function getTelefon() : string {
         return $this->telefon;
     }
      
      public function setOpis($param) {
       $this->opis=(htmlspecialchars($param));
       // $this->opis=$this->escaper->escapeHtml($param);
    }
       
     public function getOpis() : string {
         return htmlspecialchars_decode($this->opis);
     }

     public function getArrayCopy()
    {
       return [
           'idlekarz'=> $this->getIdlekarz(),
           'imie' => $this->getImie(),
           'nazwisko' => $this->getNazwisko(),
           'pesel' => $this->getPesel(),
           'plec'=>$this->plec->getPlec(),
            'mail' => $this->getMail(),
           'specjalnosc'=>$this->getSpecjalnosc(),
           'telefon' => $this->getTelefon(),
           'zdjecie' => $this->getZdjecie(),
           'opis'=>$this->getOpis(),
       ];
    }
    
    public function exchangeArray(array $data)
    {
        $this->idlekarz     = !empty($data['idlekarz']) ? $data['idlekarz'] : null;
        $this->imie = !empty($data['imie']) ? $data['imie'] : null;
        $this->nazwisko  = !empty($data['nazwisko']) ? $data['nazwisko'] : null;
        $this->pesel  = !empty($data['pesel']) ? $data['pesel'] : null;
    }
    
     protected function pobierzAdapter() {
        
    $adapter = new \Laminas\Db\Adapter\Adapter([
    'driver'   => 'Mysqli',
    'database' => 'przychodnia',
    'username' => USERNAME,
    'password' => PASSWORD,
]);

       return $adapter;
    }
    

     public function getInputFilter(): InputFilterInterface {
        
        if ($this->inputFilter) {
             return $this->inputFilter;
         }
         $inputFilter = new InputFilter('');
         
    
          $inputFilter->add([
            'name' => 'idlekarz',
            'allow_empty' => true,   
            'filters' => [
              ['name' => Filter\ToInt::class],
              ['name'=> Filter\ToNull::class],
            ],
        ]);
             ////////////////////////////////////
        $inputFilter->add([
            'name'=>'imie',
            'require'=>true,
            'filters'=>[
              ['name' => StringTrim::class] ,
               ['name'=> \Laminas\Filter\StripTags::class],
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
                        'pattern'=>"/[a-zA-ZąśłżźńćęŚ ŻĆŁ]+$/",
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
               ['name'=> \Laminas\Filter\StripTags::class] ,
               ['name'=> \Laminas\Filter\StringTrim::class] ,
            ],
            'validators'=>[
                ['name'=> \Laminas\Validator\NotEmpty::class],
                ['name'=> \Laminas\Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZąśłżńźćęŚŻĆŁ -]+$/",
                        'messages'=>[
                        \Laminas\Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
               ['name'=> \Laminas\Validator\StringLength::class,
                'options'=>[
                    'min'=>2,
                    'max'=>50,
                    'encoding'=>'UTF-8',
                           ]
                   ],
            ],
        ]);

     
        /////////////////////////////////////////////////////////////////
        if(!$this->pesel){
        $inputFilter->add([
            'name'=>'pesel',
            'require'=>true,
            'filters'=>[
             ['name'=> \Laminas\Filter\StringTrim::class]   ,
            ],
            'validators'=>[
          //  ['name'=> PeselValidator::class],
                 ['name'=> \Laminas\Validator\Db\NoRecordExists::class,
                   'options'=>[
                        'table'   => 'lekarz',
                         'field'   => 'pesel',
                        'adapter' => $this->adapterInterface,
                       // 'adapter' => $this->pobierzAdapter(),
                        'messages' => array(
                         \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Taki PESEL  już istnieje w bazie'
                     )
                    ],
                    
                ],
            ],
            
        ]);
        }else{
           $inputFilter->add([
            'name'=>'pesel',
            'require'=>true,
            'filters'=>[
            ['name'=> \Laminas\Filter\StringTrim::class]   ,
            ],
            'validators'=>[
           //   ['name'=> PeselValidator::class],
                 ['name'=> \Laminas\Validator\Db\NoRecordExists::class,
                   'options'=>[
                        'table'   => 'lekarz',
                         'field'   => 'pesel',
                         'adapter' => $this->adapterInterface,
                      // 'adapter' => $this->pobierzAdapter(),
                       'exclude' => [
                              'field' => 'pesel',
                               'value' => $this->pesel,
                          ],
                        'messages' => array(
                         \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Taki PESEL  już istnieje w bazie'
                     )
                    ],
                    
                ],
            ],
               
        ]); 
        }
            
        ////////////////////////////////////////  
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
                        'table'   => 'lekarz',
                         'field'   => 'mail',
                        'adapter' => $this->adapterInterface,
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
                        'table'   => 'lekarz',
                         'field'   => 'mail',
                         'adapter' => $this->adapterInterface,
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
        ///////////////////////////////////////
               $inputFilter->add([
            'name'=>'specjalnosc',
             'required' => false,
            'filters'=>[
              ['name'=> StringTrim::class] ,
               ['name'=> Filter\StripTags::class],
            ],
            'validators'=>[
               ['name'=> \Laminas\Validator\StringLength::class,
                'options'=>[
                    'max'=>100,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> \Laminas\Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZąśłżźńćęŚŻ ĆŁ,]+$/",
                        'messages'=>[
                        \Laminas\Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
            ],
        ]);
        //////////////////////////////////////////   
              
          $inputFilter->add([
            'name'=>'telefon',
             'required' => false,
            'filters'=>[
              ['name'=> StringTrim::class] ,
               ['name'=> Filter\StripTags::class],
            ],
           'validators'=>[
             //  ['name'=> \Application\Form\Element\Telefon::class,],
                ['name'=> \Laminas\Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/^\+{1}[0-9]{2}\s?\d{9}$/",
                        'messages'=>[
                        \Laminas\Validator\Regex::NOT_MATCH=>'Proszę wstawić telefon w formacie +48607345765ee',
                        ]
                   ],
                    ],
            ],
        ]);   
                

   
          /////////////////////////////////////////
             $inputFilter->add([
            'name'=>'opis',
            'required' => false,
            'filters'=>[
              ['name'=> StringTrim::class] ,
            ],
            'validators'=>[
                          ]
        ]);
        
        /////////////////////////////////////////
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
 
