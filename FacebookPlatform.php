<?php

require_once 'Platform.php';
require_once 'facebook/facebook.php';

class FacebookPlatform extends Platform {
	protected $facebook;
	protected $user;
	protected $config;

	public function __construct($config) {
		$this->config = $config;
		$this->facebook = new Facebook(array(
											'appId' => $config['app_id'],
											'secret' => $config['app_secret'],
											'cookie' => true
									   ));
	}

	public function loadLibraries() {
	?>
	<div id="fb-root"></div>
	<div id="iframe_container" style="display:none"></div>
	<script type="text/javascript">
		var origPostTarget;
		window.fbAsyncInit = function() {
			FB.init({
				appId   : '<?= $this->config['app_id'] ?>',
				session : <?= json_encode($this->facebook->getSession()); ?>,
				status  : true, // check login status
				cookie  : true, // enable cookies to allow the server to access the session
				xfbml   : true // parse XFBML
			});
			FB.Canvas.setAutoResize();
			FB.Event.subscribe('auth.login', function() {
				window.location.reload();
			});
		};

		(function() {
			var e = document.createElement('script');
			e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
			e.async = true;
			document.getElementById('fb-root').appendChild(e);
		}());

		function publishStream(title, message, body, actionText, link, images, target) {

			link = '<?= $this->config['app_id'] ?>/' + link + "&ref_id=" + <?= $this->user ?>;
			var data = {
				method: 'stream.publish',
				display: 'dialog',
				message: message,
				attachment: {
					name: title,
					caption: body,
					href: link
				},
				action_links: [
					{ text: actionText, href: link }
				],
				user_message_prompt: 'Share your thoughts'
			};

			data.attachment.media = [];
			var i = 0;
			for (var image in images) {
				data.attachment.media[i] = {'type':'image','src':'images/' + images[image],'href':link};
				i++;
			}
			FB.ui(data, function(response) {

			});

			var attachment = {'media':[], 'name':title, 'caption':body};

		}
		function showInvitePopup() {
			FB.ui({method: 'apprequests',
				   message: 'You should learn more about this awesome game.',
				   display: 'iframe',
				   data: 'trackingdata'});
		}

	</script>
	<?php
	}

	public function getSessionParams() {
		global $_REQUEST;
		if (isset($_REQUEST['signed_request']) && $_REQUEST['signed_request'] != "") {
			return "signed_request=" . $_REQUEST['signed_request'];
		}
		return "session=" . urlencode(json_encode($this->facebook->getSession()));
	}

	public function displayLogin() {
		$loginUrl = $this->facebook->getLoginUrl();
		echo "<a href='$loginUrl'>";
		echo "<img src='http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif'></a>";

	}

	public function displayLogout() {
		$logoutUrl = $this->facebook->getLogoutUrl();
		echo "<a href='$logoutUrl'>";
		echo "<img src='http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif'></a>";
	}

	public function getFlashHeight() {
		return 660;
	}

	public function getFlashParams() {
		$tpi = $this->getThirdPartyID($this->user);
		$params = "&user_id=$this->user";
		$params .= "&is_canvas=true";
		$params .= "&tpi=$tpi";
		return $params;
	}

	public function displayFlashFile() {
		?>
	<div id="swfdiv" style='z-index:-1;'>

		<EMBED src="/Main.swf" wmode="transparent" FlashVars="<?=$this->getFlashParams()?>" quality=high width=760
			   height=<?=$this->getFlashHeight()?> TYPE=
		"application/x-shockwave-flash"></EMBED>

	</div>
	<?php

	}
	
