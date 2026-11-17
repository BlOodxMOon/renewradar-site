<?php
/**
 * Tests for the sign-in human check. Run: php _wp/tests/login-challenge-test.php
 * WordPress is replaced by small stubs, so this checks the logic, not the live sign-in page.
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['rr_hooks'] = array();
function add_action( $name, $fn, $priority = 10 ) {
	$GLOBALS['rr_hooks'][] = array( $name, $fn, $priority );
}
function add_filter( $name, $fn, $priority = 10 ) {
	$GLOBALS['rr_hooks'][] = array( $name, $fn, $priority );
}
function wp_salt( $scheme = 'auth' ) {
	return 'test-salt-' . $scheme;
}
function wp_unslash( $value ) {
	return $value;
}
function esc_html( $s ) {
	return htmlspecialchars( (string) $s, ENT_QUOTES );
}
function esc_attr( $s ) {
	return htmlspecialchars( (string) $s, ENT_QUOTES );
}
class WP_Error {
	public $code;
	public $message;
	public function __construct( $code = '', $message = '' ) {
		$this->code    = $code;
		$this->message = $message;
	}
	public function get_error_code() {
		return $this->code;
	}
}
function is_wp_error( $v ) {
	return $v instanceof WP_Error;
}
class WP_User {}

require __DIR__ . '/../renewradar-core/login-challenge.php';

$failures = 0;
$count    = 0;
function check( $label, $condition ) {
	global $failures, $count;
	$count++;
	if ( ! $condition ) {
		$failures++;
		echo "FAIL: $label\n";
	}
}

function answer_for( $token ) {
	$q = rr_human_question( rr_human_parse_token( $token ) );
	return (string) $q['answer'];
}

$now   = 1800000000;
$token = rr_human_make_token( $now - 10 );

/* Token */
check( 'a genuine token parses', rr_human_parse_token( $token ) === $now - 10 );
check( 'a tampered time stamp is refused', null === rr_human_parse_token( ( $now - 5 ) . substr( $token, strpos( $token, '.' ) ) ) );
check( 'a tampered signature is refused', null === rr_human_parse_token( substr( $token, 0, -1 ) . ( substr( $token, -1 ) === 'a' ? 'b' : 'a' ) ) );
check( 'garbage is refused', null === rr_human_parse_token( 'hello' ) && null === rr_human_parse_token( '' ) && null === rr_human_parse_token( null ) );

/* Question */
$seen = array();
for ( $i = 0; $i < 300; $i++ ) {
	$q = rr_human_question( $now + $i );
	check( 'answer is never negative', $q['answer'] >= 0 && $q['answer'] <= 18 );
	check( 'text matches the answer', 1 === preg_match( '/^(\d) ([+-]) (\d)$/', $q['text'], $m ) && ( '+' === $m[2] ? (int) $m[1] + (int) $m[3] : (int) $m[1] - (int) $m[3] ) === $q['answer'] );
	$seen[ $q['text'] ] = true;
}
check( 'questions vary', count( $seen ) > 20 );
check( 'the same token gives the same question', rr_human_question( $now ) === rr_human_question( $now ) );

/* Verify */
check( 'correct answer passes', true === rr_human_verify( $token, answer_for( $token ), '', $now ) );
check( 'answer with spaces passes', true === rr_human_verify( $token, ' ' . answer_for( $token ) . ' ', '', $now ) );
check( 'wrong answer fails', 'answer' === rr_human_verify( $token, (string) ( (int) answer_for( $token ) + 1 ), '', $now ) );
check( 'empty answer fails', 'answer' === rr_human_verify( $token, '', '', $now ) );
check( 'text answer fails', 'answer' === rr_human_verify( $token, 'eleven', '', $now ) );
check( 'filled honeypot fails', 'honeypot' === rr_human_verify( $token, answer_for( $token ), 'http://spam', $now ) );
check( 'missing token fails', 'token' === rr_human_verify( '', '5', '', $now ) );
check( 'too fast fails', 'fast' === rr_human_verify( rr_human_make_token( $now - 1 ), '5', '', $now ) );
check( 'exactly the minimum passes the time check', true === rr_human_verify( rr_human_make_token( $now - 2 ), answer_for( rr_human_make_token( $now - 2 ) ), '', $now ) );
check( 'expired fails', 'expired' === rr_human_verify( rr_human_make_token( $now - 1801 ), '5', '', $now ) );
check( 'a token from the future fails', 'fast' === rr_human_verify( rr_human_make_token( $now + 100 ), '5', '', $now ) );

