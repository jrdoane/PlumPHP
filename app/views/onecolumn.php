<?php

$html = new \Plum\HtmlBuilder('div', array('id' => 'tpl_body'));

$html->div(array('id' => 'one_column_left', 'class' => 'segment'));

if(!isset($column)) { $column = false; }
if(\Plum\HtmlBuilder::is_builder($column)) {
    $html->merge_builders($column);
} else if(is_string($column)) {
    $html->tag('div', array('id' => 'one_column_left_content', 'class' => 'segment'), $column);
}
// Step out to the column div.
$html->step_out('div');

// Step out into the body div.

$html->div(array('id' => 'one_column_center', 'class' => 'segment'));
if(!isset($content)) { $content = false; }
if(\Plum\HtmlBuilder::is_builder($content)) {
    $html->merge_builders($content);
} else if (is_string($content)) {
    $html->tag('div', array('id' => 'one_column_center_content', 'class' => 'segment'), $content);
}
$html->step_out('div');

// In a function this would be: return $html;
// This gives the data back to the controller rather than putting to stdout.
// This enables objects to go back, instead of just strings.
\Plum\View::set_return($html);
