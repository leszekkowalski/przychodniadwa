<?php
declare(strict_types=1);

namespace Kalendarz\Entity;

use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;

class Wydarzenie implements InputFilterAwareInterface
{
    protected $idwydarzenie;
     /**
     *
     * @var int lub null -  klucz obcy - powiazanie z identifikatorem tabeli Lekarz lub "null" jesli 
     * wydarzenie będzie wpisywane dla wszystkich lekarzy
     */
    protected $wydarzenie_idlekarz;
    /**
     *
     * @var string - data w formacie YYYY-MM-DD  koniec wydarzenia
     */
    protected $wydarzenie_data;
    
    protected $wydarzenie_start;
    /**
     *
     * @var string - czas w formacie GG:MM:SS  koniec wydarzenia
     */
    protected $wydarzenie_koniec;
    
    protected $wydarzenie_tytul;
    
    protected $wydarzenie_opis;
    
    
    
    protected $inputFilter;


    public function __construct() 
    {
        
    }
    
    public function getIdwydarzenie()
    {
       return $this->idwydarzenie; 
    }
    
    public function getWydarzenie_data()
    {
       return $this->wydarzenie_data; 
    }
    
    public function getWydarzenie_tytul()
    {
       return $this->wydarzenie_tytul; 
    }
    
    public function getWydarzenie_start()
    {
       return $this->wydarzenie_start; 
    }

    public function getWydarzenie_koniec()
    {
       return $this->wydarzenie_koniec; 
    }
    
    public function getWydarzenie_opis()
    {
       return $this->wydarzenie_opis; 
    }
    
    
    public function getInputFilter(): InputFilterInterface {
        
        if($this->inputFilter)
        {
            return $this->inputFilter;
        }
        
        $inputFilter=new \Laminas\InputFilter\InputFilter();
        
        $inputFilter->add([
              'name'=>'idwydarzenie',
            'required'=>false,
            'filters'=>[
                ['name'=> \Laminas\Filter\ToInt::class],
            ]
        ]);
       
         $inputFilter->add([
              'name'=>'wydarzenie_idlekarz',
            'required'=>false,
            'filters'=>[
                ['name'=> \Laminas\Filter\ToInt::class],
            ]
        ]);
        
         $inputFilter->add([
              'name'=>'wydarzenie_data',
            'required'=>true,
            'filters'=>[
                ['name'=> \Laminas\Filter\StringTrim::class],
                ['name'=> \Laminas\Filter\StripTags::class],
            ],
                 'validators'=>[
            ['name'=> \Laminas\Validator\NotEmpty::class],
             ['name'=> \Laminas\Validator\Date::class],
           ],
        ]); 
         
        $inputFilter->add([
              'name'=>'wydarzenie_start',
            'required'=>true,
            'filters'=>[
                ['name'=> \Laminas\Filter\StringTrim::class],
                ['name'=> \Laminas\Filter\StripTags::class],
            ],
                 'validators'=>[
            ['name'=> \Laminas\Validator\NotEmpty::class],
             ['name'=> \Laminas\Validator\Date::class,
                 'options'=>[
                       'format'=>'G:i:s',
                     'messages'=>[
                     \Laminas\Validator\Date::FALSEFORMAT=>'Niepoprawny format czasu',
                       ]
                   ],
                 ],
           ],
        ]); 
        
           $inputFilter->add([
              'name'=>'wydarzenie_koniec',
            'required'=>true,
            'filters'=>[
                ['name'=> \Laminas\Filter\StringTrim::class],
                ['name'=> \Laminas\Filter\StripTags::class],
            ],
                 'validators'=>[
            ['name'=> \Laminas\Validator\NotEmpty::class],
             ['name'=> \Laminas\Validator\Date::class,
                 'options'=>[
                       'format'=>'G:i:s',
                     'messages'=>[
                     \Laminas\Validator\Date::FALSEFORMAT=>'Niepoprawny format czasu',
                       ]
                   ],
                 ],
           ],
        ]); 
           
                   $inputFilter->add([
           'name'=>'wydarzenie_tytul',
           'required'=>true,
           'filters'=>[
             ['name'=> \Laminas\Filter\StringTrim::class] ,
              ['name'=> \Laminas\Filter\StripTags::class],
           ],
           'validators'=>[
            ['name'=> \Laminas\Validator\NotEmpty::class],
              ['name'=> \Laminas\Validator\StringLength::class,
               'options'=>[
                  'min'=>3,
                   'max'=>45,
                   'encoding'=>'UTF-8',
               ],
                  ],
               ['name'=> \Laminas\Validator\Regex::class,
                   'options'=>[
                       'pattern'=>"/[0-9a-zA-ZąśłżźńćęŚŻĆŁ,.\s]+$/",// \w - word charakter ze spacja i _ oraz polsie znaki
                       'messages'=>[
                       \Laminas\Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                       ]
                   ],
                   ],
           ],
       ]);   
                   
        $inputFilter->add([
           'name'=>'wydarzenie_opis',
           'required'=>true,
           'filters'=>[
             ['name'=> \Laminas\Filter\StringTrim::class] ,
              ['name'=> \Laminas\Filter\StripTags::class],
           ],
           'validators'=>[
            ['name'=> \Laminas\Validator\NotEmpty::class],
              ['name'=> \Laminas\Validator\StringLength::class,
               'options'=>[
                  'min'=>3,
                   'max'=>45,
                   'encoding'=>'UTF-8',
               ],
                  ],
               ['name'=> \Laminas\Validator\Regex::class,
                   'options'=>[
                       'pattern'=>"/[0-9a-zA-ZąśłżźńćęŚŻĆŁ,.\s]+$/",// \w - word charakter ze spacja i _ oraz polsie znaki
                       'messages'=>[
                       \Laminas\Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                       ]
                   ],
                   ],
           ],
       ]);           
         
        
        $this->inputFilter=$inputFilter;
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter): InputFilterAwareInterface {
        throw new DomainException(sprintf(
           '%s nie ma pozwolenia na użycie filtra',
           __CLASS__
       ));
    }

}

