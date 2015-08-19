<?php
/**
 * Chatcat Module Entry Point
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
 
// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once( dirname(__FILE__) . '/helper.php' );
require_once( dirname(__FILE__) . '/CCAuth.php' );
require_once( JModuleHelper::getLayoutPath('mod_chatcat'));

$app = JFactory::getApplication();
$router = $app->getRouter();
$router->setMode(0);

$javascript = modChatCatHelper::getChatJavascript($params);

$document = JFactory::getDocument();
$document->addScriptDeclaration( $javascript );

// Add the javascript code
$document->addScript(JURI::base().'modules/mod_chatcat/assets/chatcat.js' );

