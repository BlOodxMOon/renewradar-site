import { article, crumbsSchema, ctaBand, pageHero } from '../layout.mjs';

const path = '/bobby-alternative-android/';
const crumbs = [
  ['Home', '/'],
  ['Bobby alternative for Android', null],
];

export default {
  path,
  nav: 'features',
  updated: '2026-11-14',
  title: 'Bobby Alternative for Android: Manual Subscription Tracker | RenewRadar',
  description:
    'Bobby is a popular manual subscription tracker for iPhone. RenewRadar brings the same simple, private approach to Android, with free trial countdowns and multi-currency support.',
  schema: [crumbsSchema(crumbs, path)],
  main: `
${pageHero({
  crumbs,
  tag: 'Compare',
  h1: 'Like Bobby, but for Android.',
  lede: 'Bobby made manual subscription tracking popular on the iPhone. If you are on Android, RenewRadar follows the same idea: add what you pay for by hand, see the totals, and get reminded in time.',
})}
${article({
  toc: [
    ['why', 'Why people look for Bobby on Android'],
    ['same', 'What RenewRadar has in common'],
    ['table', 'Side by side'],
    ['extra', 'What RenewRadar adds'],
  ],
  body: `
<h2 id="why">Why people look for Bobby on Android</h2>
<p>Bobby is a well-liked iOS app for keeping a manual list of subscriptions. It is available only for iPhone and iPad, so Android users, and people who switch from iPhone to Android, need something else.</p>

<h2 id="same">What RenewRadar has in common with Bobby</h2>
<ul>
  <li><strong>Manual entry.</strong> No bank connection; you add each subscription yourself.</li>
  <li><strong>Reminders before renewals</strong> on the device.</li>
  <li><strong>Totals</strong> so you can see what everything costs together.</li>
  <li><strong>Free to start, with a one-time upgrade</strong> rather than another subscription.</li>
</ul>

<h2 id="table">Side by side</h2>
<div class="table-wrap"><table>
  <thead><tr><th></th><th>Bobby</th><th>RenewRadar</th></tr></thead>
  <tbody>
    <tr><td>Platform</td><td>iPhone and iPad</td><td>Android</td></tr>
    <tr><td>Bank connection</td><td>No</td><td>No</td></tr>
    <tr><td>Account needed</td><td>No</td><td>No</td></tr>
    <tr><td>Free trial countdown section</td><td>No dedicated section</td><td>Yes, trials first with days left</td></tr>
    <tr><td>Per-subscription reminder timing</td><td>Yes</td><td>Yes: 14, 7, 3, 1 day or same day</td></tr>
    <tr><td>Multiple currencies</td><td>Yes, totals converted to a base currency</td><td>Yes, totals kept per currency without guessed rates</td></tr>
    <tr><td>Sync between devices</td><td>iCloud sync with the upgrade</td><td>No, your list stays on your phone</td></tr>
    <tr><td>Pricing</td><td>Free for 5; small one-time upgrade</td><td>Free for 5; Pro one-time US$9.99</td></tr>
  </tbody>
</table></div>
<p class="fine">Bobby details are based on its App Store listing and public reviews as of November 2026 and may change. Bobby is a trademark of its owner; RenewRadar is not affiliated with it.</p>

<h2 id="extra">What RenewRadar adds</h2>
<ul>
  <li><strong>Free trials come first.</strong> A dedicated Trials section shows the days left and the price after the trial, and reminders count down to the trial end.</li>
  <li><strong>Due this week and later this month.</strong> Home groups what is coming up next.</li>
  <li><strong>Personal, work or business.</strong> Tag each subscription by who it is for.</li>
  <li><strong>Pause, cancel and restore</strong> without losing the details.</li>
  <li><strong>Pro reports and CSV export</strong> for the next 12 months of charges.</li>
</ul>
<p>If you are moving from an iPhone, add your subscriptions from Bobby's list into RenewRadar with their next charge dates. It takes about a minute each.</p>
`,
  related: [
    ['/subscription-tracker/', 'Subscription tracker', 'Every subscription on one screen, sorted by what is charged next.'],
    ['/rocket-money-alternative/', 'Rocket Money alternative', 'How a manual tracker compares with a bank-linked one.'],
  ],
})}
${ctaBand()}
`,
};
