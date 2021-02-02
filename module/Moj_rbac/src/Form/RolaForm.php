<?php

namespace Moj_rbac\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Validator;
use Laminas\Filter;


class RolaForm extends Form implements \Laminas\InputFilter\InputFilterProviderInterface
{

    private $rola;
    

    public function __construct($name='rola-form',$rola = null)
    {
        $this->rola=$rola;
        
        parent::__construct($name);  
   
        $this->dodajInputFilter();
        
        
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'idrola',
            'attributes'=>[
                    'required'   => false,
            ],  
        ]);
        
        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Podaj nazwę Roli:',
            ],
            // 'options'=>$parametry,
        ]);
        
        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'opis',
            'options' => [
                'label' => 'Podaj opis Roli:',
            ],
            'attributes'=>[
                    'rows'   => 3,
            ],
        ]);
        
        $this->add([
            'type'=> Element\MultiCheckbox::class,
            'name'=>'multi_checkbox',
            'options'=>[
                'label'=>'Wybierz opcjonalnie role do dziedziczenia:',
                'disable_inarray_validator' => true,
                 ],  
            
        ]);
        
         $this->add([
            'type'=> Element\Csrf::class,
            'name'=>'csrf',
            'options'=>[
                 'csrf_options' => [
                          'timeout' => 600,
                     ],
            ]
        ]);
         
          $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Zarejestruj w systemie:',
                'class' => 'btn btn-primary'
            ]
        ]);
          
        $this->setAttribute('method', 'POST');
    }
    
    public function getInputFilterSpecification(): array
    {
       $validators=
        [
           [
              'name' => 'idrola',
            'allow_empty' => true,   
            'filters' => [
              ['name' => Filter\ToInt::class],
              ['name'=> Filter\ToNull::class],
            ],  
           ],
           //////////////////////////////////////////////// 
           /**
            [
             'name'=>'name',
            'required'=>true,
            'filters'=>[
              ['name' => Filter\StringTrim::class] ,
               ['name'=> Filter\StripTags::class],
            ],
            'validators'=>[
             ['name'=> Validator\NotEmpty::class],
               ['name'=> Validator\StringLength::class,
                'options'=>[
                   'min'=>2,
                    'max'=>30,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\-]+$/",
                        'messages'=>[
                        Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
                
                  ['name'=> \Laminas\Validator\Db\NoRecordExists::class,
                  'options'=>[
                      'adapter'=> \Application\Model\Lekarz::pobierzAdapter(),
                      'table'=>'rola',
                      'field'=>'name',
                      'messages'=>[
                      \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND=>'Taka nazwa Roli już istnieje w bazie',
                      ]
                  ]
                
                ],
                [
                  'name'  => Validator\InArray::class,
                    'options'=>[
                        'haystack'=> \Moj_rbac\Model\Rola::$tablicaRol,
                        'messages'=>[
                        Validator\InArray::NOT_IN_ARRAY=>'Zdefiniowana Rola nie wystepuja wśród możliwych ról do wpisania',
                        ]
                    ]
                    
                ],
                
            ],  
           ],
            
            */
            
           //////////////////////////////////////////
          [
             'name'=>'opis',
            'required'=>true,
            'filters'=>[
              ['name' => Filter\StringTrim::class] ,
               ['name'=> Filter\StripTags::class],
            ],
            'validators'=>[
             ['name'=> Validator\NotEmpty::class],
                ['name'=> Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\'\-\.]+$/",
                        'messages'=>[
                        Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
            ],  
           ], 
            
            
              [
              'name' => 'multi_checkbox',
            'allow_empty' => true,   
            'filters' => [
              ['name' => Filter\ToInt::class],
             // ['name'=> Filter\ToNull::class],
            ],  
              'validators'=>[
           
            ],     
           ],
            
        ];
       
       //dodaje extra validator DB (bazy danych) do formularza rejestracji omijajac  formularz logowania 
        /**
           if(!empty($this->getOption('dbAdapter'))) {
              $validators[0]['validators'][0] =[
                  'name'=> \Laminas\Validator\Db\NoRecordExists::class,
                  'options'=>array(
                      'adapter'=>$this->getOption('dbAdapter'),
                      'table'=>'rola',
                      'field'=>'name',
                      'messages'=>array(
                      \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND=>'Taka nazwa Roli już istnieje w bazie',
                      )
                  )
              ];
           }
         * 
         */
       return $validators;
    }
    
    protected function dodajInputFilter()
    {
        $inputFilter=$this->getInputFilter();
        if(!$this->rola)
        {
        $inputFilter->add([
             'name'=>'name',
            'required'=>true,
            'filters'=>[
              ['name' => Filter\StringTrim::class] ,
               ['name'=> Filter\StripTags::class],
            ],
            'validators'=>[
             ['name'=> Validator\NotEmpty::class],
               ['name'=> Validator\StringLength::class,
                'options'=>[
                   'min'=>2,
                    'max'=>30,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\-]+$/",
                        'messages'=>[
                        Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
                
                  ['name'=> \Laminas\Validator\Db\NoRecordExists::class,
                  'options'=>[
                      'adapter'=> \Application\Model\Lekarz::pobierzAdapter(),
                      'table'=>'rola',
                      'field'=>'name',
                      'messages'=>[
                      \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND=>'Taka nazwa Roli już istnieje w bazie',
                      ]
                  ]
                
                ],
                [
                  'name'  => Validator\InArray::class,
                    'options'=>[
                        'haystack'=> \Moj_rbac\Model\Rola::$tablicaRol,
                        'messages'=>[
                        Validator\InArray::NOT_IN_ARRAY=>'Zdefiniowana Rola nie wystepuja wśród możliwych ról do wpisania',
                        ]
                    ]
                    
                ],
                
            ],   
        ]);
        }else{
          $inputFilter->add([
             'name'=>'name',
            'required'=>true,
            'filters'=>[
              ['name' => Filter\StringTrim::class] ,
               ['name'=> Filter\StripTags::class],
            ],
            'validators'=>[
             ['name'=> Validator\NotEmpty::class],
               ['name'=> Validator\StringLength::class,
                'options'=>[
                   'min'=>2,
                    'max'=>30,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\-]+$/",
                        'messages'=>[
                        Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
                
                  ['name'=> \Laminas\Validator\Db\NoRecordExists::class,
                  'options'=>[
                      'adapter'=> \Application\Model\Lekarz::pobierzAdapter(),
                      'table'=>'rola',
                      'field'=>'name',
                       'exclude' => [
                             'field' => 'name',
                              'value' => $this->rola->getName(),
                                     ],
                      'messages'=>[
                      \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND=>'Taka nazwa Roli już istnieje w bazie',
                      ]
                  ]
                
                ],
                [
                  'name'  => Validator\InArray::class,
                    'options'=>[
                        'haystack'=> \Moj_rbac\Model\Rola::$tablicaRol,
                        'messages'=>[
                        Validator\InArray::NOT_IN_ARRAY=>'Zdefiniowana Rola nie wystepuja wśród możliwych ról do wpisania',
                        ]
                    ]
                    
                ],
                
            ],   
        ]);  
        }
    }

}

