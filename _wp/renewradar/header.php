<?php
/**
 * Site header.
 */

defined( 'ABSPATH' ) || exit;

$rr_nav = array(
	array( '/subscription-tracker/', 'Features', is_page( array( 'subscription-tracker', 'multi-currency-subscription-tracker', 'private-subscription-tracker', 'rocket-money-alternative', 'bobby-alternative-android' ) ) ),
	array( '/free-trial-reminder/', 'Free trials', is_page( 'free-trial-reminder' ) ),
	array( '/subscription-cost-calculator/', 'Calculator', is_page( 'subscription-cost-calculator' ) ),
	array( '/guides/', 'Guides', is_home() || is_single() ),
	array( '/#pricing', 'Pricing', false ),
);
$rr_modes = array(
	'light'   => array( 'Light mode', '<circle cx="12" cy="12" r="4"/><path d="M12 2.5v2M12 19.5v2M4.6 4.6L6 6M18 18l1.4 1.4M2.5 12h2M19.5 12h2M4.6 19.4L6 18M18 6l1.4-1.4"/>' ),
	'dark'    => array( 'Dark mode', '<path d="M20.5 13.2A8.5 8.5 0 1 1 10.8 3.5a6.6 6.6 0 0 0 9.7 9.7z"/>' ),
	'festive' => array( 'Christmas mode', '<path d="M12 2.5v19M3.8 7.2l16.4 9.6M3.8 16.8l16.4-9.6M9.5 4l2.5 2.5L14.5 4M9.5 20l2.5-2.5 2.5 2.5M4.2 10.3l3.4.9-.9 3.4M19.8 13.7l-3.4-.9.9-3.4"/>' ),
);
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main">Skip to content</a>
<header class="top"><div class="wrap bar">
	<a class="lockup" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="RenewRadar, home"><img src="<?php echo esc_url( rr_asset( 'img/icon.png' ) ); ?>" alt="" width="38" height="38"><span><b>RenewRadar</b><small>by Pixel DevCode</small></span></a>
	<nav class="nav" id="site-nav" aria-label="Main">
		<?php foreach ( $rr_nav as $item ) : ?>
			<a href="<?php echo esc_url( home_url( $item[0] ) ); ?>"<?php echo $item[2] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item[1] ); ?></a>
		<?php endforeach; ?>
	</nav>
	<div class="bar-end">
		<div class="modes" role="group" aria-label="Color mode">
			<?php foreach ( $rr_modes as $mode => $m ) : ?>
				<button type="button" data-mode="<?php echo esc_attr( $mode ); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( $m[0] ); ?>" title="<?php echo esc_attr( $m[0] ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $m[1]; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></svg></button>
			<?php endforeach; ?>
		</div>
		<?php echo rr_play( 'sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<button class="menu-btn" type="button" aria-label="Menu" aria-expanded="false" aria-controls="site-nav"><span></span><span></span><span></span></button>
	</div>
</div></header>
<main id="main">
