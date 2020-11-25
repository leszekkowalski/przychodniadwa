<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Application\Form\Element\Telefon;
use Application\Form\LekarzDodajForm;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Model\Lekarz;
use Application\Model\Factory\LekarzFactory;
use Application\Polaczenie\LekarzPolaczenie;
use Application\Form\LekarzFieldset;
use Application\Form\Factory\LekarzFieldsetFactory;

return [
    
     'service_manager' => [
        'aliases' => [
          
        ],
        'factories' => [
            Lekarz::class=> LekarzFactory::class,
            LekarzPolaczenie::class=> Polaczenie\Factory\LekarzPolaczenieFactory::class,
        ],
    ],
    
     'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\LekarzController::class=> Controller\Factory\LekarzControllerFactory::class,
        ],
    ],

    'form_elements' => [
        'aliases' => [
           'telefon' => Telefon::class,
        ],
        'factories' => [
            Telefon::class => InvokableFactory::class,
            LekarzFieldset::class=> LekarzFieldsetFactory::class,
        ],
    ],
    
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'lekarze' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/lekarze[page/:page]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                         'page' => 1,
                    ],
                ],
            ],
            ////////////////////////////////////////////
            'lekarz' => [
                'type'    => Segment::class,
                'options' => [
                     'route' => '/lekarz[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                        
                    ],
                    'defaults' => [
                        'controller'    => Controller\LekarzController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    ///////////////////////////////////////
                     'dodaj' => [
                        'type' => Literal::class,
                        'options' => [
                           'route'    => '/dodaj',
                           'defaults' => [
                                'controller' => Controller\LekarzController::class,
                                'action'     => 'dodaj',
                                        ],
                                     ],
                                ],
                ],
            ],
            //////////////////////////////////////////////
        ],
    ],
   
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'base_path' => '/przychodniadwa/public/'
    ],
];
