<?= \Plum\Html::doctype() ?>

<?php
if(empty($page)) {
    throw new \Plum\Exception('No page object passed to page view.');
}
$html = new \Plum\HtmlBuilder();

// TODO: Re-add this when we actually need it.
$html->head();
$html->title();
$html->link_style('style');
$html->step_out('html');

// Enter the html body and create a new drive using the header attributes.
$div_header = array(
    'id' => 'tpl_header'
);

$div_body = array(
    'id' => 'tpl_body'
);

$div_footer = array(
    'id' => 'tpl_footer'
);

$html->body()->div($div_header);

// Put header contents here.
$bread_list = array (
    'id' => 'breadcrumbs'
);
$html->h(3, \Plum\Config::get('site_name', 'web'));
$html->ul($bread_list);

// Lets say where we are and provide it as a link for starters.
$html->li(false, array('class' => 'breadcrumb', 'id' => 'firstbreadcrumb'))
    ->a(\Plum\Config::get('site_name_short', 'web'), \Plum\Uri::href())
    ->step_out('ul');

if(!empty($page->breadcrumbs)) {
    foreach($page->breadcrumbs as $crumb) {
        if(is_array($crumb)) {
            $crumb = (object)$crumb;
        }
        $html->li(false, array('class' => 'breadcrumb'))
            ->span('>')
            ->a($crumb->text, $crumb->url)
            ->step_out('ul');
    }
}

// End header contents
$html->step_out('body');

if(!empty($page->body)) {
    $html->div($div_body);
    if(is_object($page->body)) {
        $html->merge_builders($page->body);
    }
    $html->step_out('body');
}

$html->hr();
if(!empty($page->footer)) {
    $html->div($div_footer);

    $html->step_out('html');
}
?>

<?= $html->get_string() ?>
