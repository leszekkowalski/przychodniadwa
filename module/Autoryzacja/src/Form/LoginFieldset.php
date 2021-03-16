<?php

namespace Autoryzacja\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Form\Element;

class LoginFieldset extends Fieldset implements InputFilterProviderInterface
{
    const CZAS=600;
    const ELEMENT_MAIL='mail';
    const ELEMENT_PASSWORD='haslo';
    const ELEMENT_CSRF='csrf_uzytkownik_loguj';
    
    public function __construct()
    {
        parent::__construct('login_fieldset');
                   
        $this->add([
            'type'=> Element\Email::class,
            'name'=>self::ELEMENT_MAIL,
            'attributes'=>[
                    'required'   => true,
            ],
            'options'   =>[
                    'label'=>'Twój Email:'
            ]
        ]);
        
        $this->add([
            'type'=> Element\Password::class,
            'name'=>self::ELEMENT_PASSWORD,
            'attributes'=>[
                    'required'   => true,
            ],
            'options'   =>[
                    'label'=>'Twoje Hasło:'
            ]
        ]);
        
        $this->add([
            'type'=> Element\Csrf::class,
            'name'=>self::ELEMENT_CSRF,
            'options'=>[
                 'csrf_options' => [
                          'timeout' => self::CZAS,
                     ],
            ]
            
        ]);
    }
    
    
    public function getInputFilterSpecification(): array {
        
        $validators=[
            [
              'name'=>self::ELEMENT_MAIL,
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
                   
                ],
            ],
            [
               'name'=>self::ELEMENT_PASSWORD,
               'required'=>true,
                'filters'=>[
                    [
                        'name'=> \Laminas\Filter\StringTrim::class,
                    ]
                ],
                'validators'=>[
                    [
                        'name'=> \Laminas\Validator\StringLength::class,
                        'options'=>[
                            'min'=>5,
                            'messages'=>[
                            \Laminas\Validator\StringLength::TOO_SHORT=>
                'Minimalna długość hasła wynosi: %min%',
                            ]
                        ]
                    ]  
                ]  
            ],
        ];
 //dodaje extra validator DB (bazy danych) do formularza rejestracji omijajac  formularz logowania 

           if(!empty($this->getOption('dbAdapter'))) {
              $validators[0]['validators'][0] =[
                  'name'=> \Laminas\Validator\Db\NoRecordExists::class,
                  'options'=>array(
                      'adapter'=>$this->getOption('dbAdapter'),
                      'table'=>'uzytkownik',
                      'field'=>'mail',
                      'messages'=>array(
                      \Laminas\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND=>'Taki adres email już istnieje w bazie',
                      )
                  )
              ];
              
           }
         
                
      //     var_dump($this->getOption('dbAdapter'));
           return $validators;
    }

}

