<?php

namespace Str\Controller;

use Zend\Cache\StorageFactory;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class StrController extends AbstractRestfulController
{
    private $cache;
    private $cacheKey = 'find-string';
    private $cacheTTL = 60*60*1; //1 hour
    private $cacheDir = './data/cache';

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');

        $this->cache = $this->initCache();
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

    public function indexAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $response['data'] = [
                'status' => 'notOk',
                'message' => 'Invalid requested!',
            ];

            return new JsonModel($response);
        }

        $inputs = $this->params()->fromPost('inputs', []);
        $extendKey = str_replace(',', '-', $inputs);
        $cacheKey = "$this->cacheKey-{$extendKey}";
        $results = $this->cache->getItem($cacheKey, $success);

        if (!$success) {
            if (!empty(trim($inputs))) {
                $inputs = explode(',', $inputs);
            }

            $results = array_filter($inputs, function ($value) {
                return preg_match('/[A-Za-z]/', $value);
            });

            $this->cache->setItem($cacheKey, $results);
        }


        $response['data'] = [
            'status' => 'ok',
            'result' => array_values($results),
        ];

        return new JsonModel($response);
    }
}
