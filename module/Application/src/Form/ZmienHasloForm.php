<?php

namespace Application\Form;


use Laminas\Form\Form;
use Laminas\Form\Element;

class ZmienHasloForm extends Form implements \Laminas\InputFilter\InputFilterProviderInterface
{
    
    public function __construct() {
        parent::__construct('zmien-haslo-form');
    }
    
    public function init(){
        
        // Add "password" field
            $this->add([            
                'type'  => Element\Password::class,
                'name' => 'haslo',
                'options' => [
                    'label' => 'Hasło:',
                ],
            ]);
            
            // Add "confirm_password" field
            $this->add([            
                'type'  => Element\Password::class,
                'name' => 'confirm_haslo',
                'options' => [
                    'label' => 'Powtórz hasło:',
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
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Wyślij nowe Hasło:',
            ],
        ]);
               
    }

    public function getInputFilterSpecification(): array {
        return [
            [
               'name'     => 'haslo',
                    'required' => true,
                    'filters'  => [                        
                    ],                
                    'validators' => [
                        [
                            'name'    => \Laminas\Validator\StringLength::class,
                            'options' => [
                                'min' => 6,
                                'max' => 64
                            ],
                        ],
                    ], 
                
                ],
            [
             'name' => 'confirm_haslo',
                    'required' => true,
                    'filters'  => [                        
                    ],                
                    'validators' => [
                        [
                            'name'    => \Laminas\Validator\Identical::class,
                            'options' => [
                                'token' => 'haslo',                            
                            ],
                        ],
                    ],   
  
            ],
            
            
            
            
            
            ];
    }

}

