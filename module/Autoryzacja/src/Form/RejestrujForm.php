<?php

namespace Autoryzacja\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Form\Element;

class RejestrujForm extends Form implements InputFilterProviderInterface
{
    const TIME=300;
    
    public function __construct($name='rejestruj_form',$parametry) {
        parent::__construct($name,$parametry);
        
        $this->add([
            'name'=>'imie_nazwisko_fieldset',
            'type'=> imie_nazwiskoFieldset::class,
        ]);
        
        $this->add([
            'name'=>'mail_haslo_csrf_fieldset',
            'type'=> LoginFieldset::class,
        ]);
        $this->add([
            'name'=>'powtorz_haslo',
            'type'=> Element\Password::class,
            'options' => [
                'label' => 'Powtórz Hasło:',
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        
         $this->add([
            'name' => 'captcha',
            'type' => Element\Captcha::class,
            'options' => [
                'label' => 'Przepisz obrazek Captcha:',
                'captcha' => new \Laminas\Captcha\Image([
                    'name'    => 'myCaptcha',
                    'messages' => array(
                        'badCaptcha' => 'źle przepisany obrazek'
                    ),
                    'wordLen' => 5,  
                    'timeout' => self::TIME,  
                    'font'    => APPLICATION_PATH.'/public/fonts/arbli.ttf',  
                    'imgDir'  => APPLICATION_PATH.'/public/img/captcha/',  
                    'imgUrl'  => $this->getOption('baseUrl').'/public/img/captcha/',
                    'lineNoiseLevel' => 4,
                    'width'	=> 200,
                    'height' => 70
                ]),
            ]
        ]);
         
          $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Zarejestruj',
                'class' => 'btn btn-primary'
            ]
        ]);
        $this->setAttribute('method', 'POST');
    }
    
    public function getInputFilterSpecification(): array {
       
        return [
            [
                'name' => 'powtorz_haslo',
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Identical::class,
                        'options' => [
                            'token' => ['mail_haslo_csrf_fieldset' => 'haslo'],
                            'messages' => [
                                \Laminas\Validator\Identical::NOT_SAME => 'Hasła nie są takie same',
                            ]
                        ]
                    ]
                ]
            ]
        ]; 
        
    }

}

