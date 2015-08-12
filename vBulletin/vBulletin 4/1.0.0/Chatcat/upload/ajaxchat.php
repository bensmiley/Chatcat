<?php
/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 3.8.4
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2000-2009 Jelsoft Enterprises Ltd. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/

// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE & ~8192);

// #################### DEFINE IMPORTANT CONSTANTS #######################
define('THIS_SCRIPT', 'ajaxchat');
define('CSRF_PROTECTION', true);
define('LOCATION_BYPASS', 1);
define('NOPMPOPUP', 1);

// ################### PRE-CACHE TEMPLATES AND DATA ######################
// get special phrase groups
$phrasegroups = array('posting');


// get special data templates from the datastore
$specialtemplates = array('bbcodecache');

// pre-cache templates used by all actions
$globaltemplates = array();

// pre-cache templates used by specific actions
$actiontemplates = array(
	'fetchuserfield' => array(
		'memberinfo_customfield_edit',
		'userfield_checkbox_option',
		'userfield_optional_input',
		'userfield_radio',
		'userfield_radio_option',
		'userfield_select',
		'userfield_select_option',
		'userfield_select_multiple',
		'userfield_textarea',
		'userfield_textbox',
	),
	'quickedit' => array(
		'editor_clientscript',
		'editor_css',
		'editor_jsoptions_font',
		'editor_jsoptions_size',
		'editor_smilie',
		'editor_smiliebox',
		'editor_smiliebox_row',
		'editor_smiliebox_straggler',
		'newpost_disablesmiliesoption',
		'postbit_quickedit',
	)
);

$_POST['ajax'] = 1;

// ######################### REQUIRE BACK-END ############################
require_once('./global.php');
require_once(DIR . '/includes/class_xml.php');
require_once('./CCAuth.php');

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

($hook = vBulletinHook::fetch_hook('ajax_start')) ? eval($hook) : false;

// #############################################################################
// user name search


function cc_logout () {
    CCAuth::logout();
    die();
}

function get_auth () {

	global $vbulletin;
	global $db;
	$user_id = $vbulletin->userinfo['userid'];
	
	if($user_id > 0){

        // Get the API key from the settings
        $api_key = $vbulletin->options['chat_cat_messenger_api'];
        $secret = $vbulletin->options['chat_cat_messenger_secret'];

		$profilepic = $db->query_first("
			SELECT userid, dateline, height, width
			FROM " . TABLE_PREFIX . "customavatar
			WHERE userid = " . $user_id
		);

		if(($profilepic['dateline'])) {
			$img = $vbulletin->options['bburl']. '/image.php?' . $vbulletin->session->vars['sessionurl'] . 'u=' . $user_id; // . "&amp;dateline=$profilepic[dateline]&amp;type=avtar";
		}
		else {
			$img = null;
		}
 
		$src = urldecode($img);
	
		$location = $vbulletin->userinfo['field2'];
	
		$bod = $vbulletin->userinfo['birthday'];

		if(!empty($bod)){
			$year = explode("-",$bod);
			$bod = $year['2'];
		}
	
		// Get the user's description
		$description = $vbulletin->userinfo['field1'];
		
		// Create a new Auth instance
		$auth = new CCAuth ($user_id, $secret, $api_key, '');
	
		// Authenticate the user
		$auth->setUserInfo(
			$vbulletin->userinfo['username'], 
			$description, 
			null, 
			$bod, 
			$location, 
			null, 
			$src, 
			array('homepageLink' => $vbulletin->options['bburl'].'/member.php?'.$vbulletin->userinfo['userid'])
		);
	  
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

($hook = vBulletinHook::fetch_hook('ajax_complete')) ? eval($hook) : false;

/*======================================================================*\
|| ####################################################################
|| # 
|| # CVS: $RCSfile$ - $Revision: 31386 $
|| ####################################################################
\*======================================================================*/
?>