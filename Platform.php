<?php

abstract class Platform
{

    // AUTHENTICATION
    abstract public function loadLibraries();
	abstract public function login();
	abstract public function isLoggedIn();
	abstract public function getUser();

    public function getSessionParams() {}


    // USER API CALLS
    abstract public function getUserName();
	abstract public function getName($user_id);

    public function displayProfilePicture($user_id) {}
    public function displayName($user_id) {}
	abstract public function getUserInfo($user_id, $fields);
    abstract public function getFriends();
    abstract public function getFriendsAppUsers();
    abstract public function getFriendsNotAppUsers();
    abstract public function areFriends($user_id, $friend_id);
	
    
    // LAYOUT/CONTENT
	abstract public function isFeatureEnabled($feature_id);
    public function displayLogin(){}
    public function displayLogout(){}
    public function displayHeader(){}
    public function displayFooter(){}

    public function getFlashHeight(){}
    public function getFlashParams(){}
	public function displayFlashFile(){}

    public function getRequestListLink(){}
    public function getAppInfoPage(){}
    public function getAppNewsfeedPage(){}
    public function getAppForums(){}
    
   
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