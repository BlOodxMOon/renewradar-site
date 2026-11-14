<?php
/**
 * RenewRadar theme setup and shared building blocks.
 */

defined( 'ABSPATH' ) || exit;

const RR_PLAY  = 'https://play.google.com/store/apps/details?id=com.pixeldevcode.renewradar';
const RR_EMAIL = 'renewradar@pixeldevcode.com';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
} );

/* ------------------------------------------------------------------ */
/* Color mode setting (Appearance > Customize > Color mode)            */
/* ------------------------------------------------------------------ */

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section( 'rr_mode', array(
		'title'    => 'Color mode',
		'priority' => 30,
	) );
	$wp_customize->add_setting( 'rr_default_mode', array(
		'default'           => 'festive',
		'sanitize_callback' => function ( $value ) {
			return in_array( $value, array( 'festive', 'light', 'dark', 'system' ), true ) ? $value : 'festive';
		},
	) );
	$wp_customize->add_control( 'rr_default_mode', array(
		'section'     => 'rr_mode',
		'label'       => 'Default mode for new visitors',
		'description' => 'Visitors can still switch with the buttons in the header. Their choice is remembered.',
		'type'        => 'radio',
		'choices'     => array(
			'festive' => 'Christmas (winter wonderland)',
			'light'   => 'Light',
			'dark'    => 'Dark',
			'system'  => 'Follow the visitor\'s device',
		),
	) );
} );

function rr_default_mode() {
	return get_theme_mod( 'rr_default_mode', 'festive' );
}

/* ------------------------------------------------------------------ */
/* Cookieless analytics (Cloudflare Web Analytics)                     */
/* ------------------------------------------------------------------ */

// Token from Cloudflare (Web Analytics, manual JS snippet). The Customizer field overrides it.
const RR_CF_TOKEN = '';

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section( 'rr_analytics', array(
		'title'    => 'Analytics',
		'priority' => 31,
	) );
	$wp_customize->add_setting( 'rr_cf_token', array(
		'default'           => '',
		'sanitize_callback' => function ( $value ) {
			return preg_replace( '/[^a-zA-Z0-9]/', '', (string) $value );
		},
	) );
	$wp_customize->add_control( 'rr_cf_token', array(
		'section'     => 'rr_analytics',
		'label'       => 'Cloudflare Web Analytics token',
		'description' => 'Cookieless page views. Leave empty to turn analytics off. Mention any change in the privacy policy.',
		'type'        => 'text',
	) );
} );

function rr_cf_token() {
	$token = get_theme_mod( 'rr_cf_token', '' );
	return $token ? $token : RR_CF_TOKEN;
}

add_action( 'wp_footer', function () {
	$token = rr_cf_token();
	if ( ! $token || is_user_logged_in() ) {
		return; // Your own visits while logged in are not counted.
	}
	printf(
		'<script defer src="https://static.cloudflareinsights.com/beacon.min.js" data-cf-beacon=\'%s\'></script>' . "\n",
		esc_attr( wp_json_encode( array( 'token' => $token ) ) )
	);
}, 99 );

/* ------------------------------------------------------------------ */
/* Assets                                                              */
/* ------------------------------------------------------------------ */

