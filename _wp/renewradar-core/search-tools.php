<?php
/**
 * Search tools (wp-admin, administrators only): resend pages to IndexNow so Bing, Yandex, Naver, Seznam and
 * Yep recrawl them, and list the total and most used keywords found in the site's own content. Search
 * queries that visitors typed into Google or Bing are not on the site; they are in the search consoles,
 * which the page links to.
 */

defined( 'ABSPATH' ) || exit;

const RR_KEYWORDS_CACHE = 'rr_keywords_cache';

/* ------------------------------------------------------------------ */
/* Keywords (pure functions)                                           */
/* ------------------------------------------------------------------ */

function rr_keyword_stopwords() {
	return array_flip( explode( ' ', 'a about above after again all also am an and any are aren as at be because been before being below between both but by can cannot could did do does doing down during each few for from further had has have having he her here hers him his how i if in into is it its itself just me more most my no nor not now of off on once only or other our ours out over own same she should so some such than that the their theirs them then there these they this those through to too under until up us very was we were what when where which while who whom why will with would you your yours yourself also get got one two like make use used using via per vs etc may might many much new need needs want way ways' ) );
}

/**
 * Counts words and two-word phrases. A title counts three times as much as body text, because it says what
 * a page is about. Phrases never run across punctuation and never start or end with a common word.
 *
 * @param array<int,array{title:string,body:string}> $pages
 * @return array{words:array<string,array{count:int,pages:int}>,phrases:array<string,array{count:int,pages:int}>,total_words:int}
 */
function rr_keywords_extract( array $pages ) {
	$stop    = rr_keyword_stopwords();
	$words   = array();
	$phrases = array();
	$total   = 0;
	foreach ( $pages as $page ) {
		$seen_w = array();
		$seen_p = array();
		foreach ( array( array( $page['title'], 3 ), array( $page['body'], 1 ) ) as $part ) {
			list( $text, $weight ) = $part;
			$text = strtolower( html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES, 'UTF-8' ) );
			foreach ( preg_split( '/[.!?;:,()\[\]{}"\x{201C}\x{201D}\n\r\t|\/\\\\]+/u', $text ) as $chunk ) {
				preg_match_all( "/[a-z0-9][a-z0-9'\-]*[a-z0-9]|[a-z0-9]/", $chunk, $m );
				$tokens = array_map( function ( $t ) {
					return str_replace( "'s", '', $t );
				}, $m[0] );
				$total += count( $tokens ) * ( 1 === $weight ? 1 : 0 );
				$prev = null;
				foreach ( $tokens as $t ) {
					$ok = strlen( $t ) >= 3 && ! isset( $stop[ $t ] ) && ! ctype_digit( $t );
					if ( $ok ) {
						$words[ $t ]['count'] = ( isset( $words[ $t ]['count'] ) ? $words[ $t ]['count'] : 0 ) + $weight;
						$seen_w[ $t ]         = true;
						if ( null !== $prev ) {
							$p                        = $prev . ' ' . $t;
							$phrases[ $p ]['count']   = ( isset( $phrases[ $p ]['count'] ) ? $phrases[ $p ]['count'] : 0 ) + $weight;
							$seen_p[ $p ]             = true;
						}
					}
					$prev = $ok ? $t : null;
				}
			}
		}
		foreach ( array_keys( $seen_w ) as $t ) {
			$words[ $t ]['pages'] = ( isset( $words[ $t ]['pages'] ) ? $words[ $t ]['pages'] : 0 ) + 1;
		}
		foreach ( array_keys( $seen_p ) as $p ) {
			$phrases[ $p ]['pages'] = ( isset( $phrases[ $p ]['pages'] ) ? $phrases[ $p ]['pages'] : 0 ) + 1;
		}
	}
	return array( 'words' => $words, 'phrases' => $phrases, 'total_words' => $total );
}

