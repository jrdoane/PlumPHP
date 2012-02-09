<?php

$html = new \Plum\HtmlBuilder('div', array('id' => 'tpl_body'));

$html->div(array('id' => 'admin_column_left'));
$html->h(3, \Plum\Lang::get('general', 'admin'));
$html->ul();
$html->li();
$html->a(
    \Plum\Lang::get('motd', 'admin'),
    \Plum\Uri::href('admin/motd')
);

// Step out to the column div.
$html->step_out('div');

// Step out into the body div.
$html->step_out('div');

$html->div(array('id' => 'admin_column_center'));
if(!isset($page)) { $page = false; }
if(\Plum\HtmlBuilder::is_builder($page)) {
    $html->merge_builder($page);
} else {
    $html->h(1, \Plum\Lang::get('adminpanel', 'admin'));
    $html->p(\Plum\Lang::get('phpversion', 'admin') . ': ' . phpversion());
}
$html->step_out();

// In a function this would be: return $html;
// This gives the data back to the controller rather than putting to stdout.
// This enables objects to go back, instead of just strings.
\Plum\View::set_return($html);

