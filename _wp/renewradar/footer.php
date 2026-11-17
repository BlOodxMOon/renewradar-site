<?php
/**
 * Site footer.
 */

defined( 'ABSPATH' ) || exit;

$rr_cols = array(
	'Product'          => array(
		array( '/subscription-tracker/', 'Subscription tracker' ),
		array( '/free-trial-reminder/', 'Free trial reminders' ),
		array( '/subscription-reminder-app/', 'Subscription reminders' ),
		array( '/renewal-reminder-app/', 'Renewal reminders' ),
		array( '/multi-currency-subscription-tracker/', 'Multi-currency tracking' ),
		array( '/private-subscription-tracker/', 'Private by design' ),
		array( '/#pricing', 'Pricing' ),
	),
	'Tools and guides' => array(
		array( '/subscription-cost-calculator/', 'Cost calculator' ),
		array( '/guides/cancel-free-trial-before-charged/', 'Cancel a free trial in time' ),
		array( '/guides/cancel-google-play-subscription/', 'Cancel on Google Play' ),
		array( '/guides/subscription-audit/', 'Subscription audit' ),
		array( '/guides/', 'All guides' ),
	),
	'Compare'          => array(
		array( '/rocket-money-alternative/', 'Rocket Money alternative' ),
		array( '/bobby-alternative-android/', 'Bobby for Android' ),
	),
	'Company'          => array(
		array( '/about/', 'About' ),
		array( '/contact/', 'Contact' ),
		array( '/privacy.html', 'Privacy policy' ),
		array( '/terms.html', 'Terms of service' ),
		array( 'https://pixeldevcode.com', 'Pixel DevCode' ),
	),
);
?>
</main>
<footer class="foot"><div class="wrap">
	<div class="foot-grid">
		<div class="foot-brand">
			<a class="lockup" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="RenewRadar, home"><span class="logo-ic"><img src="<?php echo esc_url( rr_asset( 'img/icon.png' ) ); ?>" alt="" width="38" height="38"><?php echo rr_hat_svg(); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></span><span><b>RenewRadar</b><small>by Pixel DevCode</small></span></a>
			<p>A private subscription and free trial tracker for Android. Your list stays on your phone.</p>
			<?php echo rr_play(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo rr_socials(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>
		</div>
		<?php foreach ( $rr_cols as $title => $links ) : ?>
			<div><h2><?php echo esc_html( $title ); ?></h2><ul>
				<?php foreach ( $links as $l ) : ?>
					<li><a href="<?php echo esc_url( 0 === strpos( $l[0], '/' ) ? home_url( $l[0] ) : $l[0] ); ?>"><?php echo esc_html( $l[1] ); ?></a></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endforeach; ?>
	</div>
	<div class="foot-base"><span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <a href="https://pixeldevcode.com">Pixel DevCode</a>. Google Play and the Google Play logo are trademarks of Google LLC.</span><span>Made for Android</span></div>
</div></footer>
<?php wp_footer(); ?>
</body>
</html>
