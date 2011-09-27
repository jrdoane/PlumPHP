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
    if(\Plum\Config::get('debug', 'system')) {
        $st = $ex->getTraceAsString();
        $ext = get_class($ex);
        $msg = $ex->getMessage();
        // output special stack trace if this is the case and any errors that 
        // are handy. Consider integrating dBug.php.
        print "<p>Exception ($ext): $msg</p>";
        print "<pre>Stack Trace\n$st</pre>";
    }
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
call_user_func_array(array($controller, $method), \Plum\URI::get_parameters());
$controller->after();