	public function showPurchaseButton() {
	  ?>
	  <div id="fb-root"></div>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <p> <a onclick="placeOrder(); return false;">Buy Stuff</a></p>

    <script> 
        FB.init({appId: "162536733803732", status: true, cookie: true});

        function placeOrder() {

          // Assign an internal ID that points to a database record
          var order_info = 'abc123';

          // calling the API ...
          var obj = {
            method: 'pay',
            order_info: order_info,
            purchase_type: 'item'
          };

          FB.ui(obj, callback);
        }

        var callback = function(data) {
          if (data['order_id']) {
            return true;
          } else {
            //handle errors here
            return false;
          }
        };

        function writeback(str) {
          document.getElementById('output').innerHTML=str;
        }
    </script>
    <?php 
  }

	public function getRequestListLink() {
		return "http://www.facebook.com/reqs.php#confirm_{$this->config['app_id']}" . "_0";
	}

	public function getAppInfoPage() {
		return "http://www.facebook.com/apps/application.php?id={$this->config['app_id']}";
	}

	public function getAppNewsfeedPage() {
		return "http://www.facebook.com/home.php?filter=app_{$this->config['app_id']}";
	}

	public function getAppForums() {
		return "http://www.facebook.com/board.php?uid={$this->config['app_id']}";
	}

	// User Calls

	public function getUser() {
		if (!isset($this->user)) {
			$this->user = $this->login();
		}
		return $this->user;
	}

	public function login() {
		$session = $this->facebook->getSession();
		if (!$session) {
			$url = $this->facebook->getLoginUrl(array(
													 'canvas' => 1,
													 'fbconnect' => 0,
												));
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit();
		} else {
			try {
				$this->user = $this->facebook->getUser();
			} catch (FacebookApiException $e) {
				error_log("Facebook API Error: GetUser - $e");
			}
		}
		return $this->user;
	}

	public function isLoggedIn() {
		return isset($this->user);
	}

	public function getUserName() {
		return $this->getName($this->user);
	}

	public function displayName($user_id) {
		echo"<fb:name uid='<?=$user_id?>' useyou='false' linked='false'></fb:name>";
	}

	public function getName($user_id) {
		try {
			$fbuser = $this->facebook->api("/$user_id");
			if (isset($fbuser['name'])) {
				return $fbuser['name'];
			}
		} catch (FacebookApiException $e) {
			error_log("Facebook API Exception: getName $e");
		}
		return "Facebook User";

	}

	public function getUserInfo($user_id, $fields) {
		if (is_array($fields))
			$fieldString = implode(",", $fields);
		else
			$fieldString = $fields;
		try {
			$param = array('method' => 'users.getInfo',
						   'uids' => $user_id,
						   'fields' => $fieldString);
			$response = $this->facebook->api($param);

		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: getUserInfo $e");
			return false;
		}
		return $response;
	}

	public function getFriends() {
		try {
			return $this->facebook->api("/me/friends");
		} catch (FacebookApiException $e) {
			error_log("Facebook Api Exception: getFriends $e");
			return array();
		}
	}

	public function getFriendsAppUsers() {
		try {
			$param = array('method' => 'friends.getAppUsers');
			return $this->facebook->api($param);
		} catch (FacebookApiException $e) {
			error_log("Facebook Api Exception: getFriendsAppUsers $e");
			return array();
		}
	}

	public function getFriendsNotAppUsers() {
		$query = "SELECT uid, has_added_app FROM user WHERE has_added_app!=1 and uid IN (SELECT uid2 FROM friend WHERE uid1 = '" . $this->user . "')";
		try {
			$param = array('method' => 'fql.query', 'query' => $query, 'callback' => '');
			$result = $this->facebook->api($param);
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: getFriendsNotAppUsers $e");
			return array();
		}

		$friends = array();
		if (!empty($result)) {
			foreach ($result as $r) {
				$friend = $r['uid'];
				if ($friend != null)
					$friends[] = $friend;
			}
		}
		return $friends;
	}

	public function areFriends($user_id, $friend_id) {
		try {
			$param = array('method' => 'friends.areFriends',
						   'uids1' => $user_id,
						   'uids2' => $friend_id,
						   'fields' => 'are_friends');
			$response = $this->facebook->api($param);
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: areFriends $e");
			return false;
		}

		if (!isset($response) || !isset($response[0]))
			return false;

		$isFriend = $response[0]['are_friends'];
		return $isFriend;
	}

