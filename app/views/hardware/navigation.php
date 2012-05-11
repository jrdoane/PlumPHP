<?php
/**
 * Hardware navigation view, this is the contents of the left column.
 */
$html = new \Plum\HtmlBuilder('div', array('id' => 'column_content'));

$html->h(3, \Plum\Lang::get('hardwaremanager', 'hardware'));
$html->ul();
$html->li()->a(\Plum\Lang::get('addhardware', 'hardware'), \Plum\Uri::href('hardware/additem'))->step_out();
$html->li()->a(\Plum\Lang::get('managehardware', 'hardware'), \Plum\Uri::href('hardware/manageitems'))->step_out();
$html->li(\Plum\Lang::get('managehardwaregroups', 'hardware'));
$html->li(\Plum\Lang::get('addhardwaregroup', 'hardware'));
$html->step_out('div');

\Plum\View::set_return($html);
