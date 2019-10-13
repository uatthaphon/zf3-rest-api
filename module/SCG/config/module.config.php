<?php

namespace SCG;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'scg' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/scg[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SCGController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'scg' => __DIR__ . '/../view',
        ],
    ],
];
