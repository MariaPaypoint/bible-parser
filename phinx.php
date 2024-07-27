<?php

require "config.php";

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'default',
        'default' => [
            'adapter' => 'mysql',
            'host' => MYSQL_HOST,
            'name' => MYSQL_SCHEMA,
            'user' => MYSQL_USER,
            'pass' => MYSQL_PASSWORD,
            'port' => MYSQL_PORT,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
