<?php
/**
 * Pages: features, comparisons and the legal pages.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$rr_id    = get_the_ID();
	$rr_legal = (bool) get_post_meta( $rr_id, 'rr_legal', true );

	if ( $rr_legal ) {
		echo '<div class="legal">';
	}
	rr_page_hero(
		array(
			'tag'   => get_post_meta( $rr_id, 'rr_tag', true ),
			'title' => get_the_title(),
			'lede'  => get_post_meta( $rr_id, 'rr_lede', true ),
			'meta'  => $rr_legal ? 'RenewRadar by Pixel DevCode &middot; Last updated ' . esc_html( get_the_modified_date( 'F j, Y' ) ) : '',
			'cta'   => ! $rr_legal,
		)
	);
	rr_article( $rr_id );
	if ( $rr_legal ) {
		echo '</div>';
	} else {
		echo rr_cta_band(); // phpcs:ignore WordPress.Security.EscapeOutput
	}
endwhile;

get_footer();
