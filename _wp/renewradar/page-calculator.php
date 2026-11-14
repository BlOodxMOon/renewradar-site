<?php
/**
 * Template Name: Calculator
 *
 * The subscription cost calculator. The tool itself runs in assets/calc.js; the page content shows below it.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rr_chips      = array(
	array( 'Video streaming', 'monthly' ),
	array( 'Music', 'monthly' ),
	array( 'Cloud storage', 'monthly' ),
	array( 'AI assistant', 'monthly' ),
	array( 'Gym', 'monthly' ),
	array( 'News', 'monthly' ),
	array( 'Gaming', 'monthly' ),
	array( 'Software', 'yearly' ),
	array( 'VPN', 'yearly' ),
);
$rr_currencies = array( 'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'PHP', 'INR', 'JPY', 'SGD', 'NZD', 'ZAR', 'MXN', 'BRL' );

while ( have_posts() ) :
	the_post();
	$rr_id = get_the_ID();
	rr_page_hero(
		array(
			'tag'   => get_post_meta( $rr_id, 'rr_tag', true ),
			'title' => get_the_title(),
			'lede'  => get_post_meta( $rr_id, 'rr_lede', true ),
			'cta'   => false,
		)
	);
	?>
	<div class="wrap">
		<form class="calc" id="calc" autocomplete="off">
			<div class="calc-panel">
				<h2>Your subscriptions</h2>
				<p class="fine" style="margin:0">Change the examples or add your own.</p>
				<div class="calc-row calc-head" aria-hidden="true"><span>Name</span><span>Price</span><span>Billed</span><span></span></div>
				<div id="rows"></div>
				<noscript><p class="note">The calculator needs JavaScript to run.</p></noscript>
				<div class="calc-tools">
					<button type="button" class="btn btn-ghost btn-sm" id="add-row">+ Add a subscription</button>
					<label class="fine" for="currency" style="margin-left:auto">Currency</label>
					<select id="currency" style="width:auto">
						<?php foreach ( $rr_currencies as $c ) : ?>
							<option><?php echo esc_html( $c ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="chips" aria-label="Quick add">
					<?php foreach ( $rr_chips as $chip ) : ?>
						<button type="button" class="chip" data-add="<?php echo esc_attr( $chip[0] ); ?>" data-cycle="<?php echo esc_attr( $chip[1] ); ?>">+ <?php echo esc_html( $chip[0] ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
			<aside class="result" aria-live="polite">
				<p class="tag">Per month</p>
				<div class="big" id="out-month">0</div>
				<p class="fine" style="color:inherit;opacity:.7;margin:8px 0 0" id="out-count">0 subscriptions</p>
				<div class="result-grid">
					<div><b id="out-year">0</b><span>per year</span></div>
					<div><b id="out-five">0</b><span>over 5 years</span></div>
					<div><b id="out-day">0</b><span>per day</span></div>
				</div>
				<ul class="bars" id="bars"></ul>
				<p style="margin:22px 0 10px;font-size:15px;opacity:.85">Get a reminder before each of these is charged.</p>
				<?php echo rr_play(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</aside>
		</form>
	</div>
	<?php
	rr_article( $rr_id );
	echo rr_cta_band( 'Turn the number into reminders.', 'RenewRadar tracks each subscription and warns you before every charge. Free for up to five.' ); // phpcs:ignore WordPress.Security.EscapeOutput
endwhile;

get_footer();
