<?php

namespace Kalendarz\Form;

use Kalendarz\Form\WydarzenieFieldset;
use Laminas\Form\Form;
use Laminas\Form\Element;

class WydarzenieForm extends Form
{
   public function __construct($name='wydarzenie-form',$param)
   {

        parent::__construct($name,$param);
     
        $this->setAttribute('method', 'post');
                
        $this->addElements($param);
        $this->addInputFilter($param);   
   } 
   
   protected function addElements($param) 
   {
      
     //  var_dump($param);
        $this->add([
            'name'=>'wydarzenie_fieldset',
            'type'=> WydarzenieFieldset::class,
            'options' => [
            'use_as_base_fieldset' => true,
            'parametry'=>$param,
        ],
           // 'options'=>$param,
        ]);
       
       
        $this->add([
            'type'  => 'csrf',
            'name' => Element\Csrf::class,
            'attributes' => [],
            'options' => [                
                'csrf_options' => [
                     'timeout' => 600
                ]
            ],
        ]);
        
        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => Element\Submit::class,
            'attributes' => [                
                'value' => 'Wpisz do bazy',
                'id' => 'submitbutton',
            ],
        ]);  
   }
   
   protected function addInputFilter($param) 
   {
       $inputFilter = new \Laminas\InputFilter\InputFilter();       
       $this->setInputFilter($inputFilter);
   }
}
