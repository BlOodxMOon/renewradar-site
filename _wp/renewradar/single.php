<?php
/**
 * A single guide.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$rr_id = get_the_ID();
	rr_page_hero(
		array(
			'tag'   => get_post_meta( $rr_id, 'rr_tag', true ) ? get_post_meta( $rr_id, 'rr_tag', true ) : 'Guide',
			'title' => get_the_title(),
			'lede'  => get_post_meta( $rr_id, 'rr_lede', true ) ? get_post_meta( $rr_id, 'rr_lede', true ) : get_the_excerpt(),
			'meta'  => 'Updated ' . esc_html( get_the_modified_date( 'F j, Y' ) ) . ' &middot; ' . rr_reading_minutes( $rr_id ) . ' minute read',
			'cta'   => false,
		)
	);
	rr_article( $rr_id );
	echo rr_cta_band(); // phpcs:ignore WordPress.Security.EscapeOutput
endwhile;

get_footer();
