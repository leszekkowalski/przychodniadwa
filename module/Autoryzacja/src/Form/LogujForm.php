<?php

namespace Autoryzacja\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class LogujForm extends Form implements InputFilterProviderInterface
{
    const LOGIN_FIELDSET='login_fieldset';
    
    public function __construct($name='login_form_uzytkownik')
    {
        parent::__construct($name);
        
        $this->add([
            'type'=> LoginFieldset::class,
            'name'=>self::LOGIN_FIELDSET,
        ]);
        
         // Add "redirect_url" field
        $this->add([            
            'type'  => 'hidden',
            'name' => 'redirect_url'
        ]);
        
        $this->add([
            'type'=> \Laminas\Form\Element\Submit::class,
            'name'=>'submit',
            'attributes'=>[
                'value'=>'Zaloguj: ',
            ]
        ]);
        
        $this->setAttribute('method', 'POST');
    }

    public function getInputFilterSpecification(): array
    {
       $validators=[
            [
             'name'     => 'redirect_url',
                'required' => false,
                'filters'  => [
                    ['name'=> \Laminas\Filter\StringTrim::class ]
                ],                
                'validators' => [
                    [
                        'name'    => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 2048
                        ]
                    ],
                ],   
            ]
                  ];
       
       return $validators;
    }

}

