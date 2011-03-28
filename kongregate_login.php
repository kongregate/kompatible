<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>

	<!-- Load the Kongregate Javascript API -->
	<script type="text/javascript" src="http://www.kongregate.com/javascripts/kongregate_api.js"></script>
</head>
<body style='margin: 0px;width: 760px;height:580px;border:0px solid black;overflow:hidden;'>
<a href='#' onclick='showMyLogin();'>
	You need to login to play this game.
</a>
<script type="text/javascript">
	// Called when the API is finished loading
	function onLoadCompleted() {

	}
	function showMyLogin() {
		// Get a global reference to the kongregate API. This way, pages included in the
		// iframe can access it by using "parent.kongregate"
		kongregate = kongregateAPI.getAPI();
		kongregate.services.addEventListener("login", onKongregateInPageLogin);
		kongregate.services.showSignInBox();
		//startLoginRefreshTimer();
	}

	function onKongregateInPageLogin(event) {
		onLogin();
	}

	function onLogin() {
		// Log in with new credentials here
		var url = '<?= $kgParams ?>';
		url += "&kongregate_user_id=" + kongregate.services.getUserId();
		url += "&kongregate_username=" + kongregate.services.getUsername();
		url += "&kongregate_game_auth_token=" + kongregate.services.getGameAuthToken();
		document.location = url;
	}

	var checkingInterval = false;
	var count = 60;
	function startLoginRefreshTimer() {
		if (checkingInterval)
			return;
		checkingInterval = true;
		var stopping = false;
		setInterval(function() {
			//console.log("interval fired");
			if (count <= 0) {
				count = 60;
				//console.log("checking...");
				//console.log(kongregate.services);
				if (!kongregate.services.isGuest())
					onLogin();
			} else {
				count = count - 1;
			}
		}, 1000);
	}

	// Begin the process of loading the Kongregate API:
	kongregateAPI.loadAPI(onLoadCompleted);
</script>
</body>
</html>