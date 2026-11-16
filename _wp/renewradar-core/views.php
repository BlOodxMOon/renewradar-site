<?php
/**
 * Site views: a cookieless page view counter that keeps daily totals in this site's database.
 * Each page view adds one to a row for (day, page, country, device, browser, OS, source). Nothing
 * identifies a visitor: no cookies, no stored IP addresses, no third-party service.
 * Report: wp-admin, Site views. Summary: Dashboard widget.
 */

defined( 'ABSPATH' ) || exit;

const RR_VIEWS_DB_VERSION = 1;

function rr_views_table() {
	global $wpdb;
	return $wpdb->prefix . 'rr_views';
}

add_action( 'init', function () {
	if ( (int) get_option( 'rr_views_db' ) >= RR_VIEWS_DB_VERSION ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta(
		'CREATE TABLE ' . rr_views_table() . " (
		day date NOT NULL,
		k char(32) NOT NULL,
		path varchar(191) NOT NULL DEFAULT '',
		country char(2) NOT NULL DEFAULT '',
		device varchar(10) NOT NULL DEFAULT '',
		browser varchar(24) NOT NULL DEFAULT '',
		os varchar(24) NOT NULL DEFAULT '',
		source varchar(100) NOT NULL DEFAULT '',
		views int unsigned NOT NULL DEFAULT 0,
		visits int unsigned NOT NULL DEFAULT 0,
		PRIMARY KEY  (day,k)
		) " . $wpdb->get_charset_collate() . ';'
	);
	update_option( 'rr_views_db', RR_VIEWS_DB_VERSION );
}, 5 );

/* ------------------------------------------------------------------ */
/* Counting                                                            */
/* ------------------------------------------------------------------ */

// Printed in the footer of every public page. Visitors who are signed in (you) are not counted.
add_action( 'wp_footer', function () {
	if ( is_user_logged_in() || is_preview() || is_customize_preview() || is_404() ) {
		return;
	}
	$url = wp_json_encode( esc_url_raw( rest_url( 'rr/v1/hit' ) ) );
	echo '<script>(function(){try{var U=' . $url . ',l=location,r=document.referrer,h="";try{h=r?new URL(r).host:""}catch(e){}'
		. 'var n=performance.getEntriesByType?performance.getEntriesByType("navigation")[0]:null,t=n?n.type:"navigate",q=new URLSearchParams(l.search),'
		. 'b=JSON.stringify({p:l.pathname,r:h!==l.host?h:"",s:q.get("utm_source")||q.get("ref")||"",v:t==="navigate"&&h!==l.host?1:0,'
		. 'tz:Intl.DateTimeFormat().resolvedOptions().timeZone||"",m:navigator.maxTouchPoints>1?1:0});'
		. 'if(navigator.sendBeacon)navigator.sendBeacon(U,new Blob([b],{type:"text/plain"}));'
		. 'else fetch(U,{method:"POST",body:b,keepalive:true,headers:{"Content-Type":"text/plain"}})}catch(e){}})();</script>' . "\n";
}, 100 );

add_action( 'rest_api_init', function () {
	register_rest_route(
		'rr/v1',
		'/hit',
		array(
			'methods'             => 'POST',
			'callback'            => 'rr_views_hit',
			'permission_callback' => '__return_true',
		)
	);
} );