/** The most used entries, highest count first, then in more pages first, then alphabetical. A phrase must appear twice. */
function rr_keywords_top( array $counts, $limit = 25, $min = 1 ) {
	$rows = array();
	foreach ( $counts as $term => $c ) {
		if ( $c['count'] >= $min ) {
			$rows[] = array( 'term' => (string) $term, 'count' => (int) $c['count'], 'pages' => (int) $c['pages'] );
		}
	}
	usort( $rows, function ( $a, $b ) {
		return array( $b['count'], $b['pages'], $a['term'] ) <=> array( $a['count'], $a['pages'], $b['term'] );
	} );
	return array_slice( $rows, 0, $limit );
}

/** Public posts and pages with their text, for the keyword count. */
function rr_site_pages() {
	$posts = get_posts( array(
		'post_type'      => array( 'post', 'page' ),
		'post_status'    => 'publish',
		'has_password'   => false,
		'posts_per_page' => 500,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	$pages = array();
	foreach ( $posts as $p ) {
		$pages[] = array(
			'id'    => $p->ID,
			'url'   => get_permalink( $p ),
			'title' => get_the_title( $p ),
			'body'  => strip_shortcodes( $p->post_content ),
		);
	}
	return $pages;
}

function rr_keywords_report( $refresh = false ) {
	$cached = get_transient( RR_KEYWORDS_CACHE );
	if ( ! $refresh && is_array( $cached ) ) {
		return $cached;
	}
	$pages  = rr_site_pages();
	$counts = rr_keywords_extract( $pages );
	$report = array(
		'built'          => time(),
		'pages'          => count( $pages ),
		'total_words'    => $counts['total_words'],
		'distinct_words' => count( $counts['words'] ),
		'words'          => rr_keywords_top( $counts['words'], 30 ),
		'phrases'        => rr_keywords_top( $counts['phrases'], 30, 2 ),
	);
	set_transient( RR_KEYWORDS_CACHE, $report, 6 * HOUR_IN_SECONDS );
	return $report;
}

add_action( 'save_post', function () {
	delete_transient( RR_KEYWORDS_CACHE );
} );

/* ------------------------------------------------------------------ */
/* Admin page                                                          */
/* ------------------------------------------------------------------ */

add_action( 'admin_menu', function () {
	add_menu_page( 'Search tools', 'Search tools', 'manage_options', 'rr-search', 'rr_search_page', 'dashicons-search', 4 );
} );

/** Every address a search engine should know about: the home page, the guides index and each public page and guide. */
function rr_reindex_candidates() {
	$rows   = array();
	$rows[] = array( 'url' => home_url( '/' ), 'title' => 'Home page' );
	$rows[] = array( 'url' => home_url( '/guides/' ), 'title' => 'Guides index' );
	foreach ( rr_site_pages() as $p ) {
		$rows[] = array( 'url' => $p['url'], 'title' => $p['title'] );
	}
	$seen = array();
	return array_values( array_filter( $rows, function ( $r ) use ( &$seen ) {
		if ( isset( $seen[ $r['url'] ] ) ) {
			return false;
		}
		return $seen[ $r['url'] ] = true;
	} ) );
}

add_action( 'admin_post_rr_reindex', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have permission to do this.', 403 );
	}
	check_admin_referer( 'rr_reindex' );
	$known = wp_list_pluck( rr_reindex_candidates(), 'url' );
	if ( ! empty( $_POST['all'] ) ) {
		$urls = $known;
	} else {
		$urls = isset( $_POST['urls'] ) && is_array( $_POST['urls'] ) ? array_map( 'esc_url_raw', wp_unslash( $_POST['urls'] ) ) : array();
		$urls = array_values( array_intersect( $urls, $known ) );
	}
	$result         = rr_indexnow_submit( $urls );
	$result['time'] = time();
	update_option( 'rr_reindex_last', $result, false );
	wp_safe_redirect( admin_url( 'admin.php?page=rr-search' ) );
	exit;
} );

add_action( 'admin_post_rr_keywords_refresh', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have permission to do this.', 403 );
	}
	check_admin_referer( 'rr_keywords_refresh' );
	rr_keywords_report( true );
	wp_safe_redirect( admin_url( 'admin.php?page=rr-search' ) );
	exit;
} );

