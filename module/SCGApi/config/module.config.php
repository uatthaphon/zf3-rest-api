<?php

namespace SCGApi;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'scgapi' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/api/scg',
                    'defaults' => [
                        'controller' => Controller\SCGApiController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'scgapi' => __DIR__ . '/../view',
        ],
    ],
];
