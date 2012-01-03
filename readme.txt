=== Plugin Name ===
Contributors: aaron44126
Tags: flash, upload, ssl
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: 1.0.8

Turns off SSL for the Flash uploader when you have FORCE_SSL_ADMIN enabled, in
case you are having trouble getting it to work ("IO Error").

== Description ==

If you are using SSL (https) to secure your WordPress admin sessions and you
have an SSL certificate that is not trusted by default (because it is self-
signed, signed by an untrusted certificate authority, signed for a different
domain name, etc.), then you probably have problems using the Flash uploader.

This plugin disables SSL usage by the Flash uploader.  This allows you to use
the Flash uploader when you have FORCE_SSL_ADMIN enabled, with an untrusted SSL
certificate.  This works around the vague "IO Error" you get from the Flash
uploader in such a situation.

Note that this plugin comes with the following security implications:

* Flash uploads no longer use SSL, thus, your uploaded files aren't encrypted
  during transmission.
* Uploading files with the Flash uploader will transmit your WordPress
  authentication cookie in plain text.
* If someone captures your login cookie (which is transmitted any time you load
  a page on your WordPress site while logged in, whether you are using SSL or
  not), they may be able to use it to upload files, view information about
  uploaded files, or change information about uploaded files.

If the benefit of having the Flash uploader available outweighs these potential
security risks for you, then you can use this plugin to enable the Flash
uploader.

Note that this plugin override's WordPress's auth_redirect and
wp_validate_auth_cookie functions, and may not work if you are using other
plugins that override these functions.

== Changelog ==

= 1.0.8 =
* February 28, 2011
* Updated the auth_redirect function to match changes made in WordPress 3.1.
  Now requires WordPress 3.1.  WordPress 3.0.x users may use version 1.0.7.

= 1.0.7 =
* June 19, 2010
* Updated the auth_redirect function to match changes made in WordPress 3.0.
  Now requires WordPress 3.0.  WordPress 2.9.x users may use version 1.0.6.

= 1.0.6 =
* February 15, 2010
* Changed the name of the plugin.  I don't think it needs to have my name in
  the title after all.
* Minor updates to documentation.
* Reverted "code collapse" change from 1.0.4 (no change in functionality, just
  code style).

= 1.0.5 =
* January 4, 2010
* Updated the auth_redirect function to match changes made in WordPress 2.9.
  Now requires WordPress 2.9.  WordPress 2.8.x users can use version 1.0.4.
* Minor updates to documentation.

= 1.0.4 =
* December 18, 2009
* Fixed a bug that prevented viewing and editing media information immediately
  after upload with WordPress 2.9.
* Changed code in the overridden functions so that all of my changes are on one
  line if possible.  This is to make it easier to check against the original
  WordPress functions for changes when a new version comes along.

= 1.0.3 =
* December 14, 2009
* Fixed up readme.
* Now available in the WordPress plugin directory.

= 1.0.2 =
* December 11, 2009
* Fixed error activating plugin, extra installation instructions no longer
  necessary.

= 1.0.1 =
* December 10, 2009
* Added "Installation process" section to readme, in case of errors activating
  the plugin.

= 1.0 =
* December 10, 2009
* Initial release.