function rr_search_table( $title, $rows, $first ) {
	echo '<table class="widefat striped" style="max-width:520px"><thead><tr><th>' . esc_html( $first ) . '</th><th style="width:70px">Uses</th><th style="width:70px">Pages</th></tr></thead><tbody>';
	if ( ! $rows ) {
		echo '<tr><td colspan="3">Nothing to show yet.</td></tr>';
	}
	foreach ( $rows as $r ) {
		echo '<tr><td>' . esc_html( $r['term'] ) . '</td><td>' . (int) $r['count'] . '</td><td>' . (int) $r['pages'] . '</td></tr>';
	}
	echo '</tbody></table>';
}

function rr_search_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have permission to view this page.', 403 );
	}
	$last = get_option( 'rr_reindex_last' );
	$rows = rr_reindex_candidates();
	$rep  = rr_keywords_report();
	$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	?>
	<div class="wrap">
		<h1>Search tools</h1>

		<?php if ( is_array( $last ) ) : ?>
			<div class="notice <?php echo $last['error'] ? 'notice-error' : 'notice-success'; ?>"><p>
				<?php
				echo esc_html( wp_date( 'j M Y, H:i', $last['time'] ) ) . ': ';
				echo $last['error'] ? esc_html( $last['error'] ) : esc_html( 'sent ' . $last['count'] . ' address' . ( 1 === (int) $last['count'] ? '' : 'es' ) . ' to IndexNow (HTTP ' . $last['code'] . ').' );
				?>
			</p></div>
		<?php endif; ?>

		<h2>Reindex pages</h2>
		<p>Sends addresses to IndexNow, which tells Bing, Yandex, Naver, Seznam and Yep (and so Yahoo, DuckDuckGo and Ecosia through Bing) to recrawl them. Pages are also sent automatically when you publish or edit them. Google does not use IndexNow: ask it to recrawl a page with URL inspection in Search Console.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="rr_reindex">
			<?php wp_nonce_field( 'rr_reindex' ); ?>
			<table class="widefat striped" style="max-width:860px"><thead><tr><td class="check-column"></td><th>Page</th><th>Address</th></tr></thead><tbody>
			<?php foreach ( $rows as $r ) : ?>
				<tr>
					<th class="check-column"><input type="checkbox" name="urls[]" value="<?php echo esc_attr( $r['url'] ); ?>"></th>
					<td><?php echo esc_html( $r['title'] ); ?></td>
					<td><a href="<?php echo esc_url( $r['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( preg_replace( '#^https?://' . preg_quote( $host, '#' ) . '#', '', $r['url'] ) ); ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody></table>
			<p>
				<button type="submit" class="button button-primary">Reindex selected</button>
				<button type="submit" name="all" value="1" class="button">Reindex all <?php echo (int) count( $rows ); ?> pages</button>
			</p>
		</form>

		<h2>Keywords on this site</h2>
		<p>
			<?php
			echo esc_html( sprintf(
				'%s words across %d pages and guides, %s different keywords. A word in a title counts three times. Counted %s.',
				number_format_i18n( $rep['total_words'] ),
				$rep['pages'],
				number_format_i18n( $rep['distinct_words'] ),
				wp_date( 'j M Y, H:i', $rep['built'] )
			) );
			?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=rr_keywords_refresh' ), 'rr_keywords_refresh' ) ); ?>">Recount now</a>
		</p>
		<div style="display:flex;gap:32px;flex-wrap:wrap">
			<div><h3>Popular words</h3><?php rr_search_table( 'Popular words', $rep['words'], 'Keyword' ); ?></div>
			<div><h3>Popular phrases</h3><?php rr_search_table( 'Popular phrases', $rep['phrases'], 'Phrase' ); ?></div>
		</div>

		<h2>What people searched for</h2>
		<p>These are your own words. The searches visitors typed to find you, with clicks and positions, are only in the search consoles:</p>
		<p>
			<a class="button" href="https://search.google.com/search-console/performance/search-analytics?resource_id=<?php echo rawurlencode( home_url( '/' ) ); ?>" target="_blank" rel="noopener">Google Search Console</a>
			<a class="button" href="https://www.bing.com/webmasters/" target="_blank" rel="noopener">Bing Webmaster Tools</a>
			<a class="button" href="https://webmaster.yandex.com/sites/" target="_blank" rel="noopener">Yandex Webmaster</a>
		</p>
	</div>
	<?php
}