function rr_asset( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

function rr_asset_ver( $path ) {
	$file = get_template_directory() . '/assets/' . $path;
	return file_exists( $file ) ? (string) filemtime( $file ) : null;
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'renewradar', rr_asset( 'site.css' ), array(), rr_asset_ver( 'site.css' ) );
	wp_enqueue_script( 'renewradar', rr_asset( 'site.js' ), array(), rr_asset_ver( 'site.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	if ( is_page_template( 'page-calculator.php' ) ) {
		wp_enqueue_script( 'renewradar-calc', rr_asset( 'calc.js' ), array(), rr_asset_ver( 'calc.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
	// Block library styles are not used by this theme.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 20 );

// Lighter pages: no emoji script, no generator tag.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );

/* ------------------------------------------------------------------ */
/* Head: titles, descriptions, social tags, mode script                */
/* ------------------------------------------------------------------ */

add_filter( 'pre_get_document_title', function ( $title ) {
	if ( is_front_page() ) {
		return 'RenewRadar: Subscription and Free Trial Tracker for Android';
	}
	$id = rr_current_id();
	if ( $id ) {
		$seo = get_post_meta( $id, 'rr_seo_title', true );
		if ( $seo ) {
			return $seo;
		}
	}
	if ( is_404() ) {
		return 'Page not found | RenewRadar';
	}
	return $title;
} );

add_filter( 'document_title_separator', function () {
	return '|';
} );

/** The post or page the current view is about, including the Guides page. */
function rr_current_id() {
	if ( is_home() && ! is_front_page() ) {
		return (int) get_option( 'page_for_posts' );
	}
	if ( is_singular() ) {
		return (int) get_queried_object_id();
	}
	return 0;
}

function rr_description() {
	if ( is_front_page() ) {
		return 'Track every subscription and free trial on one screen and get reminded before each charge. No bank login, no account. Free for five subscriptions on Android.';
	}
	$id = rr_current_id();
	if ( $id ) {
		$excerpt = get_post_field( 'post_excerpt', $id );
		if ( $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
		return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $id ) ), 28, '' );
	}
	return get_bloginfo( 'description' );
}

add_action( 'wp_head', function () {
	$mode  = rr_default_mode();
	$desc  = rr_description();
	$title = wp_get_document_title();
	$url   = is_front_page() ? home_url( '/' ) : ( rr_current_id() ? get_permalink( rr_current_id() ) : home_url( add_query_arg( array() ) ) );
	$image = rr_asset( 'img/og-banner.png' );
	?>
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>">
	<?php if ( is_home() && ! is_front_page() ) : // WordPress adds canonicals to single views only. ?>
	<link rel="canonical" href="<?php echo esc_url( get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) ) ); ?>">
	<?php endif; ?>
	<meta name="google-site-verification" content="j1CO1LTKK34j4kRKD7CKyY3hLCw61mzQaFAs1GppANA" />
	<?php if ( 'festive' === $mode ) : ?>
	<meta name="theme-color" content="#13284b">
	<?php else : ?>
	<meta name="theme-color" content="#f6f8fc" media="(prefers-color-scheme: light)">
	<meta name="theme-color" content="#0d0c20" media="(prefers-color-scheme: dark)">
	<?php endif; ?>
	<meta property="og:type" content="<?php echo is_single() ? 'article' : 'website'; ?>">
	<meta property="og:site_name" content="RenewRadar">
	<meta property="og:locale" content="en_US">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="630">
	<meta property="og:image:alt" content="RenewRadar by Pixel DevCode. Know before you are charged.">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>">
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<link rel="icon" href="<?php echo esc_url( home_url( '/favicon.ico' ) ); ?>" sizes="48x48">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( rr_asset( 'img/favicon-32.png' ) ); ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( rr_asset( 'img/favicon-16.png' ) ); ?>">
	<link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( rr_asset( 'img/favicon-192.png' ) ); ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( rr_asset( 'img/apple-touch-icon.png' ) ); ?>">
	<link rel="preload" href="<?php echo esc_url( rr_asset( 'fonts/plus-jakarta-sans.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>
	<script>document.documentElement.className="js";try{if(sessionStorage.getItem("rr-seen"))document.documentElement.className+=" seen";var t=localStorage.getItem("rr-theme");}catch(e){}if(t!=="light"&&t!=="dark"&&t!=="festive")t=<?php echo wp_json_encode( 'system' === $mode ? '' : $mode ); ?>;if(t)document.documentElement.setAttribute("data-theme",t)</script>
	<?php
	foreach ( rr_schema() as $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
}, 1 );

add_filter( 'site_icon_meta_tags', '__return_empty_array' );

/* Archives that only duplicate the Guides page: keep them out of search, and do not expose author names. */
add_filter( 'wp_robots', function ( $robots ) {
	if ( is_category() || is_tag() || is_date() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
} );

add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'taxonomies' === $name ? false : $provider;
}, 10, 2 );

add_action( 'template_redirect', function () {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/guides/' ), 301 );
		exit;
	}
}, 1 );

/* ------------------------------------------------------------------ */
/* Structured data                                                     */
/* ------------------------------------------------------------------ */

function rr_schema() {
	$out = array();
	if ( is_front_page() ) {
		$out[] = array(
			'@context'            => 'https://schema.org',
			'@type'               => 'MobileApplication',
			'name'                => 'RenewRadar: Subscriptions',
			'operatingSystem'     => 'ANDROID',
			'applicationCategory' => 'FinanceApplication',
			'description'         => 'A private subscription and free trial tracker. Add subscriptions by hand, see monthly and yearly totals, and get reminders before every charge.',
			'url'                 => home_url( '/' ),
			'downloadUrl'         => RR_PLAY,
			'installUrl'          => RR_PLAY,
			'image'               => rr_asset( 'img/icon.png' ),
			'offers'              => array( '@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD' ),
			'publisher'           => array( '@type' => 'Organization', 'name' => 'Pixel DevCode', 'url' => 'https://pixeldevcode.com' ),
		);
		$out[] = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'WebSite',
			'name'      => 'RenewRadar',
			'url'       => home_url( '/' ),
			'publisher' => array( '@type' => 'Organization', 'name' => 'Pixel DevCode' ),
		);
		$out[] = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => 'Pixel DevCode',
			'url'      => 'https://pixeldevcode.com',
			'logo'     => rr_asset( 'img/icon.png' ),
			'email'    => RR_EMAIL,
		);
		$out[] = rr_faq_schema( rr_home_faq() );
		return $out;
	}
	$crumbs = rr_crumbs();
	if ( count( $crumbs ) > 1 ) {
		$items = array();
		foreach ( $crumbs as $i => $c ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $c[0] ),
				'item'     => $c[1] ? $c[1] : ( rr_current_id() ? get_permalink( rr_current_id() ) : home_url( '/' ) ),
			);
		}
		$out[] = array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items );
	}
	if ( is_single() ) {
		$id    = get_queried_object_id();
		$out[] = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => get_the_title( $id ),
			'description'      => rr_description(),
			'mainEntityOfPage' => get_permalink( $id ),
			'datePublished'    => get_the_date( 'c', $id ),
			'dateModified'     => get_the_modified_date( 'c', $id ),
			'image'            => rr_asset( 'img/og-banner.png' ),
			'author'           => array( '@type' => 'Organization', 'name' => 'Pixel DevCode', 'url' => 'https://pixeldevcode.com' ),
			'publisher'        => array(
				'@type' => 'Organization',
				'name'  => 'Pixel DevCode',
				'logo'  => array( '@type' => 'ImageObject', 'url' => rr_asset( 'img/icon.png' ) ),
			),
		);
	}
	return $out;
}

