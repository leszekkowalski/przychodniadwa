<?php

namespace Autoryzacja\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Form\Element;

class OdzyskajHasloForm extends Form implements InputFilterProviderInterface
{
    const CZAS=600;
    const TIME=600;
    
    public function __construct($name='odzyskaj-haslo',$parametry)
    {
        parent::__construct($name,$parametry);
        
        $this->add([
            'name'=>'mail',
            'type'=> Element\Email::class,
             'options' => [
                'label' => 'Wpisz swój mail w celu zresetowania hasła:',
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
            'type'=> Element\Csrf::class,
            'name'=>'csrf',
            'options'=>[    
                 'csrf_options' => [
                          'timeout' => self::CZAS,
                                    ],
                        ]
        ]);
         
         $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Dalej ->',
                'class' => 'btn btn-primary'
            ]
        ]); 
         
        $this->setAttribute('method', 'POST'); 
        
    }
    
   public function getInputFilterSpecification(): array 
   {
     return   [
         [
              'name'=>'mail',
              'filters'=>[
                  ['name'=> \Laminas\Filter\StringTrim::class],
              ],
               'validators'=>[
                  [
                      'name'=> \Laminas\Validator\StringLength::class,
                      'options'=>[
                          'min'=>5,
                          'messages'=> [
                              \Laminas\Validator\StringLength::TOO_SHORT=>
                                        'Minimalna długość wynisi: %min%',
                                       ]
                      ]
                  ], 
                   [
                     'name'  => \Laminas\Validator\EmailAddress::class,
                     'options'=>([
                         'messages'=>array(
                                 \Laminas\Validator\EmailAddress::INVALID_FORMAT=>'validator.email.format',
                                 \Laminas\Validator\EmailAddress::INVALID=>'validator.email.general',
                                 \Laminas\Validator\EmailAddress::INVALID_HOSTNAME=>'validator.email.hostname',
                                 \Laminas\Validator\EmailAddress::INVALID_LOCAL_PART=>'validator.email.local',
                                 \Laminas\Validator\Hostname::UNKNOWN_TLD=>'validator.email.unkown.domain',
                                 \Laminas\Validator\Hostname::LOCAL_NAME_NOT_ALLOWED=>'validator.email.name_not_allowed', 
                         )
                         
                     ]),
                   ],
                   
                ]
            ],  
       ];
   }
}

