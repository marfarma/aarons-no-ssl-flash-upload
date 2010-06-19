<?php
/*
Plugin Name: NO SSL Flash Upload
Plugin URI: http://aaron-kelley.net/tech/wordpress/plugin-flashssl/
Description: Workaround an "IO error" from the Flash uploader, caused by using an untrusted SSL certificate to secure admin sessions.  This is done by disabling SSL for the Flash uploader.  See the readme for security implications.  Requires WordPress 2.9 or later.
Version: 1.0.7
Author: Aaron A. Kelley
Author URI: http://aaron-kelley.net/
*/

/*  Copyright 2009-2010  Aaron A. Kelley  (email : aaronkelley@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Function for stripping https from references to async-upload.php, to allow
// the Flash uploader to work with a bad SSL certificate (by skipping SSL
// altogether).
function aaron_asyncURLFilter($url, $path)
{
    // If the PATH matches async-upload.php...
    if ($path == 'async-upload.php')
    {
        // AND the URL starts with https...
        if (strlen($url) >= strlen("https://") && substr($url, 0, strlen("https://")) == "https://")
        {
            // Replace https:// with http://.
            return "http://" . substr($url, strlen("https://"));
        }
    }

    // Otherwise, return the original URL.
    return $url;
}

// Function for cancelling any redirect of the async-upload.php page.
function aaron_asyncRedirectFilter($location, $status)
{
    if (strpos($location, 'async-upload.php') !== false && substr($location, strlen($location) - strlen('async-upload.php')) == 'async-upload.php')
    {
        return false;
    }

    return $location;
}

// Register the filters.
add_filter('admin_url', 'aaron_asyncURLFilter', 10, 2);
add_filter('wp_redirect', 'aaron_asyncRedirectFilter', 10, 2);

// Returns true if we are accessing async-upload.php.
function aaron_asyncReqDetect()
{
    if (false !== strpos($_SERVER['REQUEST_URI'], 'async-upload.php') && substr($_SERVER['REQUEST_URI'], strlen($_SERVER['REQUEST_URI']) - strlen('async-upload.php')) == 'async-upload.php')
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Don't exit after attempting to redirect accesses to async-upload.php...
// Also, check the logged in cookie instead of the auth cookie for access to
// async-upload.php.
if ( !function_exists('auth_redirect') ) :
/**
 * Checks if a user is logged in, if not it redirects them to the login page.
 *
 * @since 1.5
 */
function auth_redirect() {
	// Checks if a user is logged in, if not redirects them to the login page

	$secure = ( is_ssl() || force_ssl_admin() );

	// If https is required and request is http, redirect
	if ( $secure && !is_ssl() && false !== strpos($_SERVER['REQUEST_URI'], 'wp-admin') ) {
		if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
			wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
			if (!aaron_asyncReqDetect()) {
			    exit();
			}
		} else {
			wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			if (!aaron_asyncReqDetect()) {
			    exit();
			}
		}
	}

	if ( $user_id = wp_validate_auth_cookie( '', apply_filters( 'auth_redirect_scheme', '' ) ) ) {
		do_action('auth_redirect', $user_id);

		// If the user wants ssl but the session is not ssl, redirect.
		if ( !$secure && get_user_option('use_ssl', $user_id) && false !== strpos($_SERVER['REQUEST_URI'], 'wp-admin') ) {
			if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
				wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
				exit();
			} else {
				wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				exit();
			}
		}

		return;  // The cookie is good so we're done
	} else if (aaron_asyncReqDetect()) {
	    if ( empty($_COOKIE[LOGGED_IN_COOKIE]) || !wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in') ) {
	        $login_url = wp_login_url($redirect, true);

	        wp_redirect($login_url);
	        exit();
	    }
	    return;
	}

	// The cookie is no good so force login
	nocache_headers();

	if ( is_ssl() )
		$proto = 'https://';
	else
		$proto = 'http://';

	$redirect = ( strpos($_SERVER['REQUEST_URI'], '/options.php') && wp_get_referer() ) ? wp_get_referer() : $proto . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$login_url = wp_login_url($redirect, true);

	wp_redirect($login_url);
	exit();
}
endif;

// If we notice a request for async-upload.php, grab the right authentication cookie.
if ( !function_exists('wp_parse_auth_cookie') ) :
/**
 * Parse a cookie into its components
 *
 * @since 2.7
 *
 * @param string $cookie
 * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
 * @return array Authentication cookie components
 */
function wp_parse_auth_cookie($cookie = '', $scheme = '') {
	if ( empty($cookie) ) {
		switch ($scheme){
			case 'auth':
				$cookie_name = AUTH_COOKIE;
				break;
			case 'secure_auth':
				$cookie_name = SECURE_AUTH_COOKIE;
				break;
			case "logged_in":
				$cookie_name = LOGGED_IN_COOKIE;
				break;
			default:
				if ( is_ssl() || aaron_asyncReqDetect() ) {
				    if ( aaron_asyncReqDetect() && !is_ssl() ) {
				        $_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
				    }
					$cookie_name = SECURE_AUTH_COOKIE;
					$scheme = 'secure_auth';
				} else {
					$cookie_name = AUTH_COOKIE;
					$scheme = 'auth';
				}
	    }

		if ( empty($_COOKIE[$cookie_name]) )
			return false;
		$cookie = $_COOKIE[$cookie_name];
	}

	$cookie_elements = explode('|', $cookie);
	if ( count($cookie_elements) != 3 )
		return false;

	list($username, $expiration, $hmac) = $cookie_elements;

	return compact('username', 'expiration', 'hmac', 'scheme');
}
endif;

?>
