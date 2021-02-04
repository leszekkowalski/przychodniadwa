<?php

namespace Moj_rbac\Form;

use Laminas\Form\Form;
use Laminas\Form\Fieldset;


class EdytujUprawnieniaForm extends Form
{
    
    public function __construct() 
    {
        parent::__construct('edutuj-uprawnienia');
        
        $this->setAttribute('method', 'POST');
        
        $this->addElements();
        $this->addInputFilter();
        
    }
   
    public function addElements() 
    {
        $fieldset=new Fieldset('uprawnienie');
        $this->add($fieldset);
        
         // Add the Submit button
        $this->add([
            'type'  => \Laminas\Form\Element\Submit::class,
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Zapisz',
                'id' => 'submit',
            ],
        ]);
        
       // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
      
    }
    
    public function addUprawnienieRola($name,$label, $disabled=false)
    {
        $this->get('uprawnienie')->add([
            'type'=> \Laminas\Form\Element\Checkbox::class,
            'name'=>$name,
            'attributes'=>[
                'id'=>$name,
                'disabled'=>$disabled,
            ],
            'options'=>[
            'label'=>$label,
             
            ], 
        ]);
        
        
        $this->getInputFilter()->get('uprawnienie')->add([
             'name'=>$name,
             'required'=>false,
                'filters'  => [                    
                ],                
                'validators' => [
                    ['name' => \Laminas\I18n\Validator\IsInt::class],
                ],
            
        ]);
    }
    
    private function addInputFilter()
    {
        $inputFilter=$this->getInputFilter();
    }
}

