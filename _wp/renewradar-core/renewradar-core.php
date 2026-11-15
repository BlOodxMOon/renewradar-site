<?php
/**
 * Plugin Name: RenewRadar Core
 * Description: Service suggestions for the RenewRadar app (served at /presets.json), the /privacy.html and /terms.html addresses, the first-run content import and the scheduled articles.
 * Version: 1.1.0
 * Author: Pixel DevCode
 * Author URI: https://pixeldevcode.com
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Text Domain: renewradar-core
 */

defined( 'ABSPATH' ) || exit;

const RR_CORE_FILE = __FILE__;
const RR_VERSION_OPTION = 'rr_presets_version';
const RR_UPDATED_OPTION = 'rr_presets_updated';

/** Categories and billing cycles the app understands. Keep in sync with src/lib/cycles.ts in the app. */
function rr_categories() {
	return array( 'streaming', 'music', 'storage', 'software', 'gaming', 'news', 'fitness', 'utilities', 'other' );
}

function rr_cycles() {
	return array( 'weekly', 'monthly', 'quarterly', 'yearly' );
}

/* ------------------------------------------------------------------ */
/* Services (app presets)                                              */
/* ------------------------------------------------------------------ */

add_action( 'init', 'rr_register_service_type' );

function rr_register_service_type() {
	register_post_type(
		'rr_service',
		array(
			'labels'        => array(
				'name'          => 'Services',
				'singular_name' => 'Service',
				'add_new'       => 'Add service',
				'add_new_item'  => 'Add service',
				'edit_item'     => 'Edit service',
				'search_items'  => 'Search services',
				'not_found'     => 'No services yet',
				'menu_name'     => 'App services',
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'menu_position' => 25,
			'menu_icon'     => 'dashicons-smartphone',
			'supports'      => array( 'title' ),
			'map_meta_cap'  => true,
		)
	);
}

add_filter( 'enter_title_here', function ( $text, $post ) {
	return 'rr_service' === $post->post_type ? 'Service name, for example Netflix' : $text;
}, 10, 2 );

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'rr_service_fields', 'Details used by the app', 'rr_service_box', 'rr_service', 'normal', 'high' );
} );

