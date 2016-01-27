<?php
/*
Plugin Name: Chatcat Messenger
Plugin URI: http://chatcat.io/support/wordpress-plugin/
Description: Add Chatcat instant messenger to your website
Version: 1.0.2
Author: Ben Smiley-Andrews
Author URI: http://chatcat.io
License: GPL2

LICENSE

Copyright 2015  Ben Smiley-Andrews  (email : ben@chatcatapp.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

require_once 'CCAuth.php';

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Get the current user
if ( !defined('__DIR__') ) define('__DIR__', dirname(__FILE__));
$path = explode('wp-content',__DIR__);
include_once($path[0].'wp-load.php');

// Add a function to allow the client to get
// the
add_action('wp_ajax_cc_get_token', 'cc_get_token');
add_action('wp_ajax_nopriv_cc_get_token', 'cc_logout');

add_action('wp_ajax_cc_get_uid', 'cc_get_uid');
add_action('wp_ajax_nopriv_cc_get_uid', 'cc_logout');

function getAuth () {

    global $current_user;

    $doc = new DOMDocument();
    $doc->loadHTML(get_avatar($current_user->ID, 100));
    $xpath = new DOMXPath($doc);
    $src = $xpath->evaluate('string(//img/@src)');
    $exp = explode('&d=', $src);
    $src = urldecode($exp[1]);

    // Is the user logged in?
    if($current_user->ID > 0) {
        // Get the API key from the settings
        $options = get_option('cc_options');

        // Get the user's description
        $description = get_the_author_meta('description', $current_user->ID, $_SERVER['HTTP_HOST']);

        // Create a new Auth instance
        $auth = new CCAuth ($current_user->ID, $options['secret'], $options['api_key'], '');

        // Authenticate the user
        $auth->setUserInfo($current_user->display_name, $description, null, null, null, null, $src,
            array(
//                'homepageLink' => 'http://www.google.co.uk/'.$current_user->ID,
//                'homepageText' => 'Profile'
                //'friends' => array('simplelogin:8039')
                //'publicRoomsEnabled' => false,
                //'onlineUsersEnabled' => true,
                //'friends' => array('simplelogin:1', '2222222222222222222222222222222222222222:2')
            )
        );

        return $auth;
    }
    return null;
}

function cc_get_token () {

    $auth = getAuth();
    
    if($auth) {
        $auth->respondWithToken();
    }
    else {
        CCAuth::logout();
    }
    die();
}

function cc_get_uid () {

    $auth = getAuth();
    if($auth) {
        $auth->respondWithUserID();
    }
    else {
        CCAuth::logout();
    }
    die();
}

function cc_logout () {
    CCAuth::logout();
    die();
}

function cc_add_footer () {

    // Get the URL for the module this will let us set the
    $module_url = plugin_dir_url(__FILE__);

    

    $options = get_option('cc_options');

    // Get the URL of the API connector script
    //$api_url = $module_url . 'cc-auth-api.php';
    $api_url = strlen($options['api_key']) ?  admin_url('admin-ajax.php') : '';

    // If the API key isn't set disable the API URL
    $secret = $options['secret'];
    if(!isset($secret) || strlen($secret) != 10) {
        $api_url = '';
    }

    // Setup the login and register URLs
    $login_url = $options['login'];
    if(!isset($login_url) || strlen($login_url) == 0) {
        $login_url = wp_login_url();
    }

    // Setup the login and register URLs
    $register_url = $options['register'];
    if(!isset($register_url) || strlen($register_url) == 0) {
        $register_url = wp_registration_url();
    }

    $primary_url = $options['primary_url'];

    $config_options = $options['config_options'];

    // Include the chat in the footer
    ?>

    <div ng-app="myApp" ><ng-include src=" baseURL + 'chatcat.html'" ng-controller="AppController"></ng-include></div>
    <script type="text/javascript">

        // Set options here
        var CC_OPTIONS = {

            <?php if(isset($primary_url) && strlen($primary_url) > 0): ?>
            primaryDomain: '<?php echo $primary_url  ?>',
            <?php endif ?>



            // Users can create public chat rooms?
            // If this is true users will be able to setup new
            // public rooms
            //usersCanCreatePublicRooms: true,

            // Allow anonymous login?
            //anonymousLoginEnabled: true,

            // Enable social login - please email us to get your domain whitelisted
            //socialLoginEnabled: true,

            // The URL to contact for single sign on
            singleSignOnURL: '<?php  echo $api_url ?>',
            singleSignOnAPILevel: 1,

			apiLevel: 1,


            //disableUserNameChange: false,

            // Optional - if this is set the login box will direct users
            // to log in
            loginURL: '<?php echo $login_url?>',

            // Optional - if this is set the login box will direct users
            // to register
            registerURL: '<?php echo $register_url?>',

            <?php
             if(isset($config_options) && strlen($config_options)) {
                echo $config_options;
             }
             ?>

        }

        var ccProtocol = (('https:' == document.location.protocol) ? 'https://' : 'http://');

    <?php if (!$_GET['prod']) { ?>

        // TEST
        document.write(decodeURI("%3Clink rel='stylesheet' href='" + ccProtocol + "chatcat/dist/css/_/cc_styles.min.css' %3E%3C/link%3E"));
        document.write(decodeURI("%3Cscript src='" + ccProtocol + "chatcat/dist/js/all.js' type='text/javascript'%3E%3C/script%3E"));

    <?php } else { ?>

        // PRODUCTION
        document.write(decodeURI("%3Clink rel='stylesheet' href='" + ccProtocol + "chatcat.firebaseapp.com/css/_/cc_styles.min.css' %3E%3C/link%3E"));
        document.write(decodeURI("%3Cscript src='" + ccProtocol + "chatcat.firebaseapp.com/js/all.min.js' type='text/javascript'%3E%3C/script%3E"));

    <?php } ?>

    </script>

<?php

}

add_action( 'wp_footer', 'cc_add_footer' );

/**
 * Add the admin menu
 */

