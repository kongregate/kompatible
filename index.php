<?php
error_reporting(E_ALL);
if(file_exists('config.json')){
  $config = json_decode(file_get_contents('config.json'), true);
} else {
  print("put credentials in config.json");
  die();
}
if (isset($_REQUEST['platform']) && $_REQUEST['platform'] == "fb") {
	require_once("FacebookPlatform.php");
	$kompatible = new FacebookPlatform($config['facebook']);	

} else {
	require_once("KongregatePlatform.php");
	$kompatible = new KongregatePlatform($config['kongregate']);
}

$user_id = $kompatible->login();
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>
</head>
<?= $kompatible->loadLibraries(); ?>
<body>
<?= $kompatible->displayHeader(); ?>
<h1>Hello <?= $kompatible->getUserName() ?>!</h1>
<?= $kompatible->showPurchaseButton() ?>
<?php if ($kompatible->isFeatureEnabled("invites")) { ?>
<a onclick='showInvitePopup();' href="#">Invite a Friend!</a>
<?php }?>
<?= $kompatible->getFriends(); ?>
<?= $kompatible->displayFooter(); ?>
</body>