function rr_service_box( $post ) {
	wp_nonce_field( 'rr_service_save', 'rr_service_nonce' );
	$v = function ( $key ) use ( $post ) {
		return get_post_meta( $post->ID, $key, true );
	};
	$category = $v( 'rr_category' ) ? $v( 'rr_category' ) : 'other';
	$cycle    = $v( 'rr_cycle' ) ? $v( 'rr_cycle' ) : 'monthly';
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="rr_id">ID</label></th>
			<td><input type="text" id="rr_id" name="rr_id" class="regular-text code" value="<?php echo esc_attr( $v( 'rr_id' ) ); ?>" pattern="[a-z0-9-]*">
			<p class="description">Lowercase letters, numbers and dashes. Leave empty to create it from the name. Do not change it after the app has downloaded it.</p></td>
		</tr>
		<tr>
			<th><label for="rr_category">Category</label></th>
			<td><select id="rr_category" name="rr_category">
				<?php foreach ( rr_categories() as $c ) : ?>
					<option value="<?php echo esc_attr( $c ); ?>" <?php selected( $category, $c ); ?>><?php echo esc_html( ucfirst( $c ) ); ?></option>
				<?php endforeach; ?>
			</select></td>
		</tr>
		<tr>
			<th><label for="rr_cycle">Usual billing cycle</label></th>
			<td><select id="rr_cycle" name="rr_cycle">
				<?php foreach ( rr_cycles() as $c ) : ?>
					<option value="<?php echo esc_attr( $c ); ?>" <?php selected( $cycle, $c ); ?>><?php echo esc_html( ucfirst( $c ) ); ?></option>
				<?php endforeach; ?>
			</select></td>
		</tr>
		<tr>
			<th><label for="rr_aliases">Other names</label></th>
			<td><input type="text" id="rr_aliases" name="rr_aliases" class="regular-text" value="<?php echo esc_attr( $v( 'rr_aliases' ) ); ?>">
			<p class="description">Comma separated, used for search. For example: spotify premium</p></td>
		</tr>
		<tr>
			<th><label for="rr_trial_days">Free trial length</label></th>
			<td><input type="number" id="rr_trial_days" name="rr_trial_days" min="0" max="365" class="small-text" value="<?php echo esc_attr( $v( 'rr_trial_days' ) ); ?>"> days
			<p class="description">Leave empty if the service has no usual free trial.</p></td>
		</tr>
		<tr>
			<th><label for="rr_color">Color</label></th>
			<td><input type="color" id="rr_color" name="rr_color" value="<?php echo esc_attr( $v( 'rr_color' ) ? $v( 'rr_color' ) : '#4f46e5' ); ?>">
			<p class="description">Background of the circle with the service initial. No logos.</p></td>
		</tr>
		<tr>
			<th><label for="rr_manage_url">Manage or cancel link</label></th>
			<td><input type="url" id="rr_manage_url" name="rr_manage_url" class="regular-text" value="<?php echo esc_attr( $v( 'rr_manage_url' ) ); ?>">
			<p class="description">Optional. The page where people manage or cancel this subscription.</p></td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post_rr_service', 'rr_save_service', 10, 2 );

function rr_save_service( $post_id, $post ) {
	if ( ! isset( $_POST['rr_service_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rr_service_nonce'] ) ), 'rr_service_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$id = isset( $_POST['rr_id'] ) ? sanitize_title( wp_unslash( $_POST['rr_id'] ) ) : '';
	if ( '' === $id ) {
		$id = sanitize_title( $post->post_title );
	}
	update_post_meta( $post_id, 'rr_id', $id );

	$category = isset( $_POST['rr_category'] ) ? sanitize_key( wp_unslash( $_POST['rr_category'] ) ) : 'other';
	update_post_meta( $post_id, 'rr_category', in_array( $category, rr_categories(), true ) ? $category : 'other' );

	$cycle = isset( $_POST['rr_cycle'] ) ? sanitize_key( wp_unslash( $_POST['rr_cycle'] ) ) : 'monthly';
	update_post_meta( $post_id, 'rr_cycle', in_array( $cycle, rr_cycles(), true ) ? $cycle : 'monthly' );

	$aliases = isset( $_POST['rr_aliases'] ) ? sanitize_text_field( wp_unslash( $_POST['rr_aliases'] ) ) : '';
	update_post_meta( $post_id, 'rr_aliases', $aliases );

	$trial = isset( $_POST['rr_trial_days'] ) ? absint( $_POST['rr_trial_days'] ) : 0;
	update_post_meta( $post_id, 'rr_trial_days', $trial > 0 ? $trial : '' );

	$color = isset( $_POST['rr_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['rr_color'] ) ) : '';
	update_post_meta( $post_id, 'rr_color', $color ? $color : '' );

	$url = isset( $_POST['rr_manage_url'] ) ? esc_url_raw( wp_unslash( $_POST['rr_manage_url'] ) ) : '';
	update_post_meta( $post_id, 'rr_manage_url', $url );

	rr_bump_version();
}

/** Any change to the list raises the version, otherwise the app ignores the new list. */
function rr_bump_version() {
	update_option( RR_VERSION_OPTION, (int) get_option( RR_VERSION_OPTION, 1 ) + 1 );
	update_option( RR_UPDATED_OPTION, gmdate( 'Y-m-d' ) );
}

foreach ( array( 'trashed_post', 'untrashed_post', 'deleted_post' ) as $rr_hook ) {
	add_action( $rr_hook, function ( $post_id ) {
		if ( 'rr_service' === get_post_type( $post_id ) ) {
			rr_bump_version();
		}
	} );
}

add_filter( 'manage_rr_service_posts_columns', function ( $cols ) {
	return array(
		'cb'          => $cols['cb'],
		'title'       => 'Service',
		'rr_category' => 'Category',
		'rr_cycle'    => 'Cycle',
		'rr_trial'    => 'Trial',
		'rr_color'    => 'Color',
	);
} );

add_action( 'manage_rr_service_posts_custom_column', function ( $col, $post_id ) {
	switch ( $col ) {
		case 'rr_category':
			echo esc_html( ucfirst( (string) get_post_meta( $post_id, 'rr_category', true ) ) );
			break;
		case 'rr_cycle':
			echo esc_html( ucfirst( (string) get_post_meta( $post_id, 'rr_cycle', true ) ) );
			break;
		case 'rr_trial':
			$days = get_post_meta( $post_id, 'rr_trial_days', true );
			echo $days ? esc_html( $days . ' days' ) : '&ndash;';
			break;
		case 'rr_color':
			$color = get_post_meta( $post_id, 'rr_color', true );
			if ( $color ) {
				printf( '<span style="display:inline-block;width:18px;height:18px;border-radius:50%%;background:%s;vertical-align:middle"></span>', esc_attr( $color ) );
			}
			break;
	}
}, 10, 2 );

add_action( 'admin_notices', function () {
	$screen = get_current_screen();
	if ( ! $screen || 'rr_service' !== $screen->post_type ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>The RenewRadar app downloads this list from <a href="%1$s" target="_blank" rel="noopener">%1$s</a> about once a week. Current list version: <strong>%2$d</strong> (updated %3$s). Every change raises the version so phones pick it up.</p></div>',
		esc_url( home_url( '/presets.json' ) ),
		(int) get_option( RR_VERSION_OPTION, 1 ),
		esc_html( (string) get_option( RR_UPDATED_OPTION, '' ) )
	);
} );

/** The catalog in the exact shape the app validates (src/types/preset.ts). */
function rr_catalog() {
	$posts = get_posts(
		array(
			'post_type'      => 'rr_service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	$items = array();
	foreach ( $posts as $p ) {
		$item = array(
			'id'           => (string) ( get_post_meta( $p->ID, 'rr_id', true ) ? get_post_meta( $p->ID, 'rr_id', true ) : sanitize_title( $p->post_title ) ),
			'name'         => html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ),
			'category'     => (string) ( get_post_meta( $p->ID, 'rr_category', true ) ? get_post_meta( $p->ID, 'rr_category', true ) : 'other' ),
			'defaultCycle' => (string) ( get_post_meta( $p->ID, 'rr_cycle', true ) ? get_post_meta( $p->ID, 'rr_cycle', true ) : 'monthly' ),
		);
		$aliases = array_values( array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $p->ID, 'rr_aliases', true ) ) ) ) );
		if ( $aliases ) {
			$item['aliases'] = $aliases;
		}
		$trial = (int) get_post_meta( $p->ID, 'rr_trial_days', true );
		if ( $trial > 0 ) {
			$item['trialDays'] = $trial;
		}
		$color = get_post_meta( $p->ID, 'rr_color', true );
		if ( $color ) {
			$item['color'] = strtoupper( $color );
		}
		$url = get_post_meta( $p->ID, 'rr_manage_url', true );
		if ( $url ) {
			$item['manageUrl'] = $url;
		}
		$items[] = $item;
	}
	return array(
		'version'   => (int) get_option( RR_VERSION_OPTION, 1 ),
		'updatedAt' => (string) get_option( RR_UPDATED_OPTION, gmdate( 'Y-m-d' ) ),
		'items'     => $items,
	);
}

