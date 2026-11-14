import { article, articleSchema, crumbsSchema, ctaBand, pageHero } from '../layout.mjs';

const path = '/guides/cancel-google-play-subscription/';
const title = 'How to Cancel a Subscription on Google Play';
const description =
  'Cancel any subscription billed through Google Play from your Android phone or the web, and learn what happens to access, refunds and data after you cancel.';
const crumbs = [
  ['Home', '/'],
  ['Guides', '/guides/'],
  ['Cancel on Google Play', null],
];

export default {
  path,
  nav: 'guides',
  updated: '2026-11-14',
  ogType: 'article',
  title: title + ' (Phone and Web) | RenewRadar',
  description,
  schema: [crumbsSchema(crumbs, path), articleSchema({ title, description, path, updated: '2026-11-14' })],
  main: `
${pageHero({
  crumbs,
  tag: 'Guide',
  h1: title,
  lede: 'Subscriptions and trials you started in an Android app are usually billed by Google Play. Here is how to cancel them in under a minute.',
  meta: 'Updated November 14, 2026 &middot; 4 minute read',
  cta: false,
})}
${article({
  toc: [
    ['phone', 'On an Android phone'],
    ['web', 'On the web'],
    ['after', 'What happens after you cancel'],
    ['missing', 'Cannot find the subscription?'],
    ['pause', 'Pause instead of cancel'],
  ],
  body: `
<h2 id="phone">Cancel on an Android phone or tablet</h2>
<ol>
  <li>Open the <strong>Google Play Store</strong> app.</li>
  <li>Make sure you are signed in to the Google account you subscribed with.</li>
  <li>Tap your <strong>profile picture</strong> in the top right corner.</li>
  <li>Tap <strong>Payments &amp; subscriptions</strong>, then <strong>Subscriptions</strong>.</li>
  <li>Select the subscription you want to cancel.</li>
  <li>Tap <strong>Cancel subscription</strong> and follow the instructions to confirm.</li>
</ol>
<div class="note"><p>Uninstalling the app does not cancel the subscription. You will keep being charged until you cancel it in Google Play.</p></div>

<h2 id="web">Cancel on the web</h2>
<ol>
  <li>Go to <strong>play.google.com</strong> and sign in with the same Google account.</li>
  <li>Open your account menu and choose <strong>Payments &amp; subscriptions</strong>.</li>
  <li>Open <strong>Subscriptions</strong>, choose the one to cancel and select <strong>Cancel subscription</strong>.</li>
</ol>
<p>The web version is useful when the subscription is on a phone you no longer have.</p>

<h2 id="after">What happens after you cancel</h2>
<ul>
  <li><strong>You keep access</strong> for the time you have already paid for, including the rest of a free trial.</li>
  <li><strong>You get an email</strong> from Google Play confirming the cancellation. Keep it until the end date has passed.</li>
  <li><strong>No automatic refund.</strong> Cancelling stops future charges. For a charge you did not want, use Google Play's refund request, or contact the app's developer.</li>
  <li><strong>The app's data stays with the service.</strong> Cancelling does not delete your account with that service. Delete it separately if you want to.</li>
</ul>

<h2 id="missing">Cannot find the subscription?</h2>
<p>If it is not listed in Google Play, it is billed somewhere else:</p>
<ul>
  <li><strong>Another Google account.</strong> Switch accounts in the Play Store and check again.</li>
  <li><strong>The service's website.</strong> If you signed up on the web, cancel in your account there.</li>
  <li><strong>Apple.</strong> If you subscribed on an iPhone, cancel in your Apple account settings.</li>
  <li><strong>A carrier or bundle.</strong> Some plans are billed through a phone or internet provider.</li>
</ul>
<p>Our guide to <a href="/guides/cancel-free-trial-before-charged/">cancelling a free trial</a> explains how to find out who bills you.</p>

<h2 id="pause">Pause instead of cancel</h2>
<p>Some apps let you <strong>pause</strong> a Google Play subscription for a while instead of cancelling. If the option is available, you will see it in the same Subscriptions screen. Billing restarts automatically when the pause ends, so set a reminder for that date. In RenewRadar, mark the item as paused and edit the next charge date when it resumes.</p>
`,
  related: [
    ['/guides/cancel-free-trial-before-charged/', 'How to cancel a free trial before you are charged', 'Google Play, the App Store and websites.'],
    ['/guides/subscription-audit/', 'The 20-minute subscription audit', 'Find every recurring charge and decide what stays.'],
  ],
})}
${ctaBand()}
`,
};
