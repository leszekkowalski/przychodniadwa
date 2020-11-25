<?php 

namespace Application\Form;


use Laminas\Form\Fieldset;
use Laminas\Hydrator\ReflectionHydrator;
use Application\Model\Lekarz;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Application\Form\Element\Telefon;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Tel;
use Laminas\Db\Adapter\AdapterInterface;


class LekarzFieldset extends Fieldset{
 
    public $adapter;
    
   public function __construct(AdapterInterface $adapter) {
       parent::__construct();
       $this->adapter=$adapter; 
   }
    
    public function init() {
        
        $this->setHydrator(new ReflectionHydrator());
        $this->setObject(new Lekarz('','',$this->adapter));

        
        $this->add([
            'type' => Hidden::class,
            'name' => 'idlekarz',
        ]);
        
         $this->add([
            'type' => Text::class,
            'name' => 'imie',
            'options' => [
                'label' => 'Podaj imię:',
            ],
        ]);
       
         $this->add([
            'type' => Text::class,
            'name' => 'nazwisko',
            'options' => [
                'label' => 'Podaj nazwisko:',
            ],
        ]);
        
         $this->add([
            'type' => Text::class,
            'name' => 'pesel',
            'options' => [
                'label' => 'Podaj pesel:',
            ],
        ]);
           
         $this->add([
            'type' => Email::class,
            'name' => 'mail',
            'options' => [
                'label' => 'Podaj mail:',
            ],
        ]);
         
         $this->add([
            'type' => Text::class,
            'name' => 'specjalnosc',
            'options' => [
                'label' => 'Podaj specjalność:',
            ],
        ]);
        
        $this->add([
            'type' => Telefon::class,// patrz aliases w module.config.php
            'name' => 'telefon',
            'options' => [
                'label' => 'Podaj telefon:',
            ],
        ]);
        
         
         $this->add([
            'type' => Textarea::class,
            'name' => 'opis',
            'options' => [
                'label' => 'Wpisz opis lekarza:',
            ],
             
        ]);
         
      
        
    }
     
}

