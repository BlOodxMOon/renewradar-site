<?php
/**
 * The About and Contact pages. Search engines choose the extra links under a site's main result (sitelinks)
 * from clear, stable pages like these, and visitors look for them. Created once by migration 7; edit them in
 * wp-admin afterwards (the importer never overwrites an existing page).
 */

defined( 'ABSPATH' ) || exit;

function rr_extra_pages() {
	$play = 'https://play.google.com/store/apps/details?id=com.pixeldevcode.renewradar';

	$about = '<h2 id="what">What RenewRadar does</h2>'
		. '<p>You add each subscription and free trial by hand, with its price, billing cycle and next renewal date. RenewRadar shows what is due next, adds up your monthly and yearly cost, counts down your free trials, and reminds you before every charge so you can cancel in time.</p>'
		. '<h2 id="why">Why it exists</h2>'
		. '<p>Subscriptions and free trials renew quietly, and a few forgotten ones add up over a year. RenewRadar is built to answer one question: what will I be charged for next, and when?</p>'
		. '<h2 id="private">Private by design</h2>'
		. '<p>There is no account to create and no bank connection. Your list is stored on your phone and is never sent to us. The app makes only two kinds of network request: a weekly download of the public list of service names, which carries no data about you, and a purchase check that uses an anonymous ID and your purchase status. The <a href="/privacy.html">privacy policy</a> explains each one.</p>'
		. '<h2 id="pricing">Free and Pro</h2>'
		. '<p>You can track up to five active subscriptions and free trials for free, with reminders, trial countdowns and totals. Pro is a one-time purchase through Google Play, with no subscription, and it removes the limit and adds detailed reports and CSV export.</p>'
		. '<h2 id="who">Who makes it</h2>'
		. '<p>RenewRadar is made and published by <a href="https://pixeldevcode.com">Pixel DevCode</a>. It is available for Android on <a href="' . $play . '">Google Play</a>. Questions and ideas are welcome on the <a href="/contact/">contact page</a>.</p>';

	$contact = '<h2 id="email">Email</h2>'
		. '<p>Write to <a href="mailto:renewradar@pixeldevcode.com">renewradar@pixeldevcode.com</a> for help with the app, bug reports, questions about Pro, or ideas for new features.</p>'
		. '<h2 id="include">What to include</h2>'
		. '<p>For a problem, tell us what you expected and what happened, your phone model and Android version, and the app version if you know it. Please do not send passwords, card numbers or bank details. We never need them.</p>'
		. '<h2 id="reminders">Reminders not arriving?</h2>'
		. '<p>Check that notifications are allowed for RenewRadar in your phone settings. Some phones stop background apps to save battery, which can delay reminders: set RenewRadar\'s battery use to unrestricted (the wording differs by phone brand), then open the app once.</p>'
		. '<h2 id="purchases">Purchases and refunds</h2>'
		. '<p>Pro is bought through Google Play and tied to your Google account. After reinstalling or changing phones, use Restore purchase in the app\'s Settings. Refunds follow <a href="https://support.google.com/googleplay/answer/2479637">Google Play\'s refund policy</a>.</p>'
		. '<h2 id="data">Your data</h2>'
		. '<p>Your subscription list stays on your phone, so we cannot see it or recover it for you. You can export it from the app. See the <a href="/privacy.html">privacy policy</a> for details.</p>';

	return array(
		array(
			'type'        => 'page',
			'slug'        => 'about',
			'template'    => '',
			'legal'       => false,
			'title'       => 'About RenewRadar',
			'seoTitle'    => 'About RenewRadar: A Private Subscription Tracker',
			'description' => 'RenewRadar is a private subscription and free trial tracker for Android by Pixel DevCode. No account, no bank login, and your data stays on your phone.',
			'tag'         => 'About',
			'lede'        => 'A private subscription and free trial tracker for Android, made by Pixel DevCode.',
			'content'     => $about,
			'related'     => array(
				array( 'href' => '/subscription-tracker/', 'title' => 'Subscription tracker', 'text' => 'Every renewal and trial on one screen.' ),
				array( 'href' => '/guides/', 'title' => 'Guides', 'text' => 'Cancel trials in time and audit your spending.' ),
			),
		),
		array(
			'type'        => 'page',
			'slug'        => 'contact',
			'template'    => '',
			'legal'       => false,
			'title'       => 'Contact RenewRadar',
			'seoTitle'    => 'Contact and Support | RenewRadar',
			'description' => 'Contact RenewRadar support by email for help with the app, bug reports, Pro purchase questions and feature ideas.',
			'tag'         => 'Contact',
			'lede'        => 'Questions, bug reports and feature ideas are welcome.',
			'content'     => $contact,
			'related'     => array(
				array( 'href' => '/privacy.html', 'title' => 'Privacy policy', 'text' => 'Exactly what the app stores and the two network requests it makes.' ),
				array( 'href' => '/about/', 'title' => 'About RenewRadar', 'text' => 'What the app does and who makes it.' ),
			),
		),
	);
}

/* 7: create the About and Contact pages. rr_import_item skips a page that already exists. */
function rr_migrate_7() {
	$assets = content_url( '/themes/renewradar/assets' );
	foreach ( rr_extra_pages() as $item ) {
		rr_import_item( $item, $assets, 0 );
	}
}