/* ------------------------------------------------------------------ */
/* Addresses: /presets.json, /privacy.html, /terms.html, /sitemap.xml  */
/* ------------------------------------------------------------------ */

add_action( 'init', 'rr_rewrites' );

function rr_rewrites() {
	add_rewrite_rule( '^presets\.json$', 'index.php?rr_presets=1', 'top' );
	add_rewrite_rule( '^(privacy|terms)\.html$', 'index.php?pagename=$matches[1]&rr_legal=1', 'top' );
}

// Hosting tools (domain changes, migrations) can rebuild the rules without ours. Put them back when missing,
// because the app and Play Console depend on /presets.json and /privacy.html.
add_action( 'init', function () {
	$rules = get_option( 'rewrite_rules' );
	if ( is_array( $rules ) && ( ! isset( $rules['^presets\.json$'] ) || ! isset( $rules['^(privacy|terms)\.html$'] ) ) ) {
		flush_rewrite_rules( false );
	}
}, 99 );

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'rr_presets';
	$vars[] = 'rr_legal';
	return $vars;
} );

add_action( 'template_redirect', function () {
	if ( get_query_var( 'rr_presets' ) ) {
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Cache-Control: public, max-age=300' );
		echo wp_json_encode( rr_catalog(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		exit;
	}

	$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', PHP_URL_PATH );
	if ( '/sitemap.xml' === $path ) {
		wp_safe_redirect( home_url( '/wp-sitemap.xml' ), 301 );
		exit;
	}

	// Play Console links to /privacy.html, so the pages live there; /privacy/ redirects to it.
	if ( is_page( array( 'privacy', 'terms' ) ) && ! get_query_var( 'rr_legal' ) ) {
		wp_safe_redirect( home_url( '/' . get_post_field( 'post_name', get_queried_object_id() ) . '.html' ), 301 );
		exit;
	}
}, 1 );

add_filter( 'redirect_canonical', function ( $redirect ) {
	if ( get_query_var( 'rr_presets' ) || get_query_var( 'rr_legal' ) ) {
		return false;
	}
	return $redirect;
} );

add_filter( 'page_link', function ( $link, $post_id ) {
	$slug = get_post_field( 'post_name', $post_id );
	if ( in_array( $slug, array( 'privacy', 'terms' ), true ) && 0 === (int) get_post_field( 'post_parent', $post_id ) ) {
		return home_url( '/' . $slug . '.html' );
	}
	return $link;
}, 10, 2 );

// Keep users out of the public sitemap.
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );

// Do not expose WordPress usernames: the REST users list is for signed-in users only,
// and feeds and embeds name the publisher instead of the account.
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( 0 === strpos( $route, '/wp/v2/users' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
} );

add_filter( 'the_author', function ( $name ) {
	return is_feed() ? 'Pixel DevCode' : $name;
} );

add_filter( 'oembed_response_data', function ( $data ) {
	$data['author_name'] = 'Pixel DevCode';
	$data['author_url']  = home_url( '/' );
	return $data;
} );

/* ------------------------------------------------------------------ */
/* First-run setup                                                     */
/* ------------------------------------------------------------------ */

register_activation_hook( __FILE__, 'rr_activate' );

function rr_activate() {
	rr_register_service_type();
	rr_rewrites();
	if ( ! get_option( 'rr_seeded' ) ) {
		rr_seed();
		update_option( 'rr_seeded', gmdate( 'c' ) );
	}
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/* ------------------------------------------------------------------ */
/* One-time content updates                                            */
/* ------------------------------------------------------------------ */

add_action( 'init', 'rr_migrate', 20 );

function rr_migrate() {
	$done = (int) get_option( 'rr_migration', 0 );
	if ( $done >= 2 ) {
		return;
	}
	// 2: hosting moved from GitHub Pages to Hostinger, and the home page gained a click-to-play YouTube video.
	// Add the Cloudflare Web Analytics paragraph in a later step, together with the token.
	$privacy = get_page_by_path( 'privacy', OBJECT, 'page' );
	if ( $privacy ) {
		$content = $privacy->post_content;
		$content = str_replace(
			'the server that hosts the file (GitHub Pages) can see your IP address and standard request details.',
			'the server that hosts the file (Hostinger) can see your IP address and standard request details.',
			$content
		);
		$content = preg_replace(
			'#<h2><span class="n">9</span>This website</h2>.*?</section>#s',
			'<h2><span class="n">9</span>This website</h2><p>This website is hosted by Hostinger. It sets no cookies of its own and uses no advertising or tracking tools.</p><p>The home page can show a short promo video hosted on YouTube. The YouTube player is not loaded until you press play. When you do, the video plays from YouTube&#8217;s privacy-enhanced domain (youtube-nocookie.com), and Google receives your IP address and device details and may store information in your browser, as described in <a href="https://policies.google.com/privacy">Google&#8217;s privacy policy</a>.</p><p>If you pick a color mode, that choice is remembered in your browser&#8217;s local storage on your device. The subscription cost calculator runs entirely in your browser, and nothing you type into it is stored or sent anywhere. Search engines show us which search terms led people to this site as totals, without identifying anyone. Like any web host, Hostinger may log visitors&#8217; IP addresses for security and operations.</p></section>',
			$content,
			1
		);
		if ( $content !== $privacy->post_content ) {
			wp_update_post( array( 'ID' => $privacy->ID, 'post_content' => wp_slash( $content ) ) );
		}
	}
	update_option( 'rr_migration', 2 );
}

/* ------------------------------------------------------------------ */
/* Scheduled articles                                                  */
/* ------------------------------------------------------------------ */

// articles/articles.json is built from _wp/articles/*.md in the site repo. Each entry becomes a guide,
// scheduled for 08:00 on its date. Only scheduled posts are updated later; published, edited or deleted
// ones are left alone.

add_action( 'init', 'rr_articles_sync', 30 );

function rr_articles_sync() {
	$file = plugin_dir_path( __FILE__ ) . 'articles/articles.json';
	if ( ! file_exists( $file ) ) {
		return;
	}
	$stamp = filemtime( $file ) . '-' . filesize( $file );
	if ( get_option( 'rr_articles_stamp' ) === $stamp || get_transient( 'rr_articles_lock' ) ) {
		return;
	}
	set_transient( 'rr_articles_lock', 1, 5 * MINUTE_IN_SECONDS );

	$items = json_decode( (string) file_get_contents( $file ), true );
	$done  = (array) get_option( 'rr_articles_done', array() );
	$cat   = get_category_by_slug( 'guides' );
	$base  = plugins_url( 'articles', __FILE__ );
	// The content is trusted HTML from this plugin; keep KSES from stripping figures on visitor requests.
	kses_remove_filters();
	foreach ( (array) $items as $a ) {
		$existing = get_page_by_path( $a['slug'], OBJECT, 'post' );
		if ( $existing && 'future' !== $existing->post_status ) {
			continue;
		}
		if ( ! $existing && in_array( $a['slug'], $done, true ) ) {
			continue;
		}
		$local = $a['date'] . ' 08:00:00';
		$gmt   = get_gmt_from_date( $local );
		$args  = array(
			'post_type'     => 'post',
			'post_title'    => $a['title'],
			'post_name'     => $a['slug'],
			'post_content'  => str_replace( '{{articles}}', $base, $a['content'] ),
			'post_excerpt'  => $a['description'],
			'post_date'     => $local,
			'post_date_gmt' => $gmt,
			'edit_date'     => true,
			'post_status'   => strtotime( $gmt . ' UTC' ) > time() ? 'future' : 'publish',
			'post_category' => $cat ? array( $cat->term_id ) : array(),
		);
		if ( $existing ) {
			$args['ID'] = $existing->ID;
			$id         = wp_update_post( wp_slash( $args ) );
		} else {
			$id = wp_insert_post( wp_slash( $args ) );
		}
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, 'rr_tag', $a['tag'] );
		update_post_meta( $id, 'rr_lede', $a['lede'] );
		update_post_meta( $id, 'rr_seo_title', $a['seoTitle'] );
		update_post_meta( $id, 'rr_cover', 'img/' . $a['slug'] . '-cover.jpg' );
		update_post_meta( $id, 'rr_related', wp_slash( $a['related'] ) );
		$done[] = $a['slug'];
	}
	kses_init();
	update_option( 'rr_articles_done', array_values( array_unique( $done ) ), false );
	update_option( 'rr_articles_stamp', $stamp, false );
	delete_transient( 'rr_articles_lock' );
}

/** Cover image URL of an article (1200x630), or an empty string. */
function rr_article_cover( $post_id ) {
	$path = get_post_meta( $post_id, 'rr_cover', true );
	return $path ? plugins_url( 'articles/' . $path, __FILE__ ) : '';
}

// WP-Cron only runs on visits and can miss a scheduled time; publish anything overdue.
add_action( 'init', function () {
	if ( get_transient( 'rr_future_check' ) ) {
		return;
	}
	set_transient( 'rr_future_check', 1, 10 * MINUTE_IN_SECONDS );
	$due = get_posts(
		array(
			'post_type'   => 'post',
			'post_status' => 'future',
			'numberposts' => 10,
			'fields'      => 'ids',
			'date_query'  => array( array( 'column' => 'post_date_gmt', 'before' => gmdate( 'Y-m-d H:i:s' ) ) ),
		)
	);
	foreach ( $due as $id ) {
		wp_publish_post( $id );
	}
}, 40 );

function rr_seed() {
	$assets = content_url( '/themes/renewradar/assets' );
	$data   = json_decode( (string) file_get_contents( plugin_dir_path( __FILE__ ) . 'content.json' ), true );

	update_option( 'blogname', 'RenewRadar' );
	update_option( 'blogdescription', 'Subscription and free trial tracker for Android' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	// Through $wp_rewrite so the flush in rr_activate() uses the new structure.
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/guides/%postname%/' );

	// Remove WordPress sample content.
	foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
		$sample = get_page_by_path( $slug, OBJECT, $type );
		if ( $sample ) {
			wp_trash_post( $sample->ID );
		}
	}

	$guides_cat = term_exists( 'guides', 'category' );
	if ( ! $guides_cat ) {
		$guides_cat = wp_insert_term( 'Guides', 'category', array( 'slug' => 'guides' ) );
	}
	$guides_cat_id = is_array( $guides_cat ) ? (int) $guides_cat['term_id'] : (int) $guides_cat;
	update_option( 'default_category', $guides_cat_id );

	$home   = rr_ensure_page( 'home', 'Home', '' );
	$guides = rr_ensure_page( 'guides', 'Guides', '' );
	update_post_meta( $guides, 'rr_tag', 'Guides' );
	update_post_meta( $guides, 'rr_lede', 'Short, practical guides for keeping subscriptions under control, written by the team behind RenewRadar.' );
	update_post_meta( $guides, 'rr_seo_title', 'Subscription Guides: Cancel Trials, Audit Your Spending | RenewRadar' );
	wp_update_post( array( 'ID' => $guides, 'post_excerpt' => 'Practical guides to cancelling free trials in time, cancelling Google Play subscriptions and auditing what you pay for every month.' ) );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );
	update_option( 'page_for_posts', $guides );

	foreach ( (array) $data as $item ) {
		$content = str_replace( '{{assets}}', $assets, $item['content'] );
		$args    = array(
			'post_type'    => $item['type'],
			'post_status'  => 'publish',
			'post_title'   => $item['title'],
			'post_name'    => $item['slug'],
			'post_content' => $content,
			'post_excerpt' => $item['description'],
		);
		$existing = get_page_by_path( $item['slug'], OBJECT, $item['type'] );
		if ( $existing ) {
			continue;
		}
		if ( 'post' === $item['type'] ) {
			$args['post_category'] = array( $guides_cat_id );
		}
		$id = wp_insert_post( wp_slash( $args ) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, 'rr_tag', $item['tag'] );
		update_post_meta( $id, 'rr_lede', $item['lede'] );
		update_post_meta( $id, 'rr_seo_title', $item['seoTitle'] );
		if ( ! empty( $item['legal'] ) ) {
			update_post_meta( $id, 'rr_legal', 1 );
		}
		if ( ! empty( $item['related'] ) ) {
			update_post_meta( $id, 'rr_related', wp_slash( $item['related'] ) );
		}
		if ( ! empty( $item['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', $item['template'] );
		}
	}

	// Import the current app catalog, one version above it so phones accept the WordPress list.
	$seed = json_decode( (string) file_get_contents( plugin_dir_path( __FILE__ ) . 'presets-seed.json' ), true );
	if ( $seed && ! get_posts( array( 'post_type' => 'rr_service', 'posts_per_page' => 1, 'post_status' => 'any' ) ) ) {
		foreach ( $seed['items'] as $p ) {
			$id = wp_insert_post( array( 'post_type' => 'rr_service', 'post_status' => 'publish', 'post_title' => $p['name'] ) );
			if ( ! $id || is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, 'rr_id', $p['id'] );
			update_post_meta( $id, 'rr_category', $p['category'] );
			update_post_meta( $id, 'rr_cycle', $p['defaultCycle'] );
			update_post_meta( $id, 'rr_aliases', isset( $p['aliases'] ) ? implode( ', ', $p['aliases'] ) : '' );
			update_post_meta( $id, 'rr_trial_days', isset( $p['trialDays'] ) ? (int) $p['trialDays'] : '' );
			update_post_meta( $id, 'rr_color', isset( $p['color'] ) ? strtolower( $p['color'] ) : '' );
			update_post_meta( $id, 'rr_manage_url', isset( $p['manageUrl'] ) ? $p['manageUrl'] : '' );
		}
		update_option( RR_VERSION_OPTION, (int) $seed['version'] + 1 );
		update_option( RR_UPDATED_OPTION, gmdate( 'Y-m-d' ) );
	}
}

function rr_ensure_page( $slug, $title, $content ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page ) {
		return (int) $page->ID;
	}
	return (int) wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		)
	);
}
