<?php
error_reporting(E_ALL);

$config = array();
if (isset($_REQUEST['platform']) && $_REQUEST['platform'] == "fb") {
	require_once("FacebookPlatform.php");
	$config['app_id'] = "162536733803732";
	$config['app_secret'] = "51405170f11b22c8231fbc2cbda4c163";
	$config['app_root'] = "http://mrkongybot.doesntexist.com:4007/synapse/?platform=fb";
	$config['server_root'] = "http://mrkongybot.  .com:4007/synapse/";
	$platform = new FacebookPlatform($config);	

} else {
	require_once("KongregatePlatform.php");
	$config['app_id'] = "108822";
	$config['app_secret'] = "9fe7bfa1-826e-4fd7-9747-f55769e85854";
	$config['app_root'] = "http://www.kongregate.com/games/towski/facebook-example_preview?guest_access_key=ead63275767bc3adf7561672548da162c5e605f88fe3ce801d2e177e79135466";
	$config['server_root'] = "http://localhost:4007/synapse/";
	//$config['app_id'] = "72";
	//$config['app_secret'] = "513d6ce3-b085-4974-af7b-ea8813afa105";
	//$config['app_root'] = "http://www.kongregatedev.com:3000/games/jimgreer/facebook-example_preview?guest_access_key=656c10f76916679d17cdba7095107b3aa69a8fa193daa7d9a978e07c6f72097d";
	//$config['server_root'] = "http://localhost:4007/synapse/";
  //$config['api_host'] = 'http://www.kongregatedev.com:3000/api';
  $config['api_host'] = 'http://www.kongregate.com/api';
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