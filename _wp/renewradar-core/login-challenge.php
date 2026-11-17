<?php
/**
 * Human check on the wp-admin sign-in form. The visitor answers a small sum, a hidden field catches bots that
 * fill every input, and a form sent in under two seconds is refused. It is self-hosted, so no third party
 * sees who signs in. A failed check returns the error `rr_human`, which counts as a failed sign-in and so
 * feeds the five-attempt lockout in security.php.
 *
 * The sum is not stored anywhere: the form carries a time stamp signed with the site's secret key, and the
 * numbers are derived from that signature, so a bot cannot work out the question from the token alone.
 * It stops simple bots; the lockout and two-step sign-in are the strong defences.
 */

defined( 'ABSPATH' ) || exit;

const RR_HUMAN_MIN_SECONDS = 2;
const RR_HUMAN_MAX_SECONDS = 1800;
const RR_HUMAN_MESSAGE     = '<strong>Error:</strong> Please answer the human check correctly.';

function rr_human_sign( $issued ) {
	return hash_hmac( 'sha256', 'rr-human|' . (int) $issued, wp_salt( 'auth' ) );
}

function rr_human_make_token( $now = null ) {
	$issued = (int) ( null === $now ? time() : $now );
	return $issued . '.' . rr_human_sign( $issued );
}

/** The time stamp inside a genuine token, or null when the token is missing, malformed or forged. */
function rr_human_parse_token( $token ) {
	if ( ! is_string( $token ) || ! preg_match( '/^(\d{9,12})\.([0-9a-f]{64})$/', $token, $m ) ) {
		return null;
	}
	return hash_equals( rr_human_sign( $m[1] ), $m[2] ) ? (int) $m[1] : null;
}

/** The sum for a token: two numbers from 2 to 9 with plus or minus (never a negative answer). */
function rr_human_question( $issued ) {
	$h    = hash_hmac( 'sha256', 'rr-human-q|' . (int) $issued, wp_salt( 'auth' ), true );
	$a    = 2 + ord( $h[0] ) % 8;
	$b    = 2 + ord( $h[1] ) % 8;
	$plus = 0 === ord( $h[2] ) % 2;
	if ( ! $plus && $a < $b ) {
		list( $a, $b ) = array( $b, $a );
	}
	return array(
		'text'   => $a . ( $plus ? ' + ' : ' - ' ) . $b,
		'answer' => $plus ? $a + $b : $a - $b,
	);
}

/**
 * @return true|string True when the check passes, otherwise a short reason code
 *                     (honeypot, token, fast, expired, answer). The reason is never shown to the visitor.
 */
function rr_human_verify( $token, $answer, $honeypot, $now = null ) {
	$now = (int) ( null === $now ? time() : $now );
	if ( '' !== trim( (string) $honeypot ) ) {
		return 'honeypot';
	}
	$issued = rr_human_parse_token( $token );
	if ( null === $issued ) {
		return 'token';
	}
	$age = $now - $issued;
	if ( $age < RR_HUMAN_MIN_SECONDS ) {
		return 'fast';
	}
	if ( $age > RR_HUMAN_MAX_SECONDS ) {
		return 'expired';
	}
	$answer = trim( (string) $answer );
	if ( ! preg_match( '/^-?\d{1,3}$/', $answer ) ) {
		return 'answer';
	}
	$expected = rr_human_question( $issued );
	return (int) $answer === $expected['answer'] ? true : 'answer';
}

/** True only for the real sign-in form being submitted, so REST and application passwords are not affected. */
function rr_is_login_form_post() {
	global $pagenow;
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '';
	if ( 'wp-login.php' !== $pagenow || 'POST' !== $method || ! isset( $_POST['log'], $_POST['pwd'] ) ) {
		return false;
	}
	return ! isset( $_REQUEST['action'] ) || 'login' === $_REQUEST['action'];
}

add_action( 'login_form', function () {
	$token  = rr_human_make_token();
	$sum    = rr_human_question( rr_human_parse_token( $token ) );
	echo '<p class="rr-human"><label for="rr_human_answer">Human check: what is ' . esc_html( $sum['text'] ) . '?</label>';
	echo '<input type="text" name="rr_human_answer" id="rr_human_answer" class="input" inputmode="numeric" pattern="-?[0-9]*" autocomplete="off" size="6" required></p>';
	echo '<input type="hidden" name="rr_human_token" value="' . esc_attr( $token ) . '">';
	echo '<p class="rr-hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="rr_hp_x7" tabindex="-1" autocomplete="off" value=""></label></p>';
} );

add_action( 'login_head', function () {
	echo '<style>.rr-hp{position:absolute!important;left:-9999px!important;width:1px;height:1px;overflow:hidden}.rr-human{margin:0 0 16px}.rr-human label{display:block;margin-bottom:3px}</style>';
} );

/*
 * Runs after the password has been checked on purpose. A failed human check always returns the same
 * error, whether or not the password was right, so a bot cannot use the error to test passwords.
 * A lockout error wins, so a locked address sees that it is locked.
 */
add_filter( 'authenticate', function ( $user ) {
	if ( ! rr_is_login_form_post() ) {
		return $user;
	}
	if ( is_wp_error( $user ) && 'rr_locked' === $user->get_error_code() ) {
		return $user;
	}
	$ok = rr_human_verify(
		isset( $_POST['rr_human_token'] ) ? wp_unslash( $_POST['rr_human_token'] ) : '',
		isset( $_POST['rr_human_answer'] ) ? wp_unslash( $_POST['rr_human_answer'] ) : '',
		isset( $_POST['rr_hp_x7'] ) ? wp_unslash( $_POST['rr_hp_x7'] ) : ''
	);
	return true === $ok ? $user : new WP_Error( 'rr_human', RR_HUMAN_MESSAGE );
}, 99 );
