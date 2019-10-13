<?php

namespace SCGApi\Controller;

use SCGApi\Model\SCGTable;
use Zend\Cache\StorageFactory;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;

class SCGApiController extends AbstractActionController
{
    private $scgTable;
    private $cache;
    private $cacheKey = 'scg-list';
    private $cacheTTL = 60*60*1; //1 hour
    private $cacheDir = './data/cache';

    public function __construct(SCGTable $scgTable)
    {
        header('Access-Control-Allow-Origin: *');

        $this->scgTable = $scgTable;
        $this->cache = $this->initCache();
    }

    public function indexAction()
    {
        $scgs = $this->cache->getItem($this->cacheKey, $success);

        if (!$success) {
            $scgs = $this->scgTable->fetchAll()->toArray();
            $this->cache->setItem($this->cacheKey, $scgs);
        }

        $response['data'] = [
            'status' => 'ok',
            'business' => array_values($scgs),
        ];

        return new JsonModel($response);
    }

    private function initCache()
    {
        return StorageFactory::factory([
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'cache_dir' => $this->cacheDir,
                    'ttl' => $this->cacheTTL,
                ],
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => [],
                ],
                'exception_handler' => [
                    'throw_exceptions' => false
                ],
                'Serializer',
            ],
        ]);
    }

    private function clearCache()
    {
        $this->cache->removeItem($this->cacheKey);
    }
}