function rr_faq_schema( $items ) {
	$entities = array();
	foreach ( $items as $qa ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $qa[0],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $qa[1] ) ),
		);
	}
	return array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities );
}

/* ------------------------------------------------------------------ */
/* Building blocks                                                     */
/* ------------------------------------------------------------------ */

function rr_icon( $name ) {
	$icons = array(
		'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'swap'  => '<path d="M7 7h12l-3.5-3.5M17 17H5l3.5 3.5"/>',
		'book'  => '<path d="M4.5 5.5A2 2 0 0 1 6.5 3.5H19v15H6.5a2 2 0 0 0-2 2z"/><path d="M4.5 20.5v-15"/>',
		'calc'  => '<rect x="5" y="3.5" width="14" height="17" rx="2.5"/><path d="M8.5 7.5h7M8.5 12h1M12 12h1M15.5 12h0M8.5 16h1M12 16h1M15.5 16h0"/>',
		'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
	);
	return '<svg viewBox="0 0 24 24" aria-hidden="true">' . ( isset( $icons[ $name ] ) ? $icons[ $name ] : '' ) . '</svg>';
}

function rr_play_icon() {
	return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M1.337.924a1.486 1.486 0 0 0-.112.568v21.017c0 .217.045.419.124.6l11.155-11.087L1.337.924z"/><path fill="#34A853" d="M13.544 10.989l3.258-3.238L3.45.195a1.466 1.466 0 0 0-.946-.179l11.04 10.973z"/><path fill="#FBBC04" d="M22.018 13.298l-3.919 2.218-3.515-3.493 3.543-3.521 3.891 2.202a1.49 1.49 0 0 1 0 2.594z"/><path fill="#EA4335" d="M13.544 13.056l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54-3.23-3.21z"/></svg>';
}

