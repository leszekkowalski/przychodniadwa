<?php

namespace Autoryzacja\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Form\Element;
use Laminas\Filter;
use Laminas\Validator;

class imie_nazwiskoFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function __construct() 
    {
        parent::__construct('imie_nazwiskoFieldset');
        
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'iduzytkownik',
            'attributes'=>[
                    'required'   => false,
            ],    
        ]);
        $this->add([
            'type' => Element\Text::class,
            'name' => 'imie',
            'options' => [
                'label' => 'Podaj imię:',
            ],
        ]);
       
         $this->add([
            'type' => Element\Text::class,
            'name' => 'nazwisko',
            'options' => [
                'label' => 'Podaj nazwisko:',
            ],
        ]);
    }
    
    public function getInputFilterSpecification(): array 
    {
       $validators=[
           [
              'name' => 'iduzytkownik',
            'allow_empty' => true,   
            'filters' => [
              ['name' => Filter\ToInt::class],
              ['name'=> Filter\ToNull::class],
            ],  
           ],
           ////////////////////////////////////////////////
           [
             'name'=>'imie',
            'required'=>true,
            'filters'=>[
              ['name' => Filter\StringTrim::class] ,
               ['name'=> Filter\StripTags::class],
            ],
            'validators'=>[
             ['name'=> Validator\NotEmpty::class],
               ['name'=> Validator\StringLength::class,
                'options'=>[
                   'min'=>2,
                    'max'=>30,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\'\-]+$/",
                        'messages'=>[
                        Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                    ],
                    ],
            ],  
           ],
           //////////////////////////////////////////
           [
            'name'=>'nazwisko',
            'required'=>true,
            'filters'=>[
              ['name' => Filter\StringTrim::class] ,
              ['name'=> Filter\StripTags::class],
            ],
            'validators'=>[
             ['name'=> Validator\NotEmpty::class],
               ['name'=> Validator\StringLength::class,
                'options'=>[
                   'min'=>2,
                    'max'=>30,
                    'encoding'=>'UTF-8',
                ],
                   ],
                ['name'=> Validator\Regex::class,
                    'options'=>[
                        'pattern'=>"/[a-zA-ZęóśłżźćńÓŚŁŻŹŃ\s\'\-]+$/",
                        'messages'=>[
                        Validator\Regex::NOT_MATCH=>'Wpisałeś niedozwolone znaki',
                        ]
                                 ],
                ],
            ],  
           ],
           /////////////////////////////////////////////
           ];
        
        return $validators;
    }

}

