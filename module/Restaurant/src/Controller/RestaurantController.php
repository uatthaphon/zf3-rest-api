<?php

namespace Restaurant\Controller;

use joshtronic\GooglePlaces;
use Zend\Cache\StorageFactory;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class RestaurantController extends AbstractRestfulController
{
    private $cache;
    private $cacheKey = 'restaurant';
    private $cacheTTL = 60*60*1; //1 hour
    private $cacheDir = './data/cache';
    private $apiKey = ''; // Add api key for google Place API here
    private $googlePlace = null;

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');

        $this->cache = $this->initCache();
        $this->googlePlace = new GooglePlaces($this->apiKey);
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

    public function bangsueAction()
    {
        $bangsueLocations = [13.823487, 100.52563];
        $results = null;
        $response = null;
        $nextPageToken = (string) $this->params()->fromRoute('next_page_token', null);

        if (empty($nextPageToken)) {
            $response = $this->cache->getItem($this->cacheKey, $success);

            if (!$success) {
                $results = $this->getRestaurantByLocations($bangsueLocations);
                $response = $this->prepareBangsueResponse($results);
                $this->cache->setItem($this->cacheKey, $response);
            }
        } else {
            $cacheKeyWiteNextPageToken = $this->cacheKey.'-'.$nextPageToken;
            $response = $this->cache->getItem($cacheKeyWiteNextPageToken, $success);

            if (!$success) {
                $results = $this->getRestaurantByNextPageToken($bangsueLocations, $nextPageToken);
                $response = $this->prepareBangsueResponse($results);
                $this->cache->setItem($cacheKeyWiteNextPageToken, $response);
            }
        }

        return new JsonModel($response);
    }

    private function getRestaurantByLocations($locations)
    {
        $this->googlePlace->location = $locations;
        $this->googlePlace->rankby   = 'distance';
        $this->googlePlace->types = 'restaurant';

        return $this->googlePlace->nearbySearch();
    }

    private function getRestaurantByNextPageToken($locations, $nextPageToken)
    {
        $this->googlePlace->location = $locations;
        $this->googlePlace->rankby   = 'distance';
        $this->googlePlace->types = 'restaurant';
        $this->googlePlace->pagetoken = $nextPageToken;

        return $this->googlePlace->nearbySearch();
    }

    private function prepareBangsueResponse(array $googleResults)
    {
        $data = [];
        $data['data']['status'] = 'ok';
        $data['data']['next_page_token'] = isset($googleResults['next_page_token']) ? $googleResults['next_page_token'] : '';

        foreach ($googleResults['results'] as $result) {
            if (!isset($result['id'])) {
                continue;
            }

            $data['data']['restaurants'][] = [
                'id' => isset($result['id']) ? $result['id'] : '',
                'place_id' => isset($result['place_id']) ? $result['place_id'] : '',
                'location' => isset($result['geometry']['location']) ? $result['geometry']['location'] : ['lat' => 0, 'lng' => 0],
                'name' => isset($result['name']) ? $result['name'] : '',
                'rating' => isset($result['rating']) ? $result['rating'] : 0,
                'vicinity' => isset($result['vicinity']) ? $result['vicinity'] : '',
            ];
        }

        return $data;
    }
}
