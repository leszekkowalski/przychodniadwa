<?php

namespace Kalendarz\Form;

use Kalendarz\Form\WydarzenieFieldset;
use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\InputFilter\InputFilterProviderInterface;

class WydarzenieForm extends Form implements InputFilterProviderInterface
{
   public $param;
    
   public function __construct($name='wydarzenie-form',$param)
   {

        parent::__construct($name,$param);
       // $this->param=$param;
        $this->setAttribute('method', 'post');
                
       // $this->addElements($param);
        //$this->addInputFilter($param);   
   //} 
   
  // protected function addElements($param) 
  // {
      
    if($param['wpisz_czy_edytuj']==='wpisz')
    {
        $napis='Dokonujesz wpisu dla ';
                if(($param['lekarz']) instanceof \Application\Model\Lekarz) 
                {
                    $napis.= $param['lekarz']->getImie().' '.$param['lekarz']->getNazwisko();
                }else{
                    $napis.=' wszystkich lekarzy';
                }
      $this->add([
           'type' => Element\Checkbox::class,
             'name' => 'checkbox',
                 'options' => [
                    'label' => $napis,
                    'use_hidden_element' => true,
                     'unchecked_value' => 'no',
                    'checked_value' => 'yes',                   
    ],
                     'attributes' => [
                        'value' => 'yes',
                        
                             ],
          
      ]);  
    }
    
    if($param['wpisz_czy_edytuj']==='edytuj')
    {
        
    }
    
    
        $this->add([
            'name'=>'wydarzenie_fieldset',
            'type'=> WydarzenieFieldset::class,
            'options' => [
            'use_as_base_fieldset' => true,
            'parametry'=>$param,
        ],
        //    'options'=>$param,
        ]);
       
       
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'attributes' => [],
            'options' => [                
                'csrf_options' => [
                     'timeout' => 600
                ]
            ],
        ]);
        
        // Add the submit button
        $this->add([
            'type'  => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Wpisz do bazy',
               'class' => 'btn btn-primary'
            ],
        ]);  
   }
   
   protected function addInputFilter($param) 
   {
       $inputFilter = new \Laminas\InputFilter\InputFilter();       
       $this->setInputFilter($inputFilter);
   }

    public function getInputFilterSpecification(): array {
        
      //  if($param['wpisz_czy_edytuj']==='wpisz'){
        $validator=[
            
            [
             'name'=>'checkbox',
             'required'=>false,
                'filters'  => [                    
                ],                
                'validators' => [ 
                ],
            ]
        ];
      //  }else{
       //     $validator=[];
      //  }
    
    return $validator;
    }

}
