<?= \Plum\Html::doctype() ?>

<?php
if(empty($page)) {
    throw new \Plum\Exception('No page object passed to page view.');
}
$html = new \Plum\HtmlBuilder();
$html->head();

// If anything needs to go in the HTML head, do it now.

// End HTML Head.
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

if(!empty($page->header)) {
    $html->body()->div($div_header);

    // Put header contents here.

    // End header contents
    $html->step_out('body');
}

if(!empty($page->body)) {
    $html->div($div_body);

    $html->step_out('body');
}

if(!empty($page->footer)) {
    $html->div($div_footer);

    $html->step_out('html');
}
?>

<?= $html->get_string() ?>
