=== Chatcat Messenger ===
Contributors: bensmiley
Tags: chat, messenger, chatcat, instant messenger
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

This plugin integrates the Chatcat messenger with Wordpress. It adds the messenger popup to each page and integrates Chatcat with the Wordpress login system. 

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the chatcat-messenger directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to the main admin page and click Settings -> Chatcat
4. If you want to Chatcat to integrate with you site’s login system enter your API key
5. If you have custom login or register pages enter them
6. If this is a subdomain and you want to connect it with the main site, enter the domain of the main website in the primary URL box. i.e. if I had registered chat.com with Chatcat.io but I wanted to add chat to forum.chat.com; On forum.chat.com I’d set the primary URL as chat.com

== Frequently Asked Questions ==

= I’ve installed the module. Why doesn’t the chat appear? =
This is usually because your domain isn’t registered properly. To setup a new chat you need to:

1. Create an account on chatcat.io
2. Register your domain (without the www.) and get your API key
3. Add your API key to the Wordpress settings page

If this doesn’t work open the JavaScript console and look for any error messages. If you find them you can send an email to support@chatcat.io

= I don’t want to authenticate users using Wordpress =

To do this you just need to leave the API key field blank

== Screenshots ==

== Changelog ==

= 1.0.2 =
* Fixed bug that could cause a conflict with other Wordpress plugins
* Update authentication server

= 1.0.1 =
* Fixed small bugs with authentication

= 1.0.0 =
* Initial version


== Upgrade Notice ==

= 1.0.2 =
This version updates the authentication server which will improve the chat performance and fixes some small bugs
