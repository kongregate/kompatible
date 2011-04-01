<?php
error_reporting(E_ALL);

$config = array();
if (isset($_REQUEST['platform']) && $_REQUEST['platform'] == "fb") {
	require_once("FacebookPlatform.php");
	$config = array();
	$config['app_id'] = "211399198872054";
	$config['app_secret'] = "1ba40f64224da013c9b42a584d001dd8";
	$config['app_root'] = "http://dev.tyrantonline.com/platformdemo/?platform=fb";
	$config['server_root'] = "http://dev.tyrantonline.com/platformdemo";
	$platform = new FacebookPlatform($config);	

} else {
	require_once("KongregatePlatform.php");
	$config = array();
	$config['app_id'] = "108711";
	$config['app_secret'] = "5f58de78-c989-49cd-948a-af423ff222bf";
	$config['app_root'] = "http://www.kongregate.com/games/freneticpixel/platform-demo_preview/";
	$config['server_root'] = "http://dev.tyrantonline.com/platformdemo";

	$platform = new KongregatePlatform($config);
}

$user_id = $platform->login();
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>
</head>
<?= $platform->loadLibraries(); ?>
<body>
<?= $platform->displayHeader(); ?>
<h1>Hello <?= $platform->getUserName() ?>!</h1>
<?= $platform->showPurchaseButton() ?>
<?php if ($platform->isFeatureEnabled("invites")) { ?>
<a onclick='showInvitePopup();' href="#">Invite a Friend!</a>
<?php }?>
<?= $platform->getFriends(); ?>
<?= $platform->displayFooter(); ?>
</body>