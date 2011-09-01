<?php
// Plum index file that all requests hit.
require_once(dirname(dirname(__FILE__)) . "/plum/init.php");
$request = \Plum\URI::get_request_array();
print_r($request);
if(!class_exists($request['controller'])) {
    // Do a 404 or something.
    // Make this pretty when we have time.
    \Plum\HTTP::send_404();
    print "<p>Controller does not exist.</p>";
    exit();
}
$controller = new $request['controller']();