function rr_play( $size = '' ) {
	return sprintf(
		'<a class="play%s" href="%s">%s<span>%s<b>Google Play</b></span></a>',
		$size ? ' play-' . esc_attr( $size ) : '',
		esc_url( RR_PLAY ),
		rr_play_icon(),
		$size ? '' : '<small>Get it on</small>'
	);
}

function rr_device( $img, $alt, $cls = 'device' ) {
	$sizes = array( 'app-trial.jpg' => array( 672, 2048 ) );
	$wh    = isset( $sizes[ $img ] ) ? $sizes[ $img ] : array( 922, 2048 );
	return sprintf(
		'<div class="%s"><div><img src="%s" alt="%s" width="%d" height="%d" loading="lazy" decoding="async"></div></div>',
		esc_attr( $cls ),
		esc_url( rr_asset( 'img/' . $img ) ),
		esc_attr( $alt ),
		$wh[0],
		$wh[1]
	);
}

function rr_cta_band( $title = 'Know before you are charged.', $text = 'Free for up to five subscriptions. Pro is a single one-time purchase.' ) {
	return '<section class="cta-band"><div class="wrap"><div class="cta-box reveal"><div><h2>' . esc_html( $title ) . '</h2><p>' . esc_html( $text ) . '</p></div>' . rr_play() . '</div></div></section>';
}

/** Breadcrumb trail as [label, url] pairs; the last item has no url. */
function rr_crumbs() {
	if ( is_front_page() ) {
		return array();
	}
	$crumbs = array( array( 'Home', home_url( '/' ) ) );
	if ( is_home() ) {
		$crumbs[] = array( 'Guides', '' );
	} elseif ( is_single() ) {
		$crumbs[] = array( 'Guides', get_permalink( get_option( 'page_for_posts' ) ) );
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_page() ) {
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_404() ) {
		$crumbs[] = array( 'Not found', '' );
	}
	return $crumbs;
}

function rr_crumbs_html() {
	$parts = array();
	foreach ( rr_crumbs() as $c ) {
		$parts[] = $c[1]
			? '<li><a href="' . esc_url( $c[1] ) . '">' . esc_html( $c[0] ) . '</a></li>'
			: '<li><span aria-current="page">' . esc_html( $c[0] ) . '</span></li>';
	}
	return '<ol class="crumbs">' . implode( '', $parts ) . '</ol>';
}

/**
 * Adds ids to h2 headings that lack one and returns [content, toc].
 * The toc is a list of [id, label] pairs for the sidebar.
 */
function rr_toc( $content ) {
	$toc  = array();
	$used = array();
	$html = preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/is',
		function ( $m ) use ( &$toc, &$used ) {
			$attrs = $m[1];
			$label = trim( wp_strip_all_tags( preg_replace( '/<span class="n">.*?<\/span>/', '', $m[2] ) ) );
			if ( preg_match( '/id="([^"]+)"/', $attrs, $idm ) ) {
				$id = $idm[1];
			} else {
				$base = sanitize_title( $label );
				$id   = $base ? $base : 'section';
				$n    = 2;
				while ( isset( $used[ $id ] ) ) {
					$id = $base . '-' . $n++;
				}
				$attrs .= ' id="' . esc_attr( $id ) . '"';
			}
			$used[ $id ] = true;
			$toc[]       = array( $id, $label );
			return '<h2' . $attrs . '>' . $m[2] . '</h2>';
		},
		$content
	);
	// Headings inside <section id="..."> (legal pages) link to the section.
	if ( preg_match_all( '/<section id="([^"]+)"><h2[^>]*>(.*?)<\/h2>/is', $html, $sm, PREG_SET_ORDER ) ) {
		$toc = array();
		foreach ( $sm as $s ) {
			$toc[] = array( $s[1], trim( wp_strip_all_tags( preg_replace( '/<span class="n">.*?<\/span>/', '', $s[2] ) ) ) );
		}
	}
	return array( $html, $toc );
}

