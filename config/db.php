<?php
/**
 * If no servers are set as default, we will take the last one we encounter.
 * We don't want to handle this well because this is highly discouraged.
 */
$config['servers'] = array(
    $config['test_db'] = array(
        'dbtype' => 'PostgreSQL',
        'server' => 'localhost',
        'port' => '5432',
        'username' => 'plum_peared_net',
        'password' => 'lkj123',
        'database' => 'plum_peared_net',
        'prefix' => '',
        'persistant' => true,
        'default' => true
    )
);
