<?php
namespace Str;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'str' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/str[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\StrController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'str' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