function rr_views_hit( WP_REST_Request $request ) {
	$done = new WP_REST_Response( null, 204 );

	// Only beacons sent by pages of this site.
	$origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ), PHP_URL_HOST ) : '';
	if ( ! $origin || wp_parse_url( home_url(), PHP_URL_HOST ) !== $origin ) {
		return $done;
	}
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	if ( '' === $ua || preg_match( '/bot|crawl|spider|slurp|preview|headless|lighthouse|pagespeed|python|curl|wget|httpclient|okhttp|java\//i', $ua ) ) {
		return $done;
	}
	$data = json_decode( (string) $request->get_body(), true );
	if ( ! is_array( $data ) || empty( $data['p'] ) || ! is_string( $data['p'] ) ) {
		return $done;
	}
	$path = substr( $data['p'], 0, 191 );
	if ( ! preg_match( '#^/[A-Za-z0-9/._%-]*$#', $path ) ) {
		return $done;
	}

	// Limit what one address can add, so the totals cannot be flooded. The address is hashed and forgotten after an hour.
	$throttle = 'rr_vt_' . md5( rr_client_ip() . wp_salt( 'nonce' ) );
	$count    = (int) get_transient( $throttle );
	if ( $count >= 300 ) {
		return $done;
	}
	set_transient( $throttle, $count + 1, HOUR_IN_SECONDS );

	$touch  = ! empty( $data['m'] );
	$device = rr_views_device( $ua, $touch );
	$row    = array(
		'path'    => $path,
		'country' => rr_views_country( isset( $data['tz'] ) && is_string( $data['tz'] ) ? $data['tz'] : '' ),
		'device'  => $device,
		'browser' => rr_views_browser( $ua ),
		'os'      => rr_views_os( $ua, $touch ),
		'source'  => '',
	);
	$visit = ! empty( $data['v'] ) ? 1 : 0;
	if ( $visit ) {
		$campaign = isset( $data['s'] ) && is_string( $data['s'] ) ? strtolower( preg_replace( '/[^A-Za-z0-9._-]/', '', $data['s'] ) ) : '';
		$referrer = isset( $data['r'] ) && is_string( $data['r'] ) ? strtolower( preg_replace( '/[^A-Za-z0-9.:-]/', '', $data['r'] ) ) : '';
		$row['source'] = substr( $campaign ? $campaign : preg_replace( '/^(www|m|l|lm)\./', '', $referrer ), 0, 100 );
	}

	global $wpdb;
	$day = wp_date( 'Y-m-d' );
	$wpdb->query(
		$wpdb->prepare(
			'INSERT INTO ' . rr_views_table() . ' (day, k, path, country, device, browser, os, source, views, visits) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 1, %d) ON DUPLICATE KEY UPDATE views = views + 1, visits = visits + %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name.
			$day,
			md5( implode( "\n", $row ) ),
			$row['path'],
			$row['country'],
			$row['device'],
			$row['browser'],
			$row['os'],
			$row['source'],
			$visit,
			$visit
		)
	);
	return $done;
}

/** Country code from a CDN header when the host provides one, otherwise estimated from the browser's time zone. */
function rr_views_country( $tz ) {
	foreach ( array( 'HTTP_CF_IPCOUNTRY', 'HTTP_X_COUNTRY_CODE', 'HTTP_X_HCDN_COUNTRY', 'GEOIP_COUNTRY_CODE' ) as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$code = strtoupper( substr( sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ), 0, 2 ) );
			if ( preg_match( '/^[A-Z]{2}$/', $code ) && 'XX' !== $code ) {
				return $code;
			}
		}
	}
	$map = rr_views_geo();
	return isset( $map['zones'][ $tz ] ) ? $map['zones'][ $tz ] : '';
}

function rr_views_geo() {
	static $map = null;
	if ( null === $map ) {
		$map = require __DIR__ . '/views-tz.php';
	}
	return $map;
}

function rr_views_device( $ua, $touch ) {
	if ( preg_match( '/iPad|Tablet|PlayBook|Silk|Kindle/i', $ua ) || ( preg_match( '/Android/i', $ua ) && ! preg_match( '/Mobile/i', $ua ) ) ) {
		return 'Tablet';
	}
	if ( preg_match( '/Mobi|iPhone|iPod|Windows Phone/i', $ua ) ) {
		return 'Mobile';
	}
	// iPadOS reports itself as a Mac; a Mac with a touch screen is an iPad.
	if ( $touch && preg_match( '/Macintosh/i', $ua ) ) {
		return 'Tablet';
	}
	return 'Desktop';
}

function rr_views_browser( $ua ) {
	$list = array(
		'Edge'             => '/Edg(e|A|iOS)?\//',
		'Opera'            => '/OPR\/|Opera/',
		'Samsung Internet' => '/SamsungBrowser/',
		'Firefox'          => '/Firefox|FxiOS/',
		'Chrome'           => '/Chrome|CriOS/',
		'Safari'           => '/Safari/',
	);
	foreach ( $list as $name => $pattern ) {
		if ( preg_match( $pattern, $ua ) ) {
			return $name;
		}
	}
	return 'Other';
}

