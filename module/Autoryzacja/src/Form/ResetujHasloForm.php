<?php

namespace Autoryzacja\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Form\Element;


class ResetujHasloForm extends Form implements InputFilterProviderInterface
{
    
    public function __construct($name='resetuj-haslo') {
        
        parent::__construct($name);
        
         $this->add([
            'type'=> Element\Password::class,
            'name'=>'haslo',
            'attributes'=>[
                    'required'   => true,
            ],
            'options'   =>[
                    'label'=>'Podaj twoje nowe Hasło:'
            ]
        ]);
         
          $this->add([
            'name'=>'powtorz_haslo',
            'type'=> Element\Password::class,
            'options' => [
                'label' => 'Powtórz nowe Hasło:',
            ],
            'attributes' => [
                'required' => true,
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
                'value' => 'Zarejestruj nowe Hasło:',
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
               'name'=>'haslo',
               'required'=>true,
                'filters'=>[
                    [
                        'name'=> \Laminas\Filter\StringTrim::class,
                    ]
                ],
                'validators'=>[
                    [
                        'name'=> \Laminas\Validator\StringLength::class,
                        'options'=>[
                            'min'=>5,
                            'messages'=>[
                            \Laminas\Validator\StringLength::TOO_SHORT=>
                'Minimalna długość hasła wynosi: %min%',
                            ]
                        ]
                    ]  
                ]  
            ], 
            
             [
                'name' => 'powtorz_haslo',
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Identical::class,
                        'options' => [
                            'token' => 'haslo',
                            'messages' => [
                                \Laminas\Validator\Identical::NOT_SAME => 'Hasła nie są takie same',
                            ]
                        ]
                    ]
                ]
            ],
            
            
        ];
        return $validators;
    }

}