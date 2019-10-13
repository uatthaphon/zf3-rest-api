<?php

namespace SCG\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class SCGTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getById($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    // Update or Insert
    public function upsert(SCG $scg)
    {
        $data = [
            'business' => $scg->business,
            'holding'  => $scg->holding,
            'telephone'  => $scg->telephone,
        ];

        $id = (int) $scg->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getById($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update SCG with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteById($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
