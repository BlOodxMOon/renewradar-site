import { article, crumbsSchema, ctaBand, pageHero } from '../layout.mjs';

const path = '/private-subscription-tracker/';
const crumbs = [
  ['Home', '/'],
  ['Private by design', null],
];

export default {
  path,
  nav: 'features',
  updated: '2026-11-14',
  title: 'Private Subscription Tracker: No Bank Login, No Account | RenewRadar',
  description:
    'A subscription tracker that never asks for your bank login, email or name. RenewRadar stores your list on your phone and works offline.',
  schema: [crumbsSchema(crumbs, path)],
  main: `
${pageHero({
  crumbs,
  tag: 'Private by design',
  h1: 'A subscription tracker that does not need your bank login.',
  lede: 'Many money apps ask you to connect your bank so they can find subscriptions for you. RenewRadar takes the opposite approach: you tell it what you pay for, and nothing leaves your phone.',
})}
${article({
  toc: [
    ['tradeoff', 'Bank-linked versus manual'],
    ['stays', 'What stays on your phone'],
    ['network', 'The only network requests'],
    ['not', 'What RenewRadar does not do'],
    ['backup', 'Keeping a copy'],
  ],
  body: `
<h2 id="tradeoff">Bank-linked trackers versus manual trackers</h2>
<p>Bank-linked apps can find subscriptions automatically by reading your transactions. In exchange, a third party gets ongoing access to your financial history, and the app usually only works with banks in certain countries.</p>
<p>A manual tracker asks for about a minute per subscription. In exchange, nobody else sees your spending, it works in any country and with any card or wallet, and it keeps working without internet.</p>
<div class="table-wrap"><table>
  <thead><tr><th></th><th>Bank-linked tracker</th><th>RenewRadar</th></tr></thead>
  <tbody>
    <tr><td>Setup</td><td>Connect bank accounts</td><td>Add subscriptions by hand</td></tr>
    <tr><td>Account needed</td><td>Usually</td><td>No</td></tr>
    <tr><td>Who sees your transactions</td><td>The app and its data partners</td><td>Nobody</td></tr>
    <tr><td>Countries</td><td>Limited to supported banks</td><td>Anywhere</td></tr>
    <tr><td>Works offline</td><td>No</td><td>Yes</td></tr>
  </tbody>
</table></div>

<h2 id="stays">What stays on your phone</h2>
<p>Everything you enter is stored in the app's private storage on your phone: names, prices, currencies, billing cycles, dates, categories, notes and reminder settings. Reminders are local notifications created on the phone itself, not sent from a server.</p>
<p>There is no RenewRadar account, so there is no profile, email address or password to leak.</p>

<h2 id="network">The only network requests the app makes</h2>
<p>RenewRadar makes two kinds of requests, and neither contains your subscriptions:</p>
<ul>
  <li><strong>A weekly service list.</strong> A plain file of popular service names, categories and colors that powers the suggestions when you type a name.</li>
  <li><strong>Purchase checks.</strong> If you buy Pro, Google Play processes the payment, and RevenueCat confirms the purchase using a random anonymous ID.</li>
</ul>
<p>The details are in the <a href="/privacy.html">privacy policy</a>.</p>

<h2 id="not">What RenewRadar does not do</h2>
<ul>
  <li>No bank or card connections.</li>
  <li>No accounts, emails or phone numbers.</li>
  <li>No ads, advertising IDs or analytics.</li>
  <li>No selling or sharing of data, because there is none to share.</li>
</ul>

<h2 id="backup">Keeping a copy of your list</h2>
<p>Because nothing is stored on a server, uninstalling the app removes your list. Pro users can export the full list as a CSV file at any time and save it wherever they like, through the share sheet. The file goes only where you send it.</p>
`,
  related: [
    ['/rocket-money-alternative/', 'A private Rocket Money alternative', 'How a manual tracker compares with a bank-linked one.'],
    ['/privacy.html', 'Privacy policy', 'Exactly what the app stores and the two network requests it makes.'],
  ],
})}
${ctaBand('Private, offline, and yours.')}
`,
};
