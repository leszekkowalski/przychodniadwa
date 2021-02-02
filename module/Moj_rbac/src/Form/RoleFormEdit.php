<?php

namespace Moj_rbac\Form;

use Moj_rbac\Form\RolaForm;


class RoleFormEdit extends RolaForm
{
    
   public function __construct($name='role-form-edit', $rola) 
   {
       parent::__construct($name,$rola); 
       $this->dodajElement();
       $this->dodajInputFilter2();
   } 
   
   private function dodajElement()
   {
        $this->add([
    'type' => \Laminas\Form\Element\Checkbox::class,
    'name' => 'checkbox',
    'options' => [
        'label' => 'Zmieniam bez edycji ról do dziedziczenia',
        'use_hidden_element' => true,
        'checked_value' => 'Tak',
        'unchecked_value' => 'Nie',
    ],
    'attributes' => [
         'value' => 'Tak',
    ], 
              ]); 
   }
   
   private function dodajInputFilter2()
   {
       $inputFilter=$this->getInputFilter();
       
         $inputFilter->add([
                'name'     => 'checkbox',
                'required' => false,
                'filters'  => [
                                    
                ],                
                'validators' => [
                    
                ],
            ]); 
   }
}

