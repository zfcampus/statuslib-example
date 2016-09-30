<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace StatusLib;

use DomainException;
use InvalidArgumentException;
use Traversable;
use Rhumsaa\Uuid\Uuid;
use Zend\Paginator\Adapter\DbTableGateway;
use Zend\Stdlib\ArrayUtils;

/**
 * Mapper implementation using a Zend\Db\TableGateway
 */
class TableGatewayMapper implements MapperInterface
{
    /**
     * @var TableGateway
     */
    protected $table;

    /**
     * @param TableGateway $table
     */
    public function __construct(TableGateway $table)
    {
        $this->table = $table;
    }

    /**
     * @param array|Traversable|\stdClass $data
     * @return Entity
     */
    public function create($data)
    {
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        if (is_object($data)) {
            $data = (array) $data;
        }

        if (! is_array($data)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid data provided to %s; must be an array or Traversable',
                __METHOD__
            ));
        }

        $data['id'] = Uuid::uuid4()->toString();
        if (! isset($data['timestamp'])) {
            $data['timestamp'] = time();
        }
        $this->table->insert($data);

        $resultSet = $this->table->select(['id' => $data['id']]);
        if (0 === count($resultSet)) {
            throw new DomainException('Insert operation failed or did not result in new row', 500);
        }
        return $resultSet->current();
    }

    /**
     * @param string $id
     * @return Entity
     */
    public function fetch($id)
    {
        if (! Uuid::isValid($id)) {
            throw new DomainException('Invalid identifier provided', 404);
        }

        $resultSet = $this->table->select(['id' => $id]);
        if (0 === count($resultSet)) {
            throw new DomainException('Status message not found', 404);
        }
        return $resultSet->current();
    }

    /**
     * @return Collection
     */
    public function fetchAll()
    {
        return new Collection(new DbTableGateway($this->table, null, ['timestamp' => 'DESC']));
    }

    /**
     * @param string $id
     * @param array|Traversable|\stdClass $data
     * @return Entity
     */
    public function update($id, $data)
    {
        if (! Uuid::isValid($id)) {
            throw new DomainException('Invalid identifier provided', 404);
        }
        if (is_object($data)) {
            $data = (array) $data;
        }

        if (! isset($data['timestamp'])) {
            $data['timestamp'] = time();
        }

        $this->table->update($data, ['id' => $id]);

        $resultSet = $this->table->select(['id' => $id]);
        if (0 === count($resultSet)) {
            throw new DomainException('Update operation failed or result in row deletion', 500);
        }
        return $resultSet->current();
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        if (! Uuid::isValid($id)) {
            throw new DomainException('Invalid identifier provided', 404);
        }

        $result = $this->table->delete(['id' => $id]);

        if (! $result) {
            return false;
        }

        return true;
    }
}
