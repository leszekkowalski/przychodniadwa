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

class LekarzDodajForm extends Form 
{
    
    public function init()
    {
     //   parent::__construct('lekarz-form');

         
         $this->add([
           'name' => 'lekarz_fieldset',
            'type' => LekarzFieldset::class,
            'options' => [
            'use_as_base_fieldset' => true,
        ],
        ]);
       
    
  
        $this->add([
            'type' => Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Wpisz nowego Lekarza',
            ],
        ]);
    }
    
}