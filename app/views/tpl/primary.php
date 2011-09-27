<?= Html:doctype('html5') ?>
<html>
<head>
    <title><?= isset($title) ? $title : "Manager"; ?></title>
    <meta http-equiv="content-type" 
	  content="application/xhtml+xml; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="assets/css/stylesheet.css" 
	  media="screen, projection, tv " />
</head>

<body>
    <!-- start top menu and blog title-->
    <div id="blogtitle">
	<!-- Breadcrumbs -->
	<div id="small">
	    <?= isset($breadcrumbs) ? $breadcrumbs : 'Manager' ?>
	</div>
	<div id="small2">
	    <?= isset($user_string) ? $user_string : 'You are not logged in.' ?>
	</div>
    </div>
    <?php if(isset($left)): ?>
    <div id="leftcontent">
	<?= $left ?>
    </div>
    <?php endif; ?>
    <!-- end left box-->

    <!-- start content -->
    <div id="centercontent" style="<?= isset($left) ? '' : 'margin-left: 10px; ' ?>
    <?= isset($right) ? '' : 'margin-right: 10px;'?>">
	<div class="centercontenttitlebar">
	    <?= isset($titlebar) ? $titlebar : "Title Bar" ?>
	</div>
	<div class="centercontentsection">
	    <?= isset($center) ? $center : "Content goes here." ?>
	</div>
    </div>
    <!-- end content -->

    <!-- start right box -->
    <?php if(isset($right)): ?>
    <div id="rightcontent">
        <?= $right ?>
    </div>
    <?php endif; ?>
<!-- end right box -->

<!-- start footer -->
<div id="footer">Manager</div>
<!-- end footer -->
</body>
</html>
