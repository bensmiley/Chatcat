<?php if(!defined('VB_ENTRY')) die('Access denied.');

/**
 * vB_Api_User
 *
 * @package vBApi
 * @access public
 */
class vB_Api_Chatcat extends vB_Api
{

	public function get_admin_detail_chat(){
	
		global $vbulletin;
		
		// Get the settings options and the user's info
		$options = vB::getDatastore()->getValue('options');
		$userinfo = vB::getCurrentSession()->fetch_userinfo();
		
		// Api Key and Secret
		$api = $options['chat_cat_messenger_api'];
 		$secret = $options['chat_cat_messenger_secret'];
 		$login_url = $options['chat_cat_messenger_login_url'];
 		$register_url = $options['chat_cat_messenger_register_url'];
 		$primary_domain = $options['chat_cat_messenger_primary_url'];

// 		echo $primary_domain;
// 		die();

		// User info		
		$uname = $userinfo['username'];
		$uid = $userinfo['userid'];

		$desc = $userinfo['field1'];
		$src = '';
		$home = $options['bburl'].'/member/'.$uid;
		//echo $home;die;
		$location = $userinfo['field2'];
		
		$bod = $userinfo['birthday'];
		
		if(!empty($bod)){
			$year = explode("-",$bod);
			$bod = $year['2'];
		}
		
		$profilepic = $vbulletin->db->query_first("
			SELECT userid, dateline, height, width
			FROM " . TABLE_PREFIX . "customavatar
			WHERE userid = " . $uid
		);
		
		if(($profilepic['dateline'])){
			$img = $options['bburl']. '/image.php?userid=' . $uid . '&thumb=1&dateline='.$profilepic[dateline].'&type=avtar';
		}
		else{
			$img = null;
		}

		$src = urlencode($img);
		$home = urlencode($home);
		//$src = urldecode($img);
		
		$chatdata =array();
		
		$chatdata['sinonurl'] = '';
		if(isset($api) && $api != '') {
		   $chatdata['sinonurl'] = '/ajaxchat.php?uid='.$uid.'&api='.$api.'&secret='.$secret.'&uname='.$uname.'&desc='.$desc.'&src='.$src.'&home='.$home.'&birthday='.$bod.'&location='.$location;
		}
	
	    $chatdata['primaryurl'] = $primary_domain;
		
		if($login_url != '') {
			$chatdata['loginurl'] = $login_url;
			if (!preg_match("@^[hf]tt?ps?://@", $chatdata['loginurl'])) {
				$chatdata['loginurl'] = "http://" . $chatdata['loginurl'];
			}
		}
		else{
			$chatdata['loginurl'] = '';
		}
		
		if($register_url != '') {
			$chatdata['registerurl'] = $register_url;
			if (!preg_match("@^[hf]tt?ps?://@", $chatdata['registerurl'])) {
				$chatdata['registerurl'] = "http://" . $chatdata['registerurl'];
			}
		}
		else {
			$chatdata['registerurl'] = $options['frontendurl'].'/register';
		}

		return $chatdata;
	}

}