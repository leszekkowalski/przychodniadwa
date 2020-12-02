<?php

namespace Application\Form;

use Laminas\Form\Element\Csrf;

use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\Validator\Identical;
use Application\Form\LekarzFieldset;
use Laminas\Form\Element\Hidden;
use Application\Form\Element\Telefon;

class LekarzDodajForm extends Form implements \Laminas\InputFilter\InputFilterProviderInterface
    
{
    public function __construct() {
        parent::__construct('dodaj_form_lekarz');
    }
    
    public function init()
    {
 
         $this->add([
           'name' => 'lekarz_fieldset',
            'type' => LekarzFieldset::class,
            'options' => [
            'use_as_base_fieldset' => true,
        ],
        ]);
       
        $this->add([            
                'type'  => Email::class,
                'name' => 'confirm_mail',
                'options' => [
                    'label' => 'Powtórz maila:',
                ],
            ]);
         
         
          // Add "confirm_password" field
      //   $this->add([
      //     'name' => 'powtorz_mail',
      //      'type' => PowtorzMailFieldset::class,
     //   ]);
  
        $this->add([
            'type' => Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Wpisz nowego Lekarza',
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
                               'lekarz_fieldset'=>'mail' ,
                               ]                                 
                            ],
                        ],
                    ],   
  
            ],
                ];
          
    }

}