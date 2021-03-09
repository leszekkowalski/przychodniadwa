<?php

namespace Kalendarz\Form;

use Laminas\Form\Fieldset;
use Laminas\Form\Element;
use Laminas\Hydrator\ReflectionHydrator;
use Kalendarz\Entity\Wydarzenie;
use Laminas\InputFilter\InputFilterProviderInterface;

class WydarzenieFieldset extends Fieldset implements InputFilterProviderInterface {
    
    
    public function __construct() {
          
        parent::__construct('wydarzenie-fieldset'); 
        
        $this->setHydrator(new ReflectionHydrator());
        $this->setObject(new Wydarzenie());
       
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'idwydarzenie',
        ]);
        
        
         $this->add([
            'type' => Element\Hidden::class,
            'name' => 'wydarzenie_idlekarz',
        ]);
         
        $this->add([
            'type' => Element\Text::class,
            'name' => 'wydarzenie_tytul',
            'options' => [
                'label' => 'Podaj tytuł wydarzenia:',
            ],
        ]);
        
        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'wydarzenie_opis',
            'options' => [
                'label' => 'Podaj opis wydarzenia:',
            ],
        ]);
        
        $this->add([
         'type'=> Element\Time::class,
            'name'=>'wydarzenie_start',
             'options' => [
             'label'  => 'Początek wydarzenia',
              'format' => 'H:i'
                 ],
            'attributes' => [
               'min' => '10:00:00',
              'max' => '18:50:00',
              'step' => '60', // minutes; default step interval is 1 min
          ],
              ]);
        
         $this->add([
         'type'=> Element\Time::class,
            'name'=>'wydarzenie_koniec',
             'options' => [
             'label'  => 'Koniec wydarzenia',
              'format' => 'H:i'
                 ],
            'attributes' => [
               'min' => '10:14:00',
              'max' => '19:00:00',
              'step' => '60', // minutes; default step interval is 1 min
          ],
              ]);
         
         
          $this->add([
         'type'=> Element\Date::class,
            'name'=>'wydarzenie_data',
             'options' => [
             'label'  => 'Data wydarzenia',
              'format' => 'Y-m-d'
                 ],
              ]); 
         
        
    }

    public function getInputFilterSpecification(): array {
            var_dump($this->getOptions()); 
    }

    
    

}



