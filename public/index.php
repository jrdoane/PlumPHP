<?php
// Plum index file that all requests hit.
require_once(dirname(dirname(__FILE__)) . "/plum/init.php");
$cname = \Plum\URI::get_controller();
try {
    $controller = \Plum\Controller::factory($cname);
} catch (Exception $ex) {
    \Plum\HTTP::send_404();
    // Make this less dumb. --jdoane
    print "<p>Error loading the controller: {$cname}.</p>";
    exit();
}

$method = \Plum\URI::get_method();
if(!method_exists($controller, $method)) {
    \Plum\HTTP::send_404();
    // Make this less dumb. --jdoane
    print "<p>Action {$method} does not exist in controller {$cname}.</p>";
    exit();
}

$controller->before();
$controller->$method();
$controller->after();
