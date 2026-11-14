<?php
/**
 * Not found.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="hero"><div class="wrap" style="max-width:760px;padding-bottom:72px">
	<p class="tag">404</p>
	<h1>Nothing on the radar here.</h1>
	<p class="lede">The page you are looking for has moved or never existed. These might help:</p>
	<div class="hero-cta"><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Go to the home page</a><a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/guides/' ) ); ?>">Browse the guides</a></div>
</div></section>
<?php
get_footer();
