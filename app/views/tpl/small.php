<?php namespace Plum; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title><?= isset($title) ? $title : "Title here"; ?></title>
    <meta http-equiv="content-type" 
	  content="application/xhtml+xml; charset=UTF-8" />
	  <link rel="stylesheet" type="text/css" href="<?= Uri::base(false) ?>/css/stylesheet-small.css" media="screen, projection, tv " />
	  <link rel="stylesheet" type="text/css" href="<?= URI::base(false) ?>/css/forms.css" media="screen, projection, tv" />
</head>

<body>
    <!-- start top menu and blog title-->
    <div id="blogtitle">
	<!-- Breadcrumbs -->
	<div id="small">
	    <?= isset($breadcrumbs) ? $breadcrumbs : 'Breadcrumbs here.' ?>
	</div>
	<div id="small2">
	    <?= isset($user_string) ? $user_string : 'You are not logged in.' ?>
	</div>
    </div>
    <!-- start content -->
    <div id="centercontent" style="<?= !empty($boxstyle) ? $boxstyle : '' ?>">
	<div class="centercontenttitlebar">
	    <?= isset($titlebar) ? $titlebar : "Title Bar" ?>
	</div>
	<div class="centercontentsection">
	    <?= isset($center) ? $center : "Content goes here." ?>
	</div>
    </div>
    <!-- end content -->

    <!-- start footer -->
    <div id="footer">PlumPHP - Dev</div>
    <!-- end footer -->
</body>
</html>
