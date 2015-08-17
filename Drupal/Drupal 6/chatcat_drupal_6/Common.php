<?php
/**
 * Created by PhpStorm.
 * User: benjaminsmiley-andrews
 * Date: 22/07/15
 * Time: 09:40
 */

class Common {

    public static function getIncludeCode ($primary_domain, $sso_url, $login_url, $register_url) {

        $output ='';
        $output .= "<div ng-app=\"myApp\" ><ng-include src=\" baseURL + 'chatcat.html'\" ng-controller=\"AppController\"></ng-include></div>
    <script type=\"text/javascript\">";

        // Set options here
        $output .= "var CC_OPTIONS = {";

        $output .= "primaryDomain: '$primary_domain',";

        // Users can create public chat rooms?
        // If this is true users will be able to setup new
        // public rooms
        $output .= "usersCanCreatePublicRooms: true,";

        // Allow anonymous login?
        $output .= "anonymousLoginEnabled: false,";

        // Enable social login - please email us to get your domain whitelisted
        $output .= "socialLoginEnabled: true,";

        // The URL to contact for single sign on
        $output .= "singleSignOnURL: '$sso_url',";
        $output .= "singleSignOnAPILevel: 1,";

        // Optional - if this is set the login box will direct users
        // to log in
        $output .= "loginURL: '$login_url',";

        // Optional - if this is set the login box will direct users
        // to register
        $output .= "registerURL: '$register_url'";

        $output .= "};";

        $output .= "var ccProtocol = (('https:' == document.location.protocol) ? 'https://' : 'http://');";

        // PRODUCTION
        $output .= "document.write(decodeURI(\"%3Clink rel='stylesheet' href='\" + ccProtocol + \"chatcat.firebaseapp.com/css/_/cc_styles.min.css' %3E%3C/link%3E\"));";
        $output .= "document.write(decodeURI(\"%3Cscript src='\" + ccProtocol + \"chatcat.firebaseapp.com/js/all.min.js' type='text/javascript'%3E%3C/script%3E\"));";

        $output .= "</script>";

        return $output;
    }

} 