<?php
/**
 * Home page.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rr_tick = function ( $b, $t ) {
	return '<li><b>' . esc_html( $b ) . '</b> ' . esc_html( $t ) . '</li>';
};
?>
<section class="hero" id="top">
	<?php echo file_get_contents( get_template_directory() . '/inc/winter.svg' ); // phpcs:ignore WordPress.Security.EscapeOutput,WordPress.WP.AlternativeFunctions -- static SVG shipped with the theme. ?>
	<?php echo file_get_contents( get_template_directory() . '/inc/sea.svg' ); // phpcs:ignore WordPress.Security.EscapeOutput,WordPress.WP.AlternativeFunctions -- static SVG shipped with the theme. ?>
	<div class="flyby-lane">
		<a class="flyby" href="<?php echo esc_url( RR_PLAY ); ?>">
			<span class="flyby-banner"><span class="badge">Now on Google Play</span>Subscription and free trial tracker for Android</span>
			<span class="flyby-rope" aria-hidden="true"></span>
			<svg class="flyby-plane" viewBox="0 0 96 48" aria-hidden="true" focusable="false"><path class="p-tail" d="M6 10 L18 10 L26 22 L10 24 Z"/><path class="p-body" d="M8 22 Q8 18 14 18 L70 18 Q86 18 90 26 Q86 32 70 32 L16 32 Q8 32 8 26 Z"/><path class="p-wing" d="M36 25 L58 25 L48 42 L40 42 Z"/><path class="p-wing-top" d="M40 19 L54 19 L48 8 L43 8 Z"/><circle class="p-window" cx="64" cy="23" r="3.2"/><circle class="p-window" cx="55" cy="23" r="3.2"/><circle class="p-window" cx="46" cy="23" r="3.2"/><rect class="p-prop" x="89" y="14" width="3" height="24" rx="1.5"/></svg>
		</a>
	</div>
	<div class="wrap">
		<h1 class="rise" style="--i:0">Know <span class="mark">before</span> you are charged.</h1>
		<p class="lede rise" style="--i:1">Every subscription and free trial on one screen, with a reminder before each charge. No bank login, no account, and your list never leaves your phone.</p>
		<div class="hero-cta rise" style="--i:2"><?php echo rr_play(); // phpcs:ignore WordPress.Security.EscapeOutput ?><a class="btn btn-primary" href="#how">How it works</a></div>
		<ul class="facts rise" style="--i:3">
			<li><?php echo rr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Free for 5 subscriptions</li>
			<li><?php echo rr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>One-time Pro, no subscription</li>
			<li><?php echo rr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Works offline</li>
		</ul>
		<div class="rise-phone rise" style="--i:4">
			<div class="frame"><span class="notif" aria-hidden="true"><img src="<?php echo esc_url( rr_asset( 'img/icon.png' ) ); ?>" alt="" width="32" height="32"><span><b>Spotify trial ends in 2 days</b>Then $11.99 a month. Keep it or cancel in time.</span><small>now</small></span><div><img src="<?php echo esc_url( rr_asset( 'img/app-home.jpg' ) ); ?>" alt="RenewRadar home screen with monthly and yearly totals, a Spotify trial with 2 days left and subscriptions sorted by renewal date" width="922" height="2048" fetchpriority="high"></div></div>
		</div>
	</div>
</section>

<?php echo rr_sale_section(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>

<?php echo rr_video_section(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside. ?>

<section class="section" id="how">
	<div class="wrap">
		<div class="section-head reveal">
			<p class="tag">How it works</p>
			<h2>Three steps, about a minute per subscription.</h2>
		</div>
		<div class="grid-3 reveal">
			<div class="card"><?php echo rr_step_shot( 'add', 'The New subscription screen with the Service name search box highlighted' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="dot">1</div><h3>Add it</h3><p>Type a name and pick from suggestions for popular services. Set the price, currency, billing cycle and next charge date.</p></div>
			<div class="card"><?php echo rr_step_shot( 'reminders', 'The Reminders card with Same day, 1 day and 3 days selected' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="dot">2</div><h3>Choose your reminders</h3><p>14, 7, 3 or 1 day before, or on the day. Every subscription can have its own timing<?php echo rr_loud_alarm_live() ? ', and a loud alarm for the charges you must not miss' : ''; ?>.</p></div>
			<div class="card"><?php echo rr_step_shot( 'decide', 'The Edit, Pause and Mark as cancelled buttons on a subscription' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="dot">3</div><h3>Decide in time</h3><p>Keep it, pause it or mark it cancelled. Renewal dates roll forward on their own.</p></div>
		</div>
	</div>
</section>

<section class="section" id="reminders">
	<div class="wrap">
		<div class="section-head reveal">
			<p class="tag">Reminders</p>
			<h2>Make sure reminders reach you.</h2>
			<p>Reminders are scheduled on your phone, so they work offline. Check them once after you install.</p>
		</div>
		<div class="grid-3 reveal">
			<div class="card"><?php echo rr_step_shot( 'test-buttons', 'The Send a test reminder and Send a test loud alarm buttons in Settings' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="dot">1</div><h3>Send a test reminder</h3><p>Open Settings (the gear on Home) and tap <b>Send a test reminder</b> under Notifications. Allow notifications if your phone asks.</p></div>
			<div class="card"><div class="dot">2</div><h3>Lock your phone</h3><p>The test arrives in about 10 seconds. Leave the app or lock the screen to see it the way real reminders appear, at 9:00 on the days you chose.</p></div>
			<div class="card"><?php echo rr_step_shot( 'battery', 'Allow background usage turned on in the app battery settings' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="dot">3</div><h3>Nothing arrived?</h3><p>Turn on notifications for RenewRadar in your phone settings. On Android, open App info, Battery, and choose <b>No restrictions</b> or turn on <b>Allow background usage</b>, because some phones pause background reminders.</p></div>
		</div>
		<?php if ( rr_loud_alarm_live() ) : ?>
		<div class="card reveal loud-card" style="margin-top:18px">
			<div>
			<h3 style="margin-top:0">Loud alarm for the charges you must not miss</h3>
			<p>Edit a subscription and turn on <b>Loud alarm</b> in the Reminders card. Its last reminder then rings at alarm volume, even in silent mode, until you tap or dismiss it, and opens a full-screen view where you choose <b>I cancelled it</b> (moves it to Cancelled), <b>Keep paying for it</b> (stays active) or <b>Remind me in 1 hour</b>. Try it with <b>Send a test loud alarm</b> in Settings. Do Not Disturb still silences it unless you allow RenewRadar in your phone&rsquo;s Do Not Disturb settings. Free for everyone.</p>
			</div>
			<?php echo rr_step_shot( 'loud-switch', 'The Loud alarm switch turned on in the Reminders card', 569 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php endif; ?>
	</div>
</section>

<section class="section section-alt" id="features">
	<div class="wrap">
		<div class="split reveal">
			<div>
				<p class="tag">Free trials</p>
				<h2>Free trials get their own countdown.</h2>
				<p class="lede">Most surprise charges start as a free trial you forgot about. RenewRadar puts every trial at the top, with the days left and the price that starts afterwards.</p>
				<ul class="ticks">
					<?php
					echo $rr_tick( 'Trials come first,', 'sorted by end date.' ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo $rr_tick( 'The real price', 'after the trial, next to the days left.' ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo $rr_tick( 'Reminders before the trial ends,', 'not after the first charge.' ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</ul>
				<p style="margin-top:22px"><a class="btn btn-ghost btn-sm" href="<?php echo esc_url( home_url( '/free-trial-reminder/' ) ); ?>">More about trial reminders &rarr;</a></p>
			</div>
			<div class="shot"><?php echo rr_device( 'app-trial.jpg', 'RenewRadar detail screen for a Spotify free trial ending in 2 days, then $11.99 a month' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</div>

		<div class="split flip reveal">
			<div>
				<p class="tag">Totals</p>
				<h2>See what it all costs per month and per year.</h2>
				<p class="lede">Weekly, monthly, quarterly and yearly plans are converted to one monthly figure and split by category.</p>
				<ul class="ticks">
					<?php
					echo $rr_tick( 'Monthly and yearly totals', 'at the top of Home.' ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo $rr_tick( 'Spend by category:', 'streaming, music, software and more.' ); // phpcs:ignore WordPress.Security.EscapeOutput
					echo $rr_tick( 'With Pro:', 'the next 12 months of charges and a CSV export.' ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</ul>
				<p style="margin-top:22px"><a class="btn btn-ghost btn-sm" href="<?php echo esc_url( home_url( '/subscription-cost-calculator/' ) ); ?>">Try the free cost calculator &rarr;</a></p>
			</div>
			<div class="shot"><?php echo rr_device( 'app-stats.jpg', 'RenewRadar stats screen showing $50.56 a month and $606.76 a year, split by category' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</div>
	</div>
</section>

<section class="section" id="private">
	<div class="wrap">
		<div class="privacy reveal">
			<div>
				<p class="tag">Private by design</p>
				<h2>Your subscriptions are nobody else&rsquo;s business.</h2>
				<p>RenewRadar never asks for your bank login, your email or even your name. What you enter stays on your phone, and reminders are scheduled on your phone too.</p>
				<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( home_url( '/private-subscription-tracker/' ) ); ?>">How we keep it private &rarr;</a>
			</div>
			<ul class="zeros">
				<li><b>0</b><span>bank connections</span></li>
				<li><b>0</b><span>accounts to create</span></li>
				<li><b>0</b><span>ads or trackers</span></li>
				<li><b>1</b><span>one-time price for Pro</span></li>
			</ul>
		</div>
	</div>
</section>

<section class="section section-alt" id="pricing">
	<div class="wrap">
		<div class="section-head reveal">
			<p class="tag">Pricing</p>
			<h2>No subscription for your subscription tracker.</h2>
			<p>Start free. If you track more than five things, Pro is a single payment that never renews.</p>
		</div>
		<div class="plans reveal">
			<div class="plan">
				<h3>Free</h3>
				<p class="fine">Everything you need to start.</p>
				<p class="price">$0</p>
				<ul class="ticks">
					<li>Up to 5 active subscriptions and trials</li>
					<li>Reminders before every charge</li>
					<li>Free trial countdowns</li>
					<li>Monthly and yearly totals by category</li>
				</ul>
			</div>
			<div class="plan plan-pro">
				<span class="plan-badge">Best value. Pay once</span>
				<h3>Pro</h3>
				<p class="fine">Everything in Free, without the limit.</p>
				<?php if ( rr_sale_active() ) : ?>
				<p class="price"><?php echo esc_html( RR_SALE_PRICE ); ?> <small>one time, Christmas price until Dec 31 (was US$9.99)</small></p>
				<?php else : ?>
				<p class="price">US$9.99 <small>one time</small></p>
				<?php endif; ?>
				<ul class="ticks">
					<li>Unlimited subscriptions and trials</li>
					<li>Detailed reports for the next 12 months</li>
					<li>CSV export of your full list</li>
					<li>No renewal, ever</li>
				</ul>
			</div>
		</div>
		<p class="fine center" style="margin-top:22px;max-width:46em">Prices on Google Play are shown in your local currency and can differ by country. Cancelled items do not count toward the free limit. Pro is linked to your Google account and can be restored on a new phone.</p>
	</div>
</section>

<section class="section" id="faq">
	<div class="wrap">
		<div class="section-head reveal">
			<p class="tag">Questions</p>
			<h2>Frequently asked questions</h2>
		</div>
		<?php echo rr_faq_html( rr_home_faq() ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
</section>

<nav class="dots" aria-label="Page sections">
	<?php
	$rr_dots = array(
		'top'      => 'Top',
		'how'      => 'How it works',
		'reminders' => 'Reminders',
		'features' => 'Features',
		'private'  => 'Privacy',
		'pricing'  => 'Pricing',
		'faq'      => 'Questions',
	);
	if ( rr_sale_active() ) {
		$rr_dots = array_slice( $rr_dots, 0, 1, true ) + array( 'sale' => 'Christmas sale' ) + array_slice( $rr_dots, 1, null, true );
	}
	if ( get_theme_mod( 'rr_video_id', '' ) ) {
		$rr_dots = array_slice( $rr_dots, 0, 1, true ) + array( 'video' => 'Video' ) + array_slice( $rr_dots, 1, null, true );
	}
	foreach ( $rr_dots as $rr_dot_id => $rr_dot_label ) {
		printf( '<a href="#%1$s" aria-label="%2$s"><span>%2$s</span></a>', esc_attr( $rr_dot_id ), esc_html( $rr_dot_label ) );
	}
	?>
</nav>

<?php
echo rr_cta_band(); // phpcs:ignore WordPress.Security.EscapeOutput
get_footer();
