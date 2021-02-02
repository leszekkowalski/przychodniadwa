<?php

namespace Moj_rbac\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Validator;
use Laminas\Filter;


class UprawnienieForm extends Form implements \Laminas\InputFilter\InputFilterProviderInterface
{

    private $uprawnienie;
    

    public function __construct($name='uprawnienie-form',$uprawnienie = null)
    {
        $this->uprawnienie=$uprawnienie;
        
        parent::__construct($name);  
   
        $this->dodajInputFilter();
        
        
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'iduprawnienia',
            'attributes'=>[
                    'required'   => false,
            ],  
        ]);
        
        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Podaj nazwę Uprawnienia:',
            ],
            // 'options'=>$parametry,
        ]);
        
        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'opis',
            'options' => [
                'label' => 'Podaj opis Uprawnienia:',
            ],
            'attributes'=>[
                    'rows'   => 3,
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
              'name' => 'iduprawnienia',
            'allow_empty' => true,   
            'filters' => [
              ['name' => Filter\ToInt::class],
              ['name'=> Filter\ToNull::class],
            ],  
           ],
            /////////////////////////
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
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\'\"\-\.]+$/",
                        'messages'=>[
                        Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
            ],  
           ], 
            
            
        ];
       
       return $validators;
    }
    
    protected function dodajInputFilter()
    {
        $inputFilter=$this->getInputFilter();
        if(!$this->uprawnienie)
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
                      'table'=>'uprawnienia',
                      'field'=>'name',
                      'messages'=>[
                      \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND=>'Taka nazwa Uprawnienia już istnieje w bazie',
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
                      'table'=>'uprawnienia',
                      'field'=>'name',
                       'exclude' => [
                             'field' => 'name',
                              'value' => $this->uprawnienie->getName(),
                                     ],
                      'messages'=>[
                      \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND=>'Taka nazwa Uprawnienia już istnieje w bazie',
                      ]
                  ]
                
                ],
  
            ],   
        ]);  
        }
    }

}



