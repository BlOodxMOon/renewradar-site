<?php
/**
 * Tests for the keyword count. Run: php _wp/tests/search-tools-test.php
 * WordPress is replaced by small stubs, so this checks the counting logic, not the admin page.
 */

define( 'ABSPATH', __DIR__ . '/' );
function wp_strip_all_tags( $s ) {
	return trim( preg_replace( '/<[^>]*>/', ' ', (string) $s ) );
}

function add_action() {}
function add_filter() {}

require __DIR__ . '/../renewradar-core/search-tools.php';

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

$pages = array(
	array( 'title' => 'Free trial reminders', 'body' => '<p>A <b>free trial</b> ends quickly. Set a reminder before the free trial ends, and cancel in time.</p>' ),
	array( 'title' => 'Cancel subscriptions', 'body' => 'You can cancel a subscription before it renews. The reminder helps.' ),
	array( 'title' => 'About', 'body' => 'Nothing about 123 or the and of.' ),
);
$r = rr_keywords_extract( $pages );

check( 'a word in a title counts three times', $r['words']['reminders']['count'] === 3 );
check( 'title and body both count: free = 3 (title) + 2 (body)', $r['words']['free']['count'] === 5 );
check( 'pages counts a page once however often the word appears', $r['words']['free']['pages'] === 1 );
check( 'trial appears in one page, 3 + 2 uses', $r['words']['trial']['count'] === 5 && $r['words']['trial']['pages'] === 1 );
check( 'cancel appears in two pages: 3 (title) + 1 + 1 (body)', $r['words']['cancel']['pages'] === 2 && $r['words']['cancel']['count'] === 5 );
check( 'reminder appears in two pages', $r['words']['reminder']['pages'] === 2 );
check( 'stop words are skipped', ! isset( $r['words']['the'] ) && ! isset( $r['words']['and'] ) && ! isset( $r['words']['you'] ) );
check( 'numbers are skipped', ! isset( $r['words']['123'] ) );
check( 'html tags are not counted', ! isset( $r['words']['<b>'] ) && ! isset( $r['words']['b'] ) );
check( 'short words are skipped', ! isset( $r['words']['a'] ) );

check( 'a phrase counts: free trial', isset( $r['phrases']['free trial'] ) && $r['phrases']['free trial']['count'] === 3 + 1 + 1 );
check( 'a phrase never crosses punctuation', isset( $r['phrases']['ends quickly'] ) && ! isset( $r['phrases']['quickly set'] ) && ! isset( $r['phrases']['time you'] ) );
check( 'a phrase never contains a common word', ! isset( $r['phrases']['reminder before'] ) && ! isset( $r['phrases']['before free'] ) );
check( 'total words counts body words only', $r['total_words'] > 20 && $r['total_words'] < 40 );

$top = rr_keywords_top( $r['words'], 3 );
check( 'top is limited', count( $top ) === 3 );
check( 'top is sorted by uses', $top[0]['count'] >= $top[1]['count'] && $top[1]['count'] >= $top[2]['count'] );
check( 'ties break by pages then alphabetically', rr_keywords_top( array( 'beta' => array( 'count' => 2, 'pages' => 1 ), 'alpha' => array( 'count' => 2, 'pages' => 1 ), 'gamma' => array( 'count' => 2, 'pages' => 2 ) ), 3 ) === array(
	array( 'term' => 'gamma', 'count' => 2, 'pages' => 2 ),
	array( 'term' => 'alpha', 'count' => 2, 'pages' => 1 ),
	array( 'term' => 'beta', 'count' => 2, 'pages' => 1 ),
) );
check( 'minimum uses filters phrases seen once', array() === rr_keywords_top( array( 'one off' => array( 'count' => 1, 'pages' => 1 ) ), 5, 2 ) );
check( 'empty input does not fail', rr_keywords_extract( array() ) === array( 'words' => array(), 'phrases' => array(), 'total_words' => 0 ) );
check( 'numeric-looking terms stay strings in the top list', is_string( rr_keywords_top( array( '2fa' => array( 'count' => 1, 'pages' => 1 ) ) )[0]['term'] ) );

echo $failures ? "\n$failures of $count checks FAILED\n" : "All $count checks passed\n";
exit( $failures ? 1 : 0 );
