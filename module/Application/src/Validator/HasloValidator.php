<?php

namespace Application\Validator;


use Laminas\Validator\AbstractValidator;


class HasloValidator extends AbstractValidator{
    
    const DLUGOSC='dlugosc';
    const DUZALITERA='duza';
    const MALALITERA='mala';
    const CYFRA='cyfra';
    const ZNAKSPECJALNY='specjalny';
    
    protected $messageTemplates=[
       self::DLUGOSC=>"'%value%' musi składać się z co najmniej 4 znaków (w tym: 1 duża litera, 1 mała litera, 1 cyfra i 1 znak specjalny",
        
    ];




    public function isValid($value): bool {
        return true;
    }

}

