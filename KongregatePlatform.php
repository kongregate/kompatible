<?php

require_once 'Platform.php';

class KongregatePlatform extends Platform {
	protected $config;
	protected $user;
	protected $game_auth_token;
	public $items;

	protected static $KREDS_ITEMS;

	public function __construct($config) {
	  if(empty($config['api_host'])){
	    $config['api_host'] = 'http://www.kongregate.com/api'; 
	  } 
		$this->config = $config;
		self::$KREDS_ITEMS = $this->getGameItems();
	}

	public function loadLibraries() {
		$this->updateInventory();
		$data = $this->getKredsInventory();
		$this->items = $data['items'];
		echo "<script type='text/javascript' src='http://www.kongregate.com/javascripts/kongregate_api.js'></script>";
	}

	public function displayHeader() {
	}

	public function displayFooter() {
	}
	
	public function showPurchaseButton() {
	  ?>
	  <span>Number of robots: <span id='number_of_robots'><?= count($this->items); ?></span></span><br/>
    <a id='purchase_link' href='#' onclick='purchaseRobot();return false;'>Purchase robot</a>
    <script type="text/javascript">
      function onLoadCompleted() {
      	kongregate = kongregateAPI.getAPI();
      }

      function purchaseRobot(){
        kongregate.mtx.purchaseItems(["robot"], function(){
          $('#number_of_robots').html(parseInt($('#number_of_robots').html())+1);
        });
      }

      if(kongregateAPI){
      kongregateAPI.loadAPI(onLoadCompleted);
    }
    </script>
    <?php
  }

	public function getFlashHeight() {
		return 580;
	}

	public function getFlashParams() {
		$params = "&user_id={$this->user}";
		return $params;
	}

	public function displayFlashFile() {
		?>
	<div id="swfdiv" style='z-index:-1;'>
	</div>
	<script language="javascript" type="text/javascript">
		var flashvars = kongregateAPI.flashVarsString();
		var html = '<EMBED src="/KongregateMain.swf"';
		html += 'wmode="transparent" FlashVars="' + flashvars + '<?=self::getFlashParams()?>"';
		html += 'quality=high width=760 height=<?=$this->getFlashHeight();?>';
		html += 'TYPE="application/x-shockwave-flash"></EMBED>';
		$('#swfdiv').html(html);
	</script>
	<?php

	}

	public function login() {
		global $_REQUEST;

		$this->user = $_REQUEST['kongregate_user_id'];
		$this->game_auth_token = $_REQUEST['kongregate_game_auth_token'];

		if ($this->user == 0) {

			$kgParams = "{$this->config['server_root']}?";
			$params = $_GET;
			unset($params['kongregate_user_id']);
			unset($params['kongregate_username']);
			unset($params['kongregate_game_auth_token']);
			foreach ($params as $key => $value) {
				$kgParams .= "$key=$value&";
			}

			// no guests allowed
			include("kongregate_login.php");
			exit();
		}
		else if (!$this->isValidAuthToken($this->user, $this->game_auth_token)) {
			echo "Kongregate ERROR AUTHENTICATION";
			error_log("Error Kongregate Authenticating: " . $this->user);
			return;
		}
		return $this->user;
	}

	public function isValidAuthToken($user_id, $authToken) {
		$appsecret = $this->config['app_secret'];
		$authURL = "{$this->config['api_host']}/authenticate.json?user_id=$user_id&game_auth_token=$authToken&api_key=$appsecret";
		$result = self::getRemoteData($authURL);
		return ($result['success'] == true);
	}

	protected static function getRemoteData($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}

	public function loginAPI($user_id, $token) {
		global $_REQUEST;

		$this->user = $user_id;
		$this->game_auth_token = $_REQUEST['game_auth_token'];
		return $this->user;
	}

	public function isLoggedIn() {
		return true;
	}

	public function getUser() {
		if (!isset($this->user)) {
			$this->user = $this->login();
		}
		return $this->user;
	}

	public function getUserName() {
		$name = $_REQUEST['kongregate_username'];
		return $name;
	}

	public function getUserInfo($user_id, $fields) {
		return false;

	}

	public function getFriends() {
	  $apiCall = "{$this->config['api_host']}/user_info.json?api_key={$this->config['app_secret']}&user_id={$this->user}&game_auth_token={$this->game_auth_token}&friends=true";
		$result = self::getRemoteData($apiCall);
		$facebook_array = array();
		foreach($result['friends'] as $friend){
		  array_push($facebook_array, array("name" => $friend, "id" => $friend));
	  }
	  return array("data" => $facebook_array);
	}

	public function getFriendsAppUsers() {
		return array();
	}

	public function getFriendsNotAppUsers() {
		return array();
	}

	public function areFriends($user_id, $friend_id) {
		return true;
	}

	public function isFan() {
		return true;
	}

	public function isBookmarked() {
		return true;
	}

	public function isSubscribed() {
		return true;
	}

	public function isStreaming() {
		return true;
	}

	public function getRequestListLink() {
		return false;
	}

	public function getAppInfoPage() {
		return false;
	}

	public function getAppNewsfeedPage() {
		return false;
	}

	public function getAppForums() {
		return false;
	}

	public function getName($user_id) {
		global $_REQUEST;
		return $_REQUEST['kongregate_username'];
	}

	protected function getGameItems() {
		$game_items = self::getRemoteData("{$this->config['api_host']}/items.json?api_key={$this->config['app_secret']}&game_id={$this->config['app_id']}");
		foreach($game_items['items'] as $game_item){
      $game_items[$game_item['name']] = $game_item['price'];
    }
    return $game_items;
	}

	protected function getKredsInventory() {
		return self::getRemoteData("{$this->config['api_host']}/user_items.json?api_key={$this->config['app_secret']}&user_id={$this->user}");
	}

	protected function useKredItem($itemData) {
		$app_secret = $this->config['app_secret'];
		$item_id = $itemData['id'];
		$item_ident = $itemData['identifier'];
		$apiCall = "{$this->config['api_host']}/use_item.json?api_key=$app_secret&id=$item_id&user_id={$this->user}&game_auth_token={$this->game_auth_token}";
		$result = self::getRemoteData($apiCall);

		if ($result['success']) {
			$wb = self::$KREDS_ITEMS[$item_ident];
			if ($wb > 0) {
				//give bonds to user
				return true;
			}
			else
				error_log("Error could not use Kred item $item_id - $item_ident for user: {$this->user } and $wb");
		}
		return false;
	}

	public function updateInventory() {
		if ($this->user == null) {
			global $_REQUEST;
			$this->user = $_REQUEST['user_id'];
			$this->game_auth_token = $_REQUEST['kongregate_game_auth_token'];
		}

		$items = $this->getKredsInventory();

		$used = false;
		foreach ($items['items'] as $item) {
			$used = $used || $this->useKredItem($item);
		}

		if (!$used)
			return;

		return true;
	}


	public function publishStats($key, $value) {
		$apiCall = "{$this->config['api_host']}/submit_statistics.json?api_key={$this->config['app_secret']}&user_id={$this->user}&game_auth_token={$this->game_auth_token}";
		$apiCall .= "&$key=$value";
		$this->getRemoteData($apiCall);
	}

	public function isFeatureEnabled($feature) {
		switch($feature) {
		 case 'invites':
		 	return false;
		 }
		return true;
	}
}

?>