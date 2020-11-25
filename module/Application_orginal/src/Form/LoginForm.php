<?php
namespace Application\Form;

use Laminas\Form\Form;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilter;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class LoginForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('login-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add "email" field
        $this->add([            
            'type'  => \Laminas\Form\Element\Email::class,
            'name' => 'mail',
            'options' => [
                'label' => 'Twój E-mail',
            ],
        ]);
        
        // Add "password" field
        $this->add([            
            'type'  => \Laminas\Form\Element\Password::class,
            'name' => 'haslo',
            'options' => [
                'label' => 'Hasło',
            ],
        ]);
        
        // Add "remember_me" field
        $this->add([            
            'type'  => \Laminas\Form\Element\Checkbox::class,
            'name' => 'remember_me',
            'options' => [
                'label' => 'Zapamiętaj mnie',
            ],
        ]);
        
      
        
        // Add the CSRF field
        $this->add([
            'type' => \Laminas\Form\Element\Csrf::class,
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => \Laminas\Form\Element\Submit::class,
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Zaloguj się',
                'id' => 'submit',
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create main input filter
        $inputFilter = $this->getInputFilter();        
                
        // Add input for "email" field
        $inputFilter->add([
                'name'     => 'mail',
                'required' => true,
                'filters'  => [
                  //  ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name' => \Laminas\Validator\EmailAddress::class,
                        'options' => [
                            'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                            'useMxCheck' => false,                            
                        ],
                    ],
                ],
            ]);     
        
        // Add input for "password" field
        $inputFilter->add([
                'name'     => 'haslo',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);     
        
        // Add input for "remember_me" field
        $inputFilter->add([
                'name'     => 'remember_me',
                'required' => false,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => \Laminas\Validator\InArray::class,
                        'options' => [
                            'haystack' => [0, 1],
                        ]
                    ],
                ],
            ]);
        
     
        
        
    }        
}



