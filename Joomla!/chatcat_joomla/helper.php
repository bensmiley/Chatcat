<?php
/**
 * Helper class for Chatcat module
 *
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @link http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * @license        GNU/GPL, see LICENSE.php
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die;

require_once(dirname(__FILE__) . '/CCAuth.php');

class modChatCatHelper
{

    public static function getAuth () {

        $user = JFactory::getUser();

        if($user->id > 0) {

            $module = JModuleHelper::getModule('mod_chatcat');
            $params = new JRegistry($module->params);

            $api_key = $params->get('chatcat-api-key');
            $secret = $params->get('chatcat-secret');

            // Create a new Auth instance
            $auth = new CCAuth ($user->id, $secret, $api_key, '');

            // Authenticate the user
            $auth->setUserInfo($user->username, null, null, null, null, null, null, array());

            return $auth;
        }
        else {
            return null;
        }
    }

    public static function chatcatAjax()
    {
        $auth = modChatCatHelper::getAuth();
        if($auth) {
            $app = JFactory::getApplication();
            $input = $app->input;
            $action = $input->get('action');
            if($action == 'cc_get_uid') {
            	$auth->respondWithUserID();
            	return null;
                //return json_decode($auth->UserIdJSON());
            }
            if($action == 'cc_get_token') {
            	$auth->respondWithToken();
            	return null;
                //return json_decode($auth->tokenJSON());
            }
        }
        else {
        	CCAuth::logout();
        	return null;
            //return json_decode(CCAuth::logoutJSON());
        }
    }

    public static function getChatJavascript($params)
    {

        $api_key = $params->get('chatcat-api-key');
        $secret = $params->get('chatcat-secret');

        $login_url = $params->get('chatcat-login-url');
        if ($login_url) {
            if (!preg_match("@^[hf]tt?ps?://@", $login_url)) {
                $login_url = "http://" . $login_url;
            }
        }
        else {
            $login_url = JRoute::_('index.php?option=com_users&view=login');
        }

        $register_url = $params->get('chatcat-register-url');
        if ($register_url) {
            if (!preg_match("@^[hf]tt?ps?://@", $register_url)) {
                $register_url = "http://" . $register_url;
            }
        }
        else {
            $register_url = JRoute::_('index.php?option=com_users&view=registration');
        }

        $primary_url = $params->get('chatcat-primary-url');
        if (!$primary_url) {
            $primary_url = '';
        }

        if ($api_key) {
            $single_sign_on_url = JURI::root() . "index.php?option=com_ajax&module=chatcat&method=chatcat&format=raw";
        } else {
            $single_sign_on_url = '';
        }

        $javascript =  "var CC_OPTIONS = {";
        $javascript .= "   primaryDomain:  '$primary_url',";
        $javascript .= "   usersCanCreatePublicRooms: true,";
        $javascript .= "   anonymousLoginEnabled: false,";
        $javascript .= "   socialLoginEnabled: true,";
        $javascript .= "   singleSignOnURL: '$single_sign_on_url',";
        $javascript .= "   singleSignOnAPILevel: 1,";
        $javascript .= "   loginURL: '$login_url',";
        $javascript .= "   registerURL: '$register_url',";
        $javascript .= "}";

        return $javascript;
    }


}
