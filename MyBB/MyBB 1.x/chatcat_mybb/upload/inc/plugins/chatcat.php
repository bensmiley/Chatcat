<?php
//Disallow direct Initialization for extra security.
if(!defined("IN_MYBB"))
{
    die("You Cannot Access This File Directly. Please Make Sure IN_MYBB Is Defined.");
}

// Hooks
//$plugins->add_hook('global_start', 'chatcat_global_start');
$plugins->add_hook('admin_config_settings_change', 'chatcat_add_chat_options');
$plugins->add_hook('pre_output_page', 'chatcat_insert_footer');
 
 // Information
function chatcat_info() {
    return array(
        "name"  => "Chatcat",
        "description"=> "Chatcat instant messenger",
        "website"        => 'http://chatcat.io',
        "author"        => "Ben Smiley-Andrews",
        "authorsite"    => 'http://chatcat.io',
        "version"        => "1.0",
        "guid"             => "",
        "compatibility" => "1*"
    );
}

// Activate
function chatcat_activate() {

    global $db;

    $chatcat_group = array(
        'gid'    => 'NULL',
        'name'  => 'chatcat',
        'title'      => 'Chatcat',
        'description'    => '',
        'disporder'    => "1",
        'isdefault'  => "0",
    );
	
	$db->insert_query('settinggroups', $chatcat_group);
	$gid = $db->insert_id(); 

    $chatcat_setting = array(
        'sid'         => 'NULL',
        'name'        => 'chatcat_enable',
        'title'       => 'Do you want to enable Chatcat?',
        'description' => 'If you set this option to yes, this plugin be active on your board.',
        'optionscode' => 'yesno',
        'value'       => '1',
        'disporder'   => 1,
        'gid'         => intval($gid),
    );
	
    $chatcat_setting_api = array(
		'name'        => 'chatcat_enable_api',
		'title'       => 'Chatcat API Key',
		'description' => "If left blank, single sign on will be disabled.",
		'optionscode' => 'text',
		'value'       => '',
		'disporder'   => 2,
		'gid'         => intval($gid),
	);

    $chatcat_setting_secret = array(
        'name'        => 'chatcat_secret',
        'title'       => 'Chatcat Secret',
        'description' => "Your secret can be found at dev.chatcatapp.com",
        'optionscode' => 'text',
        'value'       => '',
        'disporder'   => 2,
        'gid'         => intval($gid),
    );

    $chatcat_setting_login = array(
		'name' => 'chatcat_enable_login',
		'title' => 'Login URL',
		'description' => "Enter custom login URL",
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 3,
		'gid' => intval($gid),
	);
    
    $chatcat_setting_register = array(
		'name' => 'chatcat_enable_register',
		'title' => 'Registration URL',
		'description' => "Enter custom registration URL",
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 4,
		'gid' => intval($gid),
	);
    
    $chatcat_setting_primary = array(
		'name' => 'chatcat_enable_primary',
		'title' => 'Primary URL',
		'description' => "",
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 5,
		'gid' => intval($gid),
	);
    
	$db->insert_query('settings', $chatcat_setting);
    $db->insert_query('settings', $chatcat_setting_api);
	$db->insert_query('settings', $chatcat_setting_secret);
	$db->insert_query('settings', $chatcat_setting_login);
	$db->insert_query('settings', $chatcat_setting_register);
	$db->insert_query('settings', $chatcat_setting_primary);
	rebuild_settings();
}

// Deactivate
function chatcat_deactivate() {
    global $db;
	
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('chatcat_enable','chatcat_enable_api', 'chatcat_secret','chatcat_enable_login','chatcat_enable_register','chatcat_enable_primary')");
    $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='chatcat'");
	
    rebuild_settings();
} 

function chatcat_add_chat_options() {
//    global $mybb, $lang, $form, $forum_data;
//    if($mybb->request_method == "post") {
//        if(strlen($mybb->input['upsetting']['chatcat_enable_api']) == 0  || (strlen($mybb->input['upsetting']['chatcat_enable_api']) == 40 )){
//
//        }
//        else{
//			flash_message('API Key Invalid', 'error');
//			admin_redirect("index.php?module=config-settings");
//		}
//	}
    
    $mybb_login_url = $mybb->input['upsetting']['chatcat_enable_login'];
    $mybb_register_url = $mybb->input['upsetting']['chatcat_enable_register'];
    
    if(!filter_var($mybb_login_url, FILTER_VALIDATE_URL) && strlen($mybb_login_url) > 0){
        flash_message('Missing http:// prefix in Login URL', 'error');
        admin_redirect("index.php?module=config-settings&action=".$mybb_action."&gid=".$mybb_gid);
    }
    
    if(!filter_var($mybb_register_url, FILTER_VALIDATE_URL) && strlen($mybb_register_url) > 0) {
        flash_message('Missing http:// prefix in Registration URL', 'error');
        admin_redirect("index.php?module=config-settings&action=".$mybb_action."&gid=".$mybb_gid);
    }
    
}
 
