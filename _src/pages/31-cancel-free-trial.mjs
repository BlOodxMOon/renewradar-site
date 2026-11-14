import { article, articleSchema, crumbsSchema, ctaBand, pageHero } from '../layout.mjs';

const path = '/guides/cancel-free-trial-before-charged/';
const title = 'How to Cancel a Free Trial Before You Are Charged';
const description =
  'Where to cancel a free trial on Google Play, the App Store and websites, how early to do it, and how to make sure you are never charged by surprise.';
const crumbs = [
  ['Home', '/'],
  ['Guides', '/guides/'],
  ['Cancel a free trial', null],
];

export default {
  path,
  nav: 'guides',
  updated: '2026-11-14',
  ogType: 'article',
  title: title + ' | RenewRadar',
  description,
  schema: [crumbsSchema(crumbs, path), articleSchema({ title, description, path, updated: '2026-11-14' })],
  main: `
${pageHero({
  crumbs,
  tag: 'Guide',
  h1: title,
  lede: 'Cancelling a free trial is usually quick. The tricky part is knowing who bills you and doing it in time. Here is how to get both right.',
  meta: 'Updated November 14, 2026 &middot; 6 minute read',
  cta: false,
})}
${article({
  toc: [
    ['who', '1. Find out who bills you'],
    ['play', '2. Trials on Google Play'],
    ['appstore', '3. Trials on the App Store'],
    ['web', '4. Trials on a website'],
    ['when', '5. When to cancel'],
    ['check', '6. Confirm it worked'],
  ],
  body: `
<h2 id="who">1. Find out who bills you</h2>
<p>The same service can bill you in different ways depending on where you signed up. This decides where you cancel:</p>
<ul>
  <li><strong>Started in an Android app</strong> and paid through Google Play: cancel in Google Play.</li>
  <li><strong>Started on an iPhone or iPad</strong> and paid through Apple: cancel in your Apple account settings.</li>
  <li><strong>Started on the service's website</strong> with a card or wallet: cancel in your account on that website.</li>
  <li><strong>Added through a phone carrier, bank or bundle:</strong> cancel with that provider.</li>
</ul>
<p>Not sure? Search your email for the sign-up confirmation. The sender (Google Play, Apple or the service itself) tells you who bills you.</p>
<div class="note"><p>Uninstalling an app does not cancel its subscription or trial. You have to cancel it where you are billed.</p></div>

<h2 id="play">2. Cancel a trial billed through Google Play</h2>
<ol>
  <li>Open the <strong>Google Play Store</strong> app.</li>
  <li>Tap your <strong>profile picture</strong> in the top right.</li>
  <li>Tap <strong>Payments &amp; subscriptions</strong>, then <strong>Subscriptions</strong>.</li>
  <li>Choose the trial and tap <strong>Cancel subscription</strong>.</li>
  <li>Follow the steps to confirm.</li>
</ol>
<p>Google Play sends a confirmation email. In most cases you keep access until the end of the trial. Our <a href="/guides/cancel-google-play-subscription/">Google Play guide</a> covers the web version and what happens next.</p>

<h2 id="appstore">3. Cancel a trial billed through the App Store</h2>
<ol>
  <li>Open <strong>Settings</strong> on your iPhone or iPad.</li>
  <li>Tap your <strong>name</strong> at the top, then <strong>Subscriptions</strong>.</li>
  <li>Choose the trial and tap <strong>Cancel Free Trial</strong> or <strong>Cancel Subscription</strong>.</li>
</ol>
<p>If you do not see the trial there, it is probably billed by the service directly or through Google Play.</p>

<h2 id="web">4. Cancel a trial on a website</h2>
<p>For services you signed up for on the web, cancellation is in your account on that website. Look for <strong>Account</strong>, <strong>Billing</strong>, <strong>Plan</strong> or <strong>Membership</strong>. A few things to watch for:</p>
<ul>
  <li><strong>Several steps.</strong> Some sites show offers before the final cancel button. Keep going until you see a confirmation.</li>
  <li><strong>Notice periods.</strong> Some plans must be cancelled a set number of days before the trial ends. Check the terms when you sign up.</li>
  <li><strong>Pause is not cancel.</strong> Pausing usually restarts billing automatically later.</li>
</ul>

<h2 id="when">5. When to cancel</h2>
<p>If you already know you will not keep it, cancel the same day you sign up. Many services, including subscriptions billed through Google Play, keep the trial running until its end date after you cancel.</p>
<p>If you want to decide later, set reminders for a few days before the end, not on the last day. Time zones and billing systems do not always charge at midnight in your time zone, so the last day can be too late.</p>
<p>RenewRadar does exactly this: add the trial with its end date and the price afterwards, and it reminds you before the trial ends. See <a href="/free-trial-reminder/">free trial reminders</a>.</p>

<h2 id="check">6. Confirm the cancellation worked</h2>
<ul>
  <li>Keep the <strong>confirmation email</strong> until the date has passed.</li>
  <li>Check the subscription list where you cancelled. It should show an end date instead of a renewal date.</li>
  <li>Look at your statement after the trial end date. If you were charged anyway, contact the service or the store with your confirmation. Google Play and Apple both have refund request pages.</li>
</ul>
`,
  related: [
    ['/guides/cancel-google-play-subscription/', 'How to cancel a subscription on Google Play', 'On your phone or on the web, plus what happens after.'],
    ['/free-trial-reminder/', 'Free trial reminders', 'Get a countdown and a reminder for every trial.'],
  ],
})}
${ctaBand('Never miss a trial end date again.')}
`,
};
