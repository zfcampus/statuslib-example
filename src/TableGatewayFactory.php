<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace StatusLib;

use DomainException;

/**
 * Service factory for the StatusLib TableGateway
 *
 * If the "statuslib" key is present, and either the "db" or "table" subkeys
 * are present and valid, uses those; otherwise, uses defaults of "Db\StatusLib"
 * and "status", respectively.
 *
 * If the DB service does not exist, raises an error.
 *
 * Otherwise, creates a TableGateway instance with the DB service and table.
 */
class TableGatewayFactory
{
    public function __invoke($services)
    {
        $db    = 'Db\StatusLib';
        $table = 'status';
        if ($services->has('config')) {
            $config = $services->get('config');
            $config = $config['statuslib'];

            if (array_key_exists('db', $config) && !empty($config['db'])) {
                $db = $config['db'];
            }

            if (array_key_exists('table', $config) && !empty($config['table'])) {
                $table = $config['table'];
            }
        }

        if (!$services->has($db)) {
            throw new DomainException(sprintf(
                'Unable to create StatusLib\TableGateway due to missing "%s" service',
                $db
            ));
        }

        return new TableGateway($table, $services->get($db));
    }
}
