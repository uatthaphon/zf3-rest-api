<?php

namespace SCG\Controller;

use SCG\Model\SCG;
use SCG\Form\SCGForm;
use SCG\Model\SCGTable;
use Zend\Cache\StorageFactory;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class SCGController extends AbstractActionController
{
    private $scgTable;
    private $cache;
    private $cacheKey = 'scg-list';
    private $cacheTTL = 60*60*1; //1 hour
    private $cacheDir = './data/cache';

    public function __construct(SCGTable $scgTable)
    {
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

        return new ViewModel(['scgs' => $scgs,]);
    }

    public function addAction()
    {
        $form = new SCGForm();
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $scg = new SCG();
        $form->setInputFilter($scg->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $scg->exchangeArray($form->getData());
        $this->scgTable->upsert($scg);
        $this->clearCache();

        return $this->redirect()->toRoute('scg');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if ($id === 0) {
            return $this->redirect()->toRoute('scg', ['action' => 'add']);
        }

        try {
            $scg = $this->scgTable->getById($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('scg', ['action' => 'index']);
        }

        $form = new SCGForm();
        $form->bind($scg);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($scg->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->scgTable->upsert($scg);
        $this->clearCache();

        return $this->redirect()->toRoute('scg', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if ($id === 0) {
            return $this->redirect()->toRoute('scg');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->scgTable->deleteById($id);
                $this->clearCache();
            }

            return $this->redirect()->toRoute('scg');
        }

        return [
            'id'    => $id,
            'scg' => $this->scgTable->getById($id),
        ];
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
