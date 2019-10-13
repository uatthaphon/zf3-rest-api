<?php

namespace SCG;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\SCGTable::class => function ($container) {
                    $tableGateway = $container->get(Model\SCGTableGateway::class);
                    return new Model\SCGTable($tableGateway);
                },
                Model\SCGTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\SCG());
                    return new TableGateway('scgs', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\SCGController::class => function ($container) {
                    return new Controller\SCGController(
                        $container->get(Model\SCGTable::class)
                    );
                },
            ],
        ];
    }
}