function rr_views_os( $ua, $touch ) {
	if ( preg_match( '/Android/i', $ua ) ) {
		return 'Android';
	}
	if ( preg_match( '/iPhone|iPad|iPod/i', $ua ) || ( $touch && preg_match( '/Macintosh/i', $ua ) ) ) {
		return 'iOS';
	}
	$list = array(
		'Windows'  => '/Windows/',
		'ChromeOS' => '/CrOS/',
		'macOS'    => '/Macintosh|Mac OS X/',
		'Linux'    => '/Linux/',
	);
	foreach ( $list as $name => $pattern ) {
		if ( preg_match( $pattern, $ua ) ) {
			return $name;
		}
	}
	return 'Other';
}

/* ------------------------------------------------------------------ */
/* Reports                                                             */
/* ------------------------------------------------------------------ */

/** Total views and visits from $from to $to (Y-m-d, inclusive). */
function rr_views_totals( $from, $to ) {
	global $wpdb;
	$row = $wpdb->get_row( $wpdb->prepare( 'SELECT COALESCE(SUM(views),0) AS views, COALESCE(SUM(visits),0) AS visits FROM ' . rr_views_table() . ' WHERE day BETWEEN %s AND %s', $from, $to ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name.
	return array(
		'views'  => (int) $row['views'],
		'visits' => (int) $row['visits'],
	);
}

/** Views and visits grouped by one column, largest first. */
function rr_views_by( $column, $from, $to, $limit = 10, $metric = 'views' ) {
	if ( ! in_array( $column, array( 'path', 'country', 'device', 'browser', 'os', 'source' ), true ) || ! in_array( $metric, array( 'views', 'visits' ), true ) ) {
		return array();
	}
	global $wpdb;
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT {$column} AS label, SUM(views) AS views, SUM(visits) AS visits FROM " . rr_views_table() . " WHERE day BETWEEN %s AND %s GROUP BY {$column} HAVING SUM({$metric}) > 0 ORDER BY SUM({$metric}) DESC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- whitelisted column.
			$from,
			$to,
			$limit
		),
		ARRAY_A
	);
}

add_action( 'admin_menu', function () {
	add_menu_page( 'Site views', 'Site views', 'manage_options', 'rr-views', 'rr_views_page', 'dashicons-chart-bar', 3 );
} );

add_action( 'wp_dashboard_setup', function () {
	if ( current_user_can( 'manage_options' ) ) {
		wp_add_dashboard_widget( 'rr_views_widget', 'Site views', 'rr_views_widget' );
	}
} );

function rr_views_widget() {
	$today = wp_date( 'Y-m-d' );
	$rows  = array(
		'Today'        => rr_views_totals( $today, $today ),
		'Last 7 days'  => rr_views_totals( wp_date( 'Y-m-d', strtotime( '-6 days' ) ), $today ),
		'Last 30 days' => rr_views_totals( wp_date( 'Y-m-d', strtotime( '-29 days' ) ), $today ),
	);
	echo '<table class="widefat striped"><thead><tr><th></th><th>Page views</th><th>Visits</th></tr></thead><tbody>';
	foreach ( $rows as $label => $t ) {
		printf( '<tr><td>%s</td><td><strong>%s</strong></td><td>%s</td></tr>', esc_html( $label ), esc_html( number_format_i18n( $t['views'] ) ), esc_html( number_format_i18n( $t['visits'] ) ) );
	}
	echo '</tbody></table>';
	printf( '<p><a href="%s">Full report: pages, countries, devices and sources</a></p>', esc_url( admin_url( 'admin.php?page=rr-views' ) ) );
}

