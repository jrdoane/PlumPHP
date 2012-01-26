<?php 
if(empty($name)) {
    $name = 'basic';
}
header("Content-Type: text/css;X-Content-Type-Options: nosniff;");
require(dirname(__FILE__) . '/style/'.$name.'.css'); ?>
