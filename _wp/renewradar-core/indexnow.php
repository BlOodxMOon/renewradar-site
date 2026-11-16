<?php
/**
 * IndexNow: tells Bing, Yandex, Naver, Seznam and Yep when a page or guide is published or updated,
 * so they do not wait for their next crawl. Only page addresses are sent, never visitor data.
 */

defined( 'ABSPATH' ) || exit;

/* The key is public by design; the key file proves the site owns it. */
const RR_INDEXNOW_KEY = 'a4a197a61f3e91408bb2f5ff31ca42cf';

/* Serves /<key>.txt at the site root, where IndexNow looks for it. */
add_action( 'init', function () {
	$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
	if ( '/' . RR_INDEXNOW_KEY . '.txt' === $path ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo RR_INDEXNOW_KEY; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}
}, 0 );

/** Queues a URL; the queue is sent once at the end of the request. */
function rr_indexnow_queue( $url = null ) {
	static $urls = array();
	if ( null === $url ) {
		return $urls;
	}
	if ( ! $urls ) {
		add_action( 'shutdown', 'rr_indexnow_send' );
	}
	$urls[ $url ] = true;
	return $urls;
}

/* A post or page that is published, or edited while published (scheduled guides go live through this too). */
add_action( 'transition_post_status', function ( $new_status, $old_status, $post ) {
	if ( 'publish' !== $new_status || ! in_array( $post->post_type, array( 'post', 'page' ), true ) ) {
		return;
	}
	if ( wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) || post_password_required( $post ) ) {
		return;
	}
	rr_indexnow_queue( get_permalink( $post ) );
	if ( 'post' === $post->post_type && 'publish' !== $old_status ) {
		rr_indexnow_queue( home_url( '/guides/' ) );
	}
}, 10, 3 );

function rr_indexnow_send() {
	$urls = array_keys( rr_indexnow_queue() );
	if ( ! $urls || wp_get_environment_type() !== 'production' ) {
		return;
	}
	wp_remote_post( 'https://api.indexnow.org/indexnow', array(
		'blocking' => false,
		'timeout'  => 5,
		'headers'  => array( 'Content-Type' => 'application/json; charset=utf-8' ),
		'body'     => wp_json_encode( array(
			'host'        => (string) wp_parse_url( home_url(), PHP_URL_HOST ),
			'key'         => RR_INDEXNOW_KEY,
			'keyLocation' => home_url( '/' . RR_INDEXNOW_KEY . '.txt' ),
			'urlList'     => array_slice( $urls, 0, 100 ),
		) ),
	) );
}
