== Readme ==

If you are using SSL (https) to secure your WordPress admin sessions and you
have an SSL certificate that is not trusted by default (because it is self-
signed, signed by an untrusted certificate authority, signed for a different
domain name, etc.), then you probably have problems using the Flash uploader.

This plug-in disables SSL usage by the Flash uploader.  This allows you to use
the Flash uploader when you have FORCE_SSL_ADMIN enabled, with an untrusted SSL
certificate.  This works around the vague "IO Error" you get from the Flash
uploader in such a situation.

Note that this plug-in comes with the following security implications:

* Flash uploads no longer use SSL, thus, your uploaded files aren't encrypted
  during transmission.
* Uploading files with the Flash uploader will transmit your WordPress
  authentication cookie in plain text.
* If someone captures your login cookie (which is transmitted any time you load
  a page on your WordPress site while logged in, whether you are using SSL or
  not), they may be able to use it to upload files, view information about
  uploaded files, or change information about uploaded files.


== Changelog ==

= 1.0.2 =
* December 11, 2009.
* Fixed error activating plug-in, extra installation instructions no longer
  necessary.

= 1.0.1 =
* December 10, 2009.
* Added "Installation process" section to readme, in case of errors activating
  the plug-in.

= 1.0 =
* December 10, 2009
* Initial release.
