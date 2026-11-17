<?php
/**
 * Checks the About and Contact page data. Run: php _wp/tests/extra-pages-test.php
 */

define( 'ABSPATH', __DIR__ . '/' );
require __DIR__ . '/../renewradar-core/extra-pages.php';

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

$pages = rr_extra_pages();
check( 'two pages', 2 === count( $pages ) );
$known = array( '/', '/privacy.html', '/terms.html', '/about/', '/contact/', '/guides/', '/subscription-tracker/' );
$keys  = array( 'type', 'slug', 'template', 'legal', 'title', 'seoTitle', 'description', 'tag', 'lede', 'content', 'related' );
foreach ( $pages as $p ) {
	$n = $p['slug'];
	foreach ( $keys as $k ) {
		check( "$n has $k", array_key_exists( $k, $p ) );
	}
	check( "$n is a page", 'page' === $p['type'] );
	check( "$n seo title fits a search result (<= 60)", strlen( $p['seoTitle'] ) <= 60 && strlen( $p['seoTitle'] ) >= 20 );
	check( "$n description is 70-160 characters", strlen( $p['description'] ) >= 70 && strlen( $p['description'] ) <= 160 );
	check( "$n has a lede", '' !== $p['lede'] );
	check( "$n has no emoji or dash lookalikes", ! preg_match( '/[\x{2013}\x{2014}\x{1F300}-\x{1FAFF}]/u', $p['content'] . $p['title'] . $p['lede'] ) );
	check( "$n has one h2 per section with an id", substr_count( $p['content'], '<h2 id="' ) === substr_count( $p['content'], '<h2' ) && substr_count( $p['content'], '<h2' ) >= 4 );
	check( "$n has balanced paragraphs", substr_count( $p['content'], '<p>' ) === substr_count( $p['content'], '</p>' ) );
	preg_match_all( '/href="(\/[^"]*)"/', $p['content'], $m );
	foreach ( $m[1] as $href ) {
		check( "$n internal link $href exists", in_array( $href, $known, true ) );
	}
	foreach ( $p['related'] as $r ) {
		check( "$n related link {$r['href']} exists", in_array( $r['href'], $known, true ) );
	}
}
check( 'slugs are unique', count( array_unique( array_column( $pages, 'slug' ) ) ) === 2 );
check( 'contact page has the support address', false !== strpos( $pages[1]['content'], 'mailto:renewradar@pixeldevcode.com' ) );
check( 'about page links to the contact page', false !== strpos( $pages[0]['content'], 'href="/contact/"' ) );

echo $failures ? "\n$failures of $count checks FAILED\n" : "All $count checks passed\n";
exit( $failures ? 1 : 0 );