function rr_views_page() {
	$ranges = array(
		7   => 'Last 7 days',
		30  => 'Last 30 days',
		90  => 'Last 90 days',
		365 => 'Last 12 months',
	);
	$days   = isset( $_GET['days'] ) ? absint( $_GET['days'] ) : 30; // phpcs:ignore WordPress.Security.NonceVerification -- read-only filter.
	$days   = isset( $ranges[ $days ] ) ? $days : 30;
	$to     = wp_date( 'Y-m-d' );
	$from   = wp_date( 'Y-m-d', strtotime( '-' . ( $days - 1 ) . ' days' ) );
	$total  = rr_views_totals( $from, $to );
	$before = rr_views_totals( wp_date( 'Y-m-d', strtotime( '-' . ( 2 * $days - 1 ) . ' days' ) ), wp_date( 'Y-m-d', strtotime( '-' . $days . ' days' ) ) );

	global $wpdb;
	$daily = $wpdb->get_results( $wpdb->prepare( 'SELECT day, SUM(views) AS views, SUM(visits) AS visits FROM ' . rr_views_table() . ' WHERE day BETWEEN %s AND %s GROUP BY day', $from, $to ), OBJECT_K ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table name.
	$series = array();
	for ( $i = $days - 1; $i >= 0; $i-- ) {
		$d        = wp_date( 'Y-m-d', strtotime( '-' . $i . ' days' ) );
		$series[] = array( $d, isset( $daily[ $d ] ) ? (int) $daily[ $d ]->views : 0, isset( $daily[ $d ] ) ? (int) $daily[ $d ]->visits : 0 );
	}

	$geo       = rr_views_geo();
	$countries = function ( $code ) use ( $geo ) {
		return '' === $code ? 'Unknown' : ( isset( $geo['countries'][ $code ] ) ? $geo['countries'][ $code ] . ' (' . $code . ')' : $code );
	};
	?>
	<div class="wrap rr-views">
		<style>
			.rr-views .rr-range{margin:12px 0 18px}
			.rr-views .rr-range a{margin-right:6px}
			.rr-views .rr-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:18px}
			.rr-views .rr-card{background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:14px 16px}
			.rr-views .rr-card b{display:block;font-size:26px;line-height:1.2;margin-top:4px}
			.rr-views .rr-up{color:#007017}.rr-views .rr-down{color:#b32d2e}
			.rr-views .rr-chart{background:#fff;border:1px solid #dcdcde;border-radius:6px;padding:14px 16px;margin-bottom:18px}
			.rr-views .rr-chart svg{width:100%;height:160px;display:block}
			.rr-views .rr-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(360px,1fr));gap:18px}
			.rr-views .rr-grid h2{margin:0 0 8px;font-size:15px}
			.rr-views td.rr-l{position:relative}
			.rr-views td.rr-l span{position:absolute;left:0;top:4px;bottom:4px;background:#e5eef7;border-radius:3px;z-index:0}
			.rr-views td.rr-l em{position:relative;font-style:normal;z-index:1;word-break:break-all}
			.rr-views td.num,.rr-views th.num{text-align:right;width:70px}
		</style>
		<h1>Site views</h1>
		<p class="description">Cookieless counts of page views on this site. Your own visits while signed in are not counted. Countries are estimated from the visitor's time zone setting. A visit is an arrival from another site, a search engine, a link in an app or a typed address.</p>
		<p class="rr-range">
			<?php foreach ( $ranges as $n => $label ) : ?>
				<a class="button<?php echo $n === $days ? ' button-primary' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'days', $n, admin_url( 'admin.php?page=rr-views' ) ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</p>
		<div class="rr-cards">
			<?php
			$cards = array(
				'Page views'     => array( $total['views'], $before['views'] ),
				'Visits'         => array( $total['visits'], $before['visits'] ),
				'Pages per visit' => array( $total['visits'] ? round( $total['views'] / $total['visits'], 1 ) : 0, null ),
			);
			foreach ( $cards as $label => $c ) {
				$change = '';
				if ( null !== $c[1] && $c[1] > 0 ) {
					$pct    = (int) round( ( $c[0] - $c[1] ) / $c[1] * 100 );
					$change = sprintf( '<span class="%s">%s%d%% vs previous period</span>', $pct >= 0 ? 'rr-up' : 'rr-down', $pct >= 0 ? '+' : '', $pct );
				}
				printf( '<div class="rr-card">%s<b>%s</b>%s</div>', esc_html( $label ), esc_html( number_format_i18n( $c[0], is_float( $c[0] ) ? 1 : 0 ) ), $change ); // phpcs:ignore WordPress.Security.EscapeOutput -- built above.
			}
			?>
		</div>
		<div class="rr-chart">
			<?php echo rr_views_chart( $series ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from integers and dates. ?>
		</div>
		<div class="rr-grid">
			<?php
			rr_views_table_html( 'Pages', 'Page', rr_views_by( 'path', $from, $to, 25 ), $total['views'], 'views', function ( $p ) {
				return '<a href="' . esc_url( home_url( $p ) ) . '" target="_blank" rel="noopener">' . esc_html( $p ) . '</a>';
			} );
			rr_views_table_html( 'Countries', 'Country', rr_views_by( 'country', $from, $to, 25 ), $total['views'], 'views', function ( $c ) use ( $countries ) {
				return esc_html( $countries( $c ) );
			} );
			rr_views_table_html( 'Devices', 'Device', rr_views_by( 'device', $from, $to ), $total['views'] );
			rr_views_table_html( 'Sources (visits)', 'Source', rr_views_by( 'source', $from, $to, 25, 'visits' ), $total['visits'], 'visits', function ( $s ) {
				return '' === $s ? 'Direct or unknown' : esc_html( $s );
			} );
			rr_views_table_html( 'Browsers', 'Browser', rr_views_by( 'browser', $from, $to ), $total['views'] );
			rr_views_table_html( 'Operating systems', 'System', rr_views_by( 'os', $from, $to ), $total['views'] );
			?>
		</div>
	</div>
	<?php
}

function rr_views_table_html( $title, $head, $rows, $total, $metric = 'views', $format = null ) {
	echo '<div><h2>' . esc_html( $title ) . '</h2><table class="widefat striped"><thead><tr><th>' . esc_html( $head ) . '</th><th class="num">' . ( 'visits' === $metric ? 'Visits' : 'Views' ) . '</th><th class="num">Share</th></tr></thead><tbody>';
	if ( ! $rows ) {
		echo '<tr><td colspan="3">No data yet.</td></tr>';
	}
	$max = $rows ? max( 1, (int) $rows[0][ $metric ] ) : 1;
	foreach ( $rows as $r ) {
		$n     = (int) $r[ $metric ];
		$label = $format ? $format( $r['label'] ) : esc_html( '' === $r['label'] ? 'Unknown' : $r['label'] );
		printf(
			'<tr><td class="rr-l"><span style="width:%d%%"></span><em>%s</em></td><td class="num">%s</td><td class="num">%s%%</td></tr>',
			(int) round( $n / $max * 100 ),
			$label, // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by the formatter.
			esc_html( number_format_i18n( $n ) ),
			esc_html( $total ? number_format_i18n( $n / $total * 100, 1 ) : '0' )
		);
	}
	echo '</tbody></table></div>';
}

/** Daily bars: page views, with visits as a darker inner bar. */
function rr_views_chart( $series ) {
	$n    = count( $series );
	$max  = max( 1, max( array_column( $series, 1 ) ) );
	$w    = 1000 / $n;
	$bars = '';
	foreach ( $series as $i => $s ) {
		$hv    = $s[1] / $max * 140;
		$hs    = $s[2] / $max * 140;
		$x     = $i * $w + $w * 0.15;
		$bw    = max( 1, $w * 0.7 );
		$bars .= sprintf(
			'<g><title>%s: %d views, %d visits</title><rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" fill="#9ec2e6"/><rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" fill="#2271b1"/></g>',
			esc_html( wp_date( 'D j M Y', strtotime( $s[0] ) ) ),
			$s[1],
			$s[2],
			$x,
			150 - $hv,
			$bw,
			$hv,
			$x,
			150 - $hs,
			$bw,
			$hs
		);
	}
	$first = esc_html( wp_date( 'j M', strtotime( $series[0][0] ) ) );
	$last  = esc_html( wp_date( 'j M', strtotime( $series[ $n - 1 ][0] ) ) );
	return '<p style="margin:0 0 6px"><strong>Per day</strong> &middot; light: page views, dark: visits &middot; peak ' . esc_html( number_format_i18n( $max ) ) . ' views</p>'
		. '<svg viewBox="0 0 1000 152" preserveAspectRatio="none" role="img" aria-label="Page views per day"><line x1="0" y1="150.5" x2="1000" y2="150.5" stroke="#dcdcde"/>' . $bars . '</svg>'
		. '<p style="display:flex;justify-content:space-between;margin:4px 0 0;color:#646970"><span>' . $first . '</span><span>' . $last . '</span></p>';
}
