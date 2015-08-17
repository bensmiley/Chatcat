<?php

define("IN_MYBB", 1);
// define('THIS_SCRIPT', 'ajaxchat.php');

require_once('CCAuth.php');


// ################### PRE-CACHE TEMPLATES AND DATA ######################
// get special phrase groups
//$phrasegroups = array('posting');
    
function get_auth () {
    
    $user_id = $_REQUEST['uid'];
    //getting user details
    
    if($user_id > 0)
    {
        $api_key = $_REQUEST['api_key'];
        $secret = $_REQUEST['secret'];
        $src = urldecode($_REQUEST['src']);
        $username = $_REQUEST['username'];
        $description = $_REQUEST['description'];
        $yearOfBirth = $_REQUEST['yearOfBirth'];
        $location = $_REQUEST['location'];
        $homepage = urldecode($_REQUEST['homepage']);
        
        // Create a new Auth instance
        $auth = new CCAuth ($user_id, $secret, $api_key, '');
        
        // Authenticate the user
        $auth->setUserInfo($username,
                           $description, 
                           null, 
                           $yearOfBirth,
                           $location,
                           null, 
                           $src, 
                           array(
                                 'homepageLink' => $homepage
                           ));
        
        return $auth;
    }
    
    return null;
}

$auth = get_auth();
if ($auth != null) {
    
    if($_REQUEST['action'] == 'cc_get_uid') {
        $auth->respondWithUserID();
    }
    if($_REQUEST['action'] == 'cc_get_token') {
        $auth->respondWithToken();
    }
}
else {
    CCAuth::logout();
}

?>