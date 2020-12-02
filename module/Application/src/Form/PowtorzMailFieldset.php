<?php

namespace Application\Form;

use Laminas\Form\Fieldset;
use Laminas\Form\Element;

class PowtorzMailFieldset extends Fieldset implements \Laminas\InputFilter\InputFilterProviderInterface{
    
    
    public function init(){
         
            // Add "confirm_password" field
            $this->add([            
                'type'  => Element\Email::class,
                'name' => 'confirm_mail',
                'options' => [
                    'label' => 'Powtórz maila:',
                ],
            ]);
          
    }

    public function getInputFilterSpecification(): array {
        return [
          
            [
             'name' => 'confirm_mail',
                    'required' => true,
                    'filters'  => [                        
                    ],                
                    'validators' => [
                        [
                            'name'    => \Laminas\Validator\Identical::class,
                            'options' => [
                                'token' => [
                                'dodaj_form_lekarz'=> //nie działa
                               ['lekarz_fieldset'=>'mail'] ,
                               ]                                 
                            ],
                        ],
                    ],   
  
            ], 
                ];
          
    }


}