function rr_related_html( $post_id ) {
	$items = get_post_meta( $post_id, 'rr_related', true );
	if ( ! is_array( $items ) || ! $items ) {
		return '';
	}
	$cards = '';
	foreach ( $items as $r ) {
		$href   = isset( $r['href'] ) ? $r['href'] : '';
		$href   = 0 === strpos( $href, '/' ) ? home_url( $href ) : $href;
		$cards .= '<a class="card" href="' . esc_url( $href ) . '"><h3>' . wp_kses_post( $r['title'] ) . '</h3><p>' . wp_kses_post( $r['text'] ) . '</p><span class="go">Read more &rarr;</span></a>';
	}
	return '<aside class="related"><h2>Keep reading</h2><div class="grid-2">' . $cards . '</div></aside>';
}

function rr_reading_minutes( $post_id ) {
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
	return max( 1, (int) round( $words / 200 ) );
}

function rr_home_faq() {
	return array(
		array( 'Is RenewRadar free?', 'Yes. You can track up to five active subscriptions and free trials for free, with reminders, trial countdowns and totals. Pro removes the limit and adds detailed reports and CSV export for a single one-time payment. Cancelled items do not count toward the free limit.' ),
		array( 'Does it connect to my bank?', 'No. RenewRadar never asks for bank logins or card details. You add each subscription yourself, which takes about a minute, and nothing is read from your accounts.' ),
		array( 'Do I need to create an account?', 'No. There is no sign-up, no email and no password. Your list is stored only on your phone.' ),
		array( 'Can RenewRadar cancel a subscription for me?', 'No. It tells you in time, and you cancel with the service itself. Our <a href="' . esc_url( home_url( '/guides/cancel-free-trial-before-charged/' ) ) . '">guide to cancelling a free trial</a> shows where to do it for the most common billing methods.' ),
		array( 'Will reminders arrive if I do not open the app?', 'Yes. Reminders are scheduled as notifications on your phone, so they work without the app open and without internet. Some Android phones with strict battery saving can delay them; allowing notifications and setting RenewRadar to unrestricted battery use fixes that.' ),
		array( 'Which currencies does it support?', 'Any currency with a three-letter code. USD, EUR, GBP, CAD, AUD, PHP, INR and JPY are one tap away. When you track more than one currency, totals are shown per currency, because RenewRadar does not guess exchange rates.' ),
		array( 'Is there an iPhone version?', 'Not yet. RenewRadar is available for Android on Google Play first.' ),
		array( 'What happens to my list if I change phones?', 'Your list stays on the old phone because nothing is synced to a server. Pro users can export a CSV copy at any time. Pro itself is linked to your Google account, so Restore purchase brings it back on a new phone.' ),
	);
}

function rr_faq_html( $items ) {
	$out = '<div class="faq">';
	foreach ( $items as $qa ) {
		$out .= '<details><summary>' . esc_html( $qa[0] ) . '</summary><div>' . wp_kses_post( $qa[1] ) . '</div></details>';
	}
	return $out . '</div>';
}

