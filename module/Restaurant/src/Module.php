<?php

namespace Restaurant;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerConfig()
    {
        return [
        'factories' => [
            Controller\RestaurantController::class => function ($container) {
                return new Controller\RestaurantController();
            },
        ],
    ];
    }
}
