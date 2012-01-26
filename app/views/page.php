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
$html->h(3, \Plum\Config::get('site_name', 'web'), array('id' => 'pagetitle'));

// Check to see if a user is logged in.

// This should take two forms. Not logged in, and logged in.
if(\Plum\Auth::is_logged_in()) {
    $user = \Plum\Auth::get_current_user();
    $loginstr = \Plum\Lang::get('youareloggedinas') . " {$user->username}";
    $loginstr .= ' (' . \Plum\Xml::tag('a', 
        array('href' => \Plum\Uri::href('login/logout')),
        \Plum\Lang::get('logout')
    ) . ')';
} else {
    $loginstr = \Plum\Lang::get('youarenotloggedin');
}

$html->raw('p', array('id' => 'loginstring'), $loginstr);
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
        if(is_string($crumb)) {
            $crumb = (object)array(
                'text' => $crumb
            );
        }
        $html->li(false, array('class' => 'breadcrumb'))
            ->span('>');
        if(!empty($crumb->url)) {
            $html->a($crumb->text, $crumb->url);
        } else {
            $html->span($crumb->text);
        }
        $html->step_out('ul');
    }
}

// End header contents
$html->step_out('body');

if(!empty($page->body)) {
    $html->div($div_body);
    if(is_object($page->body)) {
        if(\Plum\HtmlBuilder::is_builder($page->body)) {
            $html->merge_builders($page->body);
        } else {
            // TODO: Handle it.
        }
    }
    $html->step_out('body');
}

$html->hr();
if(empty($page->footer)) {
    $page->footer = new \Plum\HtmlBuilder('div', $div_footer);
}
if(is_string($page->footer)) {
    $html->div($div_footer, $page->footer);
}
if(\Plum\HtmlBuilder::is_builder($page->footer)) {
    if(\Plum\Auth::is_privileged('site:admin')) {
        $page->footer->a(\Plum\Lang::get('adminpanel'), \Plum\Uri::href('admin'));
    }
    $html->merge_builders($page->footer, true);
}
$html->step_out('html');
?>

<?= $html->get_string() ?>
