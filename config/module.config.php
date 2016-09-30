<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace StatusLib;

return [
    'statuslib' => [
        // 'array_mapper_path' => 'path/to/PHP/file/returning/array.php',
    ],
    'service_manager' => [
        'aliases' => [
            Mapper::class => ArrayMapper::class,
        ],
        'factories' => [
            ArrayMapper::class        => ArrayMapperFactory::class,
            TableGatewayMapper::class => TableGatewayMapperFactory::class,
            TableGateway::class       => TableGatewayFactory::class,
        ],
    ],
];
