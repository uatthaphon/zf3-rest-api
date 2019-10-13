<?php
namespace Restaurant;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'restaurant' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api/restaurants[/:action[/:next_page_token]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'next_page_token' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\RestaurantController::class,
                        // 'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'restaurant' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
