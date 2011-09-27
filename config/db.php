<?php
/**
 * If no servers are set as default, we will take the last one we encounter.
 * We don't want to handle this well because this is highly discouraged.
 */
$config['servers'] = array(
    'default' => array(
        'dbtype' => 'PostgreSQL',
        'server' => 'localhost',
        'port' => '5432',
        'username' => 'test',
        'password' => 'lkj123',
        'database' => 'plumphp_test',
        'prefix' => '',
        'persistant' => true,
        'default' => true
    )
);