add_action( 'admin_menu', 'cc_plugin_menu' );

function cc_plugin_menu() {
    add_options_page( 'Chatcat', 'Chatcat', 'manage_options', 'cc_plugin', 'cc_plugin_options_page' );
}

// Print the options page
function cc_plugin_options_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

?>
    <div class="wrap">
        <form action="options.php" method="post">
            <?php settings_fields('cc_plugin_secret'); ?>
            <?php do_settings_sections('cc_plugin'); ?>
            <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
        </form>
    </div>
<?php
}

// Generate content for setting subtitle
function cc_plugin_section_text() {
    echo '<p>Enter your secret here to enable single sign on. The key must be 10 characters long. If login or register URLs are not provided the Wordpress default will be used.</p>';
}

// Generate text input box
function cc_print_secret_field() {
    $options = get_option('cc_options');
    $value = isset($options['secret']) ? $options['secret'] : '';
    echo "<input id='cc_plugin_text_string_secret' name='cc_options[secret]' size='10' type='text' value='{$value}' />";
}

function cc_print_api_key_field() {
    $options = get_option('cc_options');
    $value = isset($options['api_key']) ? $options['api_key'] : '';
    echo "<input id='cc_plugin_text_string_api_key' name='cc_options[api_key]' size='40' type='text' value='{$value}' />";
}

function cc_print_login_key_field() {
    $options = get_option('cc_options');
    $value = isset($options['login']) ? $options['login'] : '';
    echo "<input id='cc_plugin_text_string_login' name='cc_options[login]' size='40' type='text' value='{$value}' />";
}

function cc_print_register_key_field() {
    $options = get_option('cc_options');
    $value = isset($options['register']) ? $options['register'] : '';
    echo "<input id='cc_plugin_text_string_register' name='cc_options[register]' size='40' type='text' value='{$value}' />";
}

function cc_print_primary_url_key_field() {
    $options = get_option('cc_options');
    $value = isset($options['primary_url']) ? $options['primary_url'] : '';
    echo "<input id='cc_plugin_text_string_primary_url' name='cc_options[primary_url]' size='40' type='text' value='{$value}' />";
}

function cc_print_config_options_field() {
    $options = get_option('cc_options');
    $value = isset($options['config_options']) ? $options['config_options'] : '';
    //echo "<input id='cc_plugin_text_string_config_options' name='cc_options[config_options]' size='40' type='text' value='{$value}' />";
    echo "<textarea id='cc_plugin_text_string_config_options' cols=\"39\" rows=\"10\" name='cc_options[config_options]'>{$value}</textarea>";
}


// Validation
function plugin_options_validate($input) {

    // Validate Secret
    $secret = trim($input['secret']);
    if(!preg_match('/^[a-zA-Z0-9]{10}$/i', $secret)) {
        $secret = '';
    }
    $input['secret'] = $secret;

    // Validate the login URL
    if(strlen($input['login']) != 0) {
        $input['login'] = esc_url($input['login']);
    }

    // Validate register URL
    if(strlen($input['register']) != 0) {
        $input['register'] = esc_url($input['register']);
    }

    if(strlen($input['primary_url']) != 0) {
        $url = $input['primary_url'];
        $url = str_replace('www.', '', $url);
        $parse = parse_url(esc_url($url));
        $input['primary_url'] = $parse['host'];
    }

    return $input;
}

add_action('admin_init', 'chatcat_admin_init');

function chatcat_admin_init(){
    register_setting( 'cc_plugin_secret', 'cc_options', 'plugin_options_validate' );
    add_settings_section('cc_plugin_main', 'Chatcat Settings', 'cc_plugin_section_text', 'cc_plugin');
    add_settings_field('cc_plugin_text_string_secret', 'Secret', 'cc_print_secret_field', 'cc_plugin', 'cc_plugin_main');
    add_settings_field('cc_plugin_text_string_api_key', 'API Key', 'cc_print_api_key_field', 'cc_plugin', 'cc_plugin_main');

    add_settings_field('cc_plugin_text_string_login', 'Login Page URL (optional)', 'cc_print_login_key_field', 'cc_plugin', 'cc_plugin_main');
    add_settings_field('cc_plugin_text_string_register', 'Register Page URL (optional)', 'cc_print_register_key_field', 'cc_plugin', 'cc_plugin_main');
    add_settings_field('cc_plugin_text_string_primary_url', 'Primary URL (optional)', 'cc_print_primary_url_key_field', 'cc_plugin', 'cc_plugin_main');
    add_settings_field('cc_plugin_text_string_config_options', 'Configuration Options (optional)', 'cc_print_config_options_field', 'cc_plugin', 'cc_plugin_main');

}