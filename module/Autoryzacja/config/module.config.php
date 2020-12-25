<?php
namespace Autoryzacja;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
        Controller\Autoryzacja::class=> InvokableFactory::class,
        ],
    ],
    
    
    
    'router' => [
        'routes' => [
            'autoryzacja' => [
                'type'    => 'Literal',
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/autoryzacja',
                    'defaults' => [
                        'controller'    => Controller\Autoryzacja::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Autoryzacja' => __DIR__ . '/../view',
        ],
    ],
];
