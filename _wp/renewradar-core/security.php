<?php
/**
 * Site hardening: XML-RPC off, login attempt limits, security headers, no file editing in wp-admin,
 * and fewer version and username leaks.
 */

defined( 'ABSPATH' ) || exit;

/* XML-RPC is not used (no Jetpack, no app publishing) and is a common password-guessing target. */
if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
	status_header( 403 );
	exit;
}
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'pings_open', '__return_false' );
add_filter( 'wp_headers', function ( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
} );

/* Theme and plugin files cannot be edited from wp-admin; a stolen session cannot plant code that way. */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/* Security headers come from the .htaccess block below; until it is written, WordPress pages send them here. */
add_action( 'send_headers', function () {
	header_remove( 'X-Powered-By' );
	if ( get_option( 'rr_htaccess_rev' ) ) {
		return;
	}
	foreach ( rr_security_headers() as $name => $value ) {
		header( $name . ': ' . $value );
	}
} );

function rr_security_headers() {
	return array(
		'X-Content-Type-Options'    => 'nosniff',
		'X-Frame-Options'           => 'SAMEORIGIN',
		'Referrer-Policy'           => 'strict-origin-when-cross-origin',
		'Permissions-Policy'        => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
		'Strict-Transport-Security' => 'max-age=31536000',
	);
}

/* Head links that only advertise WordPress internals. */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

/* ------------------------------------------------------------------ */
/* Login attempts                                                      */
/* ------------------------------------------------------------------ */

// Five wrong passwords from one address lock that address out for 15 minutes. Twenty wrong passwords for
// one username in an hour lock the username, which slows attacks spread over many addresses.
const RR_LOGIN_MAX_IP   = 5;
const RR_LOGIN_MAX_USER = 20;

function rr_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

function rr_login_keys( $username ) {
	return array(
		'ip'   => 'rr_login_ip_' . md5( rr_client_ip() ),
		'user' => 'rr_login_user_' . md5( strtolower( (string) $username ) ),
	);
}

add_filter( 'authenticate', function ( $user, $username ) {
	if ( '' === (string) $username ) {
		return $user;
	}
	$keys = rr_login_keys( $username );
	if ( (int) get_transient( $keys['ip'] ) >= RR_LOGIN_MAX_IP || (int) get_transient( $keys['user'] ) >= RR_LOGIN_MAX_USER ) {
		return new WP_Error( 'rr_locked', 'Too many failed sign-in attempts. Try again in 15 minutes.' );
	}
	return $user;
}, 30, 2 );

add_action( 'wp_login_failed', function ( $username ) {
	$keys = rr_login_keys( $username );
	set_transient( $keys['ip'], (int) get_transient( $keys['ip'] ) + 1, 15 * MINUTE_IN_SECONDS );
	set_transient( $keys['user'], (int) get_transient( $keys['user'] ) + 1, HOUR_IN_SECONDS );
} );

add_action( 'wp_login', function ( $username ) {
	$keys = rr_login_keys( $username );
	delete_transient( $keys['ip'] );
	delete_transient( $keys['user'] );
} );

// The same message for a wrong username and a wrong password, so usernames cannot be confirmed.
add_filter( 'login_errors', function ( $error ) {
	global $errors;
	if ( is_wp_error( $errors ) && in_array( 'rr_locked', $errors->get_error_codes(), true ) ) {
		return $error;
	}
	if ( is_wp_error( $errors ) && array_intersect( array( 'invalid_username', 'invalid_email', 'incorrect_password' ), $errors->get_error_codes() ) ) {
		return '<strong>Error:</strong> The username or password is incorrect.';
	}
	return $error;
} );

/* ------------------------------------------------------------------ */
/* .htaccess: block files that reveal the WordPress version, add headers to static files */
/* ------------------------------------------------------------------ */

const RR_HTACCESS_REV = 1;

function rr_htaccess_lines() {
	$lines = array(
		'<FilesMatch "^(readme\.html|license\.txt|wp-config-sample\.php|xmlrpc\.php)$">',
		'Require all denied',
		'</FilesMatch>',
		'<IfModule mod_headers.c>',
		'Header unset X-Powered-By',
	);
	foreach ( rr_security_headers() as $name => $value ) {
		$lines[] = 'Header always set ' . $name . ' "' . $value . '"';
	}
	$lines[] = '</IfModule>';
	return $lines;
}

// Written once from wp-admin, and again if a hosting tool rewrites .htaccess without it.
add_action( 'admin_init', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$file = ABSPATH . '.htaccess';
	$have = file_exists( $file ) && false !== strpos( (string) file_get_contents( $file ), '# BEGIN RenewRadar security' );
	if ( $have && (int) get_option( 'rr_htaccess_rev' ) === RR_HTACCESS_REV ) {
		return;
	}
	if ( ! is_writable( file_exists( $file ) ? $file : ABSPATH ) ) {
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	if ( insert_with_markers( $file, 'RenewRadar security', rr_htaccess_lines() ) ) {
		update_option( 'rr_htaccess_rev', RR_HTACCESS_REV );
	}
} );

register_deactivation_hook( RR_CORE_FILE, function () {
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	insert_with_markers( ABSPATH . '.htaccess', 'RenewRadar security', array() );
	delete_option( 'rr_htaccess_rev' );
} );
