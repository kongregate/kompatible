<?php

abstract class Platform
{

    // AUTHENTICATION
    abstract public function loadLibraries();
    public function getSessionParams() {}
    abstract public function getUser();
    abstract public function login();
    abstract public function isLoggedIn();

    // USER API CALLS
    abstract public function getUserName();
    public function displayProfilePicture($user_id) {}
    public function displayName($user_id) {}
    abstract public function getName($user_id);
	abstract public function getUserInfo($user_id, $fields);
    abstract public function getFriends();
    abstract public function getFriendsAppUsers();
    abstract public function getFriendsNotAppUsers();
    abstract public function areFriends($user_id, $friend_id);
	
    
    // LAYOUT/CONTENT
    abstract public function displayLogin();
    abstract public function displayLogout();
    abstract public function displayHeader();
    abstract public function displayFooter();

    abstract public function getFlashHeight();
    abstract public function getFlashParams();
	abstract public function displayFlashFile();

    abstract public function getRequestListLink();
    abstract public function getAppInfoPage();
    abstract public function getAppNewsfeedPage();
    abstract public function getAppForums();
    
	abstract public function isFeatureEnabled($feature_id);
    

	// PLATFORM API
    public function getEmail($uid){}
    public function getThirdPartyID($uid){}
    public function getUidFromThirdPartyID($tpi){}
    
    abstract public function isFan();
    abstract public function isBookmarked();
	abstract public function isSubscribed();
	abstract public function isStreaming();
	

    // SOCIAL CHANNELS
    public function addUserActivity($data) {}
    public function publishUserAction($data) {}
    public function displayInviteBox($data) {}
    public function publishStats($key, $value) {}
}

?>