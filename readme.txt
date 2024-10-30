=== Lana Single Sign On ===
Contributors: lanacodes
Donate link: https://www.paypal.com/donate/?hosted_button_id=F34PNECNYHSA4
Tags: sso, single sign on, oauth2, oauth 2.0, login
Requires at least: 4.0
Tested up to: 6.6
Stable tag: 1.2.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates the ability to login using Single Sign On via OAuth 2.0

== Description ==

Lana Single Sign On is an OAuth 2.0 client, which was primarily created for the Lana Passport OAuth 2.0 server plugin.

= Lana Codes =
[Lana Single Sign On](https://lana.codes/product/lana-sso/)
[Documentation](https://lana.solutions/documentation/lana-sso/)
[Lana Passport](https://lana.codes/product/lana-passport/)

== Installation ==

= Requires =
* WordPress at least 4.0
* PHP at least 5.6

= Instalation steps =

1. Upload the plugin files to the `/wp-content/plugins/lana-sso` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

= How to use it =
* in `Settings > Lana SSO`, you need to configure the OAuth server you want to use for login.

== Frequently Asked Questions ==

Do you have questions or issues with Lana Single Sign On?
Use these support channels appropriately.

= Lana Codes =
[Support](https://lana.codes/contact/)

= WordPress Forum =
[Support Forum](http://wordpress.org/support/plugin/lana-sso)

== Screenshots ==

1. screenshot-1.jpg
1. screenshot-2.jpg
1. screenshot-3.jpg

== Changelog ==

= 1.2.0 =
* add filters and actions
* fix wp_remote_post() usage
* reformat code

= 1.1.0 =
* add LANA_SSO_CLIENT_ID and LANA_SSO_CLIENT_SECRET constants
* add toastr for notifications
* bugfix error handling
* bugfix sso uri, use home_url() instead of site_url()
* fix text domain typo
* reformat code

= 1.0.2 =
* bugfix sso uri in settings page

= 1.0.1 =
* rename user_roles to roles due to WordPress core compatibility

= 1.0.0 =
* Added Lana Single Sign On

== Upgrade Notice ==

= 1.2.0 =
This version introduces new filters and actions. Upgrade recommended.

= 1.1.0 =
This version fixes sso uri and improves security and functionality. Upgrade recommended.

= 1.0.2 =
This version fixes sso uri in settings page. Upgrade recommended.

= 1.0.1 =
This version renames the 'roles' variable. Upgrade recommended.