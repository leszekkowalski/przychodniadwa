<?php

namespace Application\Form;

use Laminas\Form\Element\Csrf;

use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\File;
use Laminas\Form\Form;
use Laminas\Validator\Identical;
use Application\Form\LekarzFieldset;
use Laminas\Form\Element\Hidden;
use Application\Form\Element\Telefon;
use Laminas\Form\Element\File\Extension;

class LekarzDodajForm extends Form implements \Laminas\InputFilter\InputFilterProviderInterface
    
{
    public function __construct() {
        parent::__construct('dodaj_form_lekarz');
    }
    
    public function init()
    {
 
         $this->add([
           'name' => 'lekarz_fieldset',
            'type' => LekarzFieldset::class,
            'options' => [
            'use_as_base_fieldset' => true,
        ],
        ]);
       
        $this->add([            
                'type'  => Email::class,
                'name' => 'confirm_mail',
                'options' => [
                    'label' => 'Powtórz maila:',
                ],
            ]);
         
          $this->add([
            'type'=> File::class,
            'name'=>'file',
            'attributes'=>[
                'id'=>'file',
            ],
            'options'=>[
               'label' =>'Wybierz zdjęcie',
            ],
        ] );
  
        $this->add([
            'type' => Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Wpisz nowego Lekarza',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array {
       
        return [
            [
             'name' => 'confirm_mail',
                    'required' => true,
                    'filters'  => [                        
                    ],                
                    'validators' => [
                        [
                            'name'    => \Laminas\Validator\Identical::class,
                            'options' => [
                                'token' => [
                               'lekarz_fieldset'=>'mail' ,
                               ]                                 
                            ],
                        ],
                    ],   
  
            ],
        [
            'name'=>'file',
         //   'type'=> \Laminas\InputFilter\FileInput::class,
            'required'=>false,
            'validators'=>[
                [
                 'name'=> \Laminas\Validator\File\Extension::class,
                    'options'=>[
                      'extension' =>['jpeg','jpg','png'],
                    ],
                ],
                [
                  'name'=> \Laminas\Validator\File\IsImage::class,
                    'options'=>[
                        'mimeType'=>['image/png', 'image/jpeg']
                    ],
                    
                ],
                [
                    'name'=> \Laminas\Validator\File\Size::class,
                    'options'=>[
                        'min'=>'100kb',
                        'max'=>'2MB',
                    ],
                ],
           //     [
             //       'name'=> File\Upload::class,
              //  ],
                [
                    'name'=> \Laminas\Validator\File\UploadFile::class,
                ],
                [
                    'name'=> \Laminas\Validator\File\ImageSize::class,
                    'options'=>[
                            'minWidth'  => 128,
                            'minHeight' => 128,
                            'maxWidth'  => 4096,
                            'maxHeight' => 4096
                    ],
                ],   
            ],
            
            'filters' =>[
                
                 ['name'=> \Laminas\Filter\File\RenameUpload::class,
                 'options'=>[
                      'target' => './public/upload/nowa_nazwa',
                     'overwrite'=>true,//istniejacy plik zostanie zastapiony w przypadku takiej samej nazwy
                     'use_upload_extension'=>true,//rozrzezenie zostanie zachowane
                     'randomize '=>false,
                     'use_upload_name '=>false,
                ],
                 ],

             ],
            
        ],
                ];
          
    }

}