function chatcat_insert_footer() {
    
    global $mybb, $templates, $myval;
    $myval ="";
    
    if ($mybb->settings['chatcat_enable'] == 1) {

        $apikey = $mybb->settings['chatcat_enable_api'];
 
        $api_url = '';
        if(strlen($mybb->settings['chatcat_enable_api']) != 0){
            
            $user = $mybb->user;
            
            $api_key = $mybb->settings['chatcat_enable_api'];
            $secret = $mybb->settings['chatcat_secret'];
            $uid = $user['uid'];
            
            $useravatar = format_avatar(htmlspecialchars_uni($user['avatar']), $user['avatardimensions'], my_strtolower($mybb->settings['memberlistmaxavatarsize']));
            
            if(!empty($useravatar['image'])){
                $src = $useravatar['image'];
            }
            
            $username = $user['username'];
            $dateOfBirth = strtotime($user['birthday']) * 1000;
            
            // Get the user's description
            $description = $user['fid2'];
            $location = $user['fid1'];
            
            $params = array(
                'api_key' => $api_key,
                'secret' => $secret,
                'src' => urlencode($src),
                'username' => $username,
                'description' => $description,
                'uid' => $uid,
                'dateOfBirth' => $dateOfBirth,
                'location' => $location,
                'homepage' => urlencode($mybb->settings['bburl']."/".get_profile_link($uid))
            );
            
            $extension = '?';
            foreach($params as $key => $value) {
                if($value) {
                    $extension .= $key . '=' . $value . '&';
                }
            }
            $extension = substr($extension, 0, -1);
            
            $api_url = $mybb->settings['bburl'] . '/ajaxchat.php' . $extension;
        }
 
        if($mybb->settings['chatcat_enable_login'] == '') {
            $login_url = $mybb->settings['bburl'].'/member.php?action=login';
        }
        else {
            $login_url = $mybb->settings['chatcat_enable_login'];
            if (!preg_match("@^[hf]tt?ps?://@", $login_url)) {
                $login_url = "http://" . $login_url;
            }
        }
        
        if(($mybb->settings['chatcat_enable_register'] == '')) {
            $register_url = $mybb->settings['bburl']. '/member.php?action=register';
        }
        else {
            $register_url = $mybb->settings['chatcat_enable_register'];
            if (!preg_match("@^[hf]tt?ps?://@", $register_url)) {
                $register_url = "http://" . $register_url;
                
            }
        }
        
        if($mybb->settings['chatcat_enable_primary'] != ''){
            $primary_url = $mybb->settings['chatcat_enable_primary'];
        }
        else {
            $primary_url = $mybb->settings['bburl'];
        }
        
    ?>

    <div ng-app="myApp" ><ng-include src=" baseURL + 'chatcat.html'" ng-controller="AppController"></ng-include></div>
    <script type="text/javascript">

        // Set options here
        var CC_OPTIONS = {

            
            primaryDomain: '<?php echo $primary_url  ?>',
			        

            // Users can create public chat rooms?
            // If this is true users will be able to setup new
            // public rooms
            usersCanCreatePublicRooms: true,

            // Allow anonymous login?
            anonymousLoginEnabled: false,

            // Enable social login - please email us to get your domain whitelisted
            socialLoginEnabled: true,

            // The URL to contact for single sign on
            singleSignOnURL: '<?php echo $api_url ?>',
            singleSignOnAPILevel: 1,

            // Optional - if this is set the login box will direct users
            // to log in
            loginURL: '<?php echo $login_url?>',

            // Optional - if this is set the login box will direct users
            // to register
            registerURL: '<?php echo $register_url?>'

        }

        var ccProtocol = (("https:" == document.location.protocol) ? "https://" : "http://");

    <?php if ($_SERVER['SERVER_NAME'] == 'ccwp') { ?>

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
}
?>