/** Page hero used by pages, posts and the guides list. */
function rr_page_hero( $args ) {
	$args = wp_parse_args( $args, array( 'tag' => '', 'title' => '', 'lede' => '', 'meta' => '', 'cta' => true ) );
	echo '<header class="page-hero"><div class="wrap">';
	echo rr_crumbs_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the function.
	if ( $args['tag'] ) {
		echo '<p class="tag">' . esc_html( $args['tag'] ) . '</p>';
	}
	echo '<h1>' . wp_kses_post( $args['title'] ) . '</h1>';
	if ( $args['lede'] ) {
		echo '<p class="lede">' . wp_kses_post( $args['lede'] ) . '</p>';
	}
	if ( $args['cta'] ) {
		echo '<div class="hero-cta">' . rr_play() . '<a class="btn btn-primary" href="' . esc_url( home_url( '/#how' ) ) . '">See how it works</a></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	if ( $args['meta'] ) {
		echo '<p class="meta">' . wp_kses_post( $args['meta'] ) . '</p>';
	}
	echo '</div>' . rr_boat_svg() . '<span class="ph-waves" aria-hidden="true"></span></header>'; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
}

/** The radar sailboat from the home hero, for the page hero. */
function rr_boat_svg() {
	return '<svg class="ph-boat" viewBox="-100 -180 210 205" aria-hidden="true" focusable="false"><g class="s-bob s-boat">'
		. '<path class="s-mast" d="M-1 -12 V-152"/>'
		. '<path class="s-sail" d="M-4 -146 V-16 H-66 Q-44 -82 -4 -146 Z"/>'
		. '<path class="s-sail s-sail-2" d="M3 -136 Q40 -72 66 -14 H3 Z"/>'
		. '<circle class="s-ping" cx="-1" cy="-158" r="7"/><circle class="s-ping s-ping-2" cx="-1" cy="-158" r="7"/><circle class="s-lamp" cx="-1" cy="-158" r="5.5"/>'
		. '<g transform="translate(80 -10)"><path class="s-legs" d="M-4 0 V-14 M4 0 V-14"/><rect class="s-shirt" x="-7" y="-30" width="14" height="18" rx="5"/><path class="s-stripes" d="M-6 -24 H6 M-6 -19 H6"/><circle class="s-skin" cx="0" cy="-37" r="6.5"/><path class="s-cap" d="M-7 -40 Q0 -50 7 -40 Z"/><rect class="s-capband" x="-8" y="-41.5" width="16" height="3" rx="1.5"/><path class="s-arm" d="M4 -26 L13 -36"/><rect class="s-glass" x="9" y="-44" width="16" height="5" rx="2" transform="rotate(-24 9 -41)"/></g>'
		. '<path class="s-hull" d="M-92 -10 H104 Q92 14 66 20 H-70 Q-88 12 -92 -10 Z"/><path class="s-hullline" d="M-86 0 H98"/>'
		. '</g></svg>';
}

/** Shared body for pages and posts: optional table of contents, content, related links. */
function rr_article( $post_id ) {
	$raw = (string) get_post_field( 'post_content', $post_id );
	// Imported pages are already full HTML; automatic paragraphs would add stray <p> and <br> tags.
	$html_authored = ! has_blocks( $raw ) && 0 === strpos( ltrim( $raw ), '<' );
	if ( $html_authored ) {
		remove_filter( 'the_content', 'wpautop' );
	}
	$content = apply_filters( 'the_content', $raw );
	if ( $html_authored ) {
		add_filter( 'the_content', 'wpautop' );
	}
	list( $html, $toc ) = rr_toc( $content );
	$show_toc         = count( $toc ) >= 3;
	echo '<div class="wrap article' . ( $show_toc ? '' : ' no-toc' ) . '">';
	if ( $show_toc ) {
		echo '<nav class="toc" aria-label="On this page"><p>On this page</p><ol>';
		foreach ( $toc as $t ) {
			echo '<li><a href="#' . esc_attr( $t[0] ) . '">' . esc_html( $t[1] ) . '</a></li>';
		}
		echo '</ol></nav>';
	}
	echo '<div class="prose">' . $html . rr_related_html( $post_id ) . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- post content.
}

// Old Classic content uses class names the editor would otherwise strip on save; allow them.
add_filter( 'wp_kses_allowed_html', function ( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['section'] = array( 'id' => true, 'class' => true );
		$tags['details'] = array( 'open' => true, 'class' => true );
		$tags['summary'] = array( 'class' => true );
	}
	return $tags;
}, 10, 2 );