	public function isFan() {
		$user = $this->user;
		$query = "SELECT uid FROM page_fan WHERE uid = '$user' AND page_id = {$this->config['app_id']}";

		try {
			$param = array('method' => 'fql.query', 'query' => $query, 'callback' => '');
			$result = $this->facebook->api($param);
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: isFan $e");
			return false;
		}
		return isset($result[0]);
	}

	public function isBookmarked() {
		$user = $this->user;
		$query = "SELECT bookmarked FROM permissions WHERE uid = $user";

		try {
			$param = array('method' => 'fql.query', 'query' => $query, 'callback' => '');
			$result = $this->facebook->api($param);
			if (!isset($result) || !isset($result[0]))
				return false;

			$bookmarked = $result[0]['bookmarked'];
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: isBookmarked $e");
			return false;
		}
		return $bookmarked;
	}

	public function isSubscribed() {
		$user = $this->user;

		$query = "SELECT email FROM permissions WHERE uid='$user'"; //(see above examples)
		try {
			$param = array('method' => 'fql.query', 'query' => $query, 'callback' => '');
			$result = $this->facebook->api($param);
			if (!isset($result) || !isset($result[0])) {
				return false;
			}

			return $result[0]['email'];
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: isBookmarked $e");
			return false;
		}
	}

	public function isStreaming() {
		$user = $this->user;

		$query = "SELECT publish_stream FROM permissions WHERE uid='$user'"; //(see above examples)
		try {
			$param = array('method' => 'fql.query', 'query' => $query, 'callback' => '');
			$result = $this->facebook->api($param);
			if (!isset($result) || !isset($result[0])) {
				return false;
			}

			return $result[0]['publish_stream'];
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: isBookmarked $e");
			return false;
		}
	}

	public function getFeaturedNews() {
		$news = array();
		$news[] = array('banner' => '5starreviewnewsbanner.png', 'link' => 'http://www.facebook.com/apps/application.php?id=130193190327885&v=app_6261817190');
		$news[] = array('banner' => 'forumsbanner.jpg', 'link' => 'http://www.facebook.com/board.php?uid=130193190327885');
		return $news;
	}

	public function displayProfilePicture($user_id) {
		echo "<img src='http://graph.facebook.com/<?=$user_id?>/picture' height='50' width='50'/>";
	}

	public function getEmail($uid) {
		$query = "SELECT uid,email FROM user WHERE uid=$uid"; //(see above examples)
		try {
			//$results = $this->facebook->api_client->fql_query($query);
			$param = array('method' => 'fql.query', 'query' => $query, 'callback' => '');
			$results = $this->facebook->api($param);
			if (!isset($results) || !isset($results[0])) {
				return false;
			}
			return $results[0];
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: getPermissiosn $e");
			return false;
		}
	}

	public function getThirdPartyID($uid) {
		try {
			$result = $this->facebook->api("/$uid/?fields=third_party_id");
			if (!isset($result) || !isset($result['third_party_id'])) {
				return false;
			}
			$tpi = $result['third_party_id'];
		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: third_party_id lookup $e");
			return false;
		}
		return $tpi;
	}

	public function getUidFromThirdPartyID($tpi) {
		try {
			$query = "select uid from user where third_party_id='$tpi'";
			$param = array('method' => 'fql.query', 'query' => $query, 'callback' => '');
			$result = $this->facebook->api($param);
			if (!isset($result) || !isset($result[0])) {
				return false;
			}
			$uid = $result[0]['uid'];

		} catch (FacebookApiException $e) {
			error_log("Facebook API Error: uidTpi lookup $e");
			return false;
		}

		return $uid;
	}

	public function isFeatureEnabled($feature) {
		switch($feature) {
			case"stats":
				return false;
		}
		return true;
	}

}