<?php

namespace Application\Form\Element;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Regex;
use Laminas\Validator\ValidatorInterface;

class Telefon extends Element implements InputProviderInterface
{
    
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected function getValidator()
    {
        if(null===$this->validator){
            $validator=new Regex('/^\+{1}[0-9]{2}\s?\d{9}$/');
            $validator->setMessage(
                'Proszę wstawić telefon w formacie +48607345765',
                Regex::NOT_MATCH
            );
            $this->validator=$validator;
        }
       return $this->validator; 
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator=$validator;
        return $this;
    }

    public function getInputSpecification()
    {
        return[
                'name' => $this->getName(),
                'required'=>false,
                'filters' => [
                    'name' => StringTrim::class
                ],
                'validators' => [
                    $this->getValidator(),
                ],
        ];
    }
}