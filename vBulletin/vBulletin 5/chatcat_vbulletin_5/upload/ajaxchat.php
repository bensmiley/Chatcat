<?php
require_once('CCAuth.php');

$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : null;
$api = isset($_REQUEST['api']) ? $_REQUEST['api'] : null;
$uname = isset($_REQUEST['uname']) ? $_REQUEST['uname'] : null;
$desc = isset($_REQUEST['desc']) ? $_REQUEST['desc'] : null;

$src = isset($_REQUEST['src']) ? $_REQUEST['src'] : null;
$src = urldecode($src);

$home = isset($_REQUEST['home']) ? $_REQUEST['home'] : null;
$home = urldecode($home);

$bod = isset($_REQUEST['birthday']) ? $_REQUEST['birthday'] : null;

$location = isset($_REQUEST['location']) ? $_REQUEST['location'] : null;
$secret = isset($_REQUEST['secret']) ? $_REQUEST['secret'] : null;
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

// echo $uid.'--'.$api.'--'.$uname.'--'.$desc.'-'.$src;
// die;

if($uid > 0) {

	// Create a new Auth instance
	$auth = new CCAuth ($uid, $secret, $api, '');

	// Authenticate the user
	$auth->setUserInfo($uname, $desc, null, $bod, $location, null, $src, array('homepageLink' => $home));

	if($action == 'cc_get_uid'){
		$auth->respondWithUserID();
	}
	if($action == 'cc_get_token'){
		$auth->respondWithToken();
	}
}
else {
	 CCAuth::logout();
}


die();