/* The sign-in form hook */
$auth = null;
foreach ( $GLOBALS['rr_hooks'] as $h ) {
	if ( 'authenticate' === $h[0] ) {
		$auth = $h;
	}
}
check( 'authenticate filter is registered after core (priority 99)', null !== $auth && 99 === $auth[2] );

function run_form( $fn, $user, $post, $pagenow = 'wp-login.php', $method = 'POST', $request = array() ) {
	$GLOBALS['pagenow']       = $pagenow;
	$_SERVER['REQUEST_METHOD'] = $method;
	$_POST                    = $post;
	$_REQUEST                 = $request;
	return $fn( $user );
}

$fresh = rr_human_make_token( time() - 5 );
$good  = array( 'log' => 'admin', 'pwd' => 'x', 'rr_human_token' => $fresh, 'rr_human_answer' => answer_for( $fresh ), 'rr_hp_x7' => '' );
$user  = new WP_User();

check( 'correct form returns the user', run_form( $auth[1], $user, $good ) === $user );
$bad = $good;
$bad['rr_human_answer'] = '99';
$r = run_form( $auth[1], $user, $bad );
check( 'wrong answer with a correct password is refused', $r instanceof WP_Error && 'rr_human' === $r->get_error_code() );
$wrong_pw = run_form( $auth[1], new WP_Error( 'incorrect_password', 'nope' ), $bad );
check( 'wrong answer with a wrong password gives the same error (no password oracle)', $wrong_pw instanceof WP_Error && 'rr_human' === $wrong_pw->get_error_code() && $wrong_pw->message === $r->message );
$nofield = run_form( $auth[1], $user, array( 'log' => 'admin', 'pwd' => 'x' ) );
check( 'a post without the check is refused', $nofield instanceof WP_Error && 'rr_human' === $nofield->get_error_code() );
$locked = new WP_Error( 'rr_locked', 'locked' );
check( 'a lockout error is kept', run_form( $auth[1], $locked, $bad ) === $locked );
check( 'a wrong password with a correct check keeps the core error', ( $e = new WP_Error( 'incorrect_password', 'nope' ) ) && run_form( $auth[1], $e, $good ) === $e );
check( 'REST or application password requests are not affected', run_form( $auth[1], $user, array(), 'index.php' ) === $user );
check( 'a GET to the login page is not affected', run_form( $auth[1], $user, array(), 'wp-login.php', 'GET' ) === $user );
check( 'other login actions are not affected', run_form( $auth[1], $user, $good + array(), 'wp-login.php', 'POST', array( 'action' => 'lostpassword' ) ) === $user );

/* The form markup */
$form = null;
foreach ( $GLOBALS['rr_hooks'] as $h ) {
	if ( 'login_form' === $h[0] ) {
		$form = $h[1];
	}
}
ob_start();
$form();
$html = ob_get_clean();
check( 'form shows the question', 1 === preg_match( '/what is \d [+-] \d\?/', $html ) );
check( 'form has the answer, token and honeypot fields', false !== strpos( $html, 'name="rr_human_answer"' ) && false !== strpos( $html, 'name="rr_human_token"' ) && false !== strpos( $html, 'name="rr_hp_x7"' ) );
check( 'honeypot is hidden from keyboard and screen readers', false !== strpos( $html, 'tabindex="-1"' ) && false !== strpos( $html, 'aria-hidden="true"' ) );
preg_match( '/name="rr_human_token" value="([^"]+)"/', $html, $tm );
preg_match( '/what is (\d) ([+-]) (\d)\?/', $html, $qm );
$shown_answer = '+' === $qm[2] ? (int) $qm[1] + (int) $qm[3] : (int) $qm[1] - (int) $qm[3];
check( 'the question on the page matches the token', (string) $shown_answer === answer_for( $tm[1] ) );

echo $failures ? "\n$failures of $count checks FAILED\n" : "All $count checks passed\n";
exit( $failures ? 1 : 0 );
