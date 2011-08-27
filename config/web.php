<?php
// Default core config file.
// Just start assigning stuff to $config. It is an assoc array.

$config['wwwroot'] = 'http://plum.peared.net/';
$config['wwwfile'] = 'index.php'; // (With a properly configured .htaccess file, this can be ''

$config['default_controller'] = 'hello_world'; // Controller to default to if uri string is empty.
$config['default_method'] = 'index'; // This is the default method that gets call if the uri string doesn't have a method.
$config['action_prefix'] = ''; // adding foo_ here would make default foo_index.
$config['action_suffix'] = ''; // adding _foo here would make default index_foo.
