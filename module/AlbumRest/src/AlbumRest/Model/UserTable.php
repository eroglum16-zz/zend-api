<?php
namespace AlbumRest\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserTable
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

    public function getUser(user)
    {
        $user = (int) $user;
        $rowset = $this->tableGateway->select(['user' => user]);

        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with user %d',
                user
            ));
        }

        return $row;
    }
    public function getUserArray($id)
    {
        $user = (int) $user;
        $rowset = $this->tableGateway->select(['user' => $user]);

        return $rowset;
    }
}
?>