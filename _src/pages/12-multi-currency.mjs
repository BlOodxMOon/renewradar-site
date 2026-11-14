import { article, crumbsSchema, ctaBand, pageHero } from '../layout.mjs';

const path = '/multi-currency-subscription-tracker/';
const crumbs = [
  ['Home', '/'],
  ['Multi-currency tracking', null],
];

export default {
  path,
  nav: 'features',
  updated: '2026-11-14',
  title: 'Multi-Currency Subscription Tracker for Android | RenewRadar',
  description:
    'Track subscriptions billed in different currencies. RenewRadar records each one in its own currency and shows honest totals per currency, without guessed exchange rates.',
  schema: [crumbsSchema(crumbs, path)],
  main: `
${pageHero({
  crumbs,
  tag: 'Multi-currency',
  h1: 'Subscriptions in more than one currency, tracked honestly.',
  lede: 'A streaming plan in pesos, cloud storage in dollars and a newspaper in euros. RenewRadar keeps each one in the currency you are actually billed in.',
})}
${article({
  toc: [
    ['who', 'Who needs this'],
    ['how', 'How currencies work in RenewRadar'],
    ['totals', 'Why totals stay per currency'],
    ['tips', 'Tips for mixed currencies'],
  ],
  body: `
<h2 id="who">Who needs a multi-currency tracker</h2>
<p>More people pay in several currencies than you might think:</p>
<ul>
  <li>People who moved abroad and still pay for services at home.</li>
  <li>Freelancers and remote workers who pay for tools in US dollars while earning in another currency.</li>
  <li>Anyone whose app store charges in the local currency while websites bill in dollars or euros.</li>
  <li>Families who share plans across countries.</li>
</ul>
<p>Most trackers assume a single currency, or convert everything with a rate that may not match what your bank actually charged.</p>

<h2 id="how">How currencies work in RenewRadar</h2>
<p>Every subscription has its own price and its own currency. When you add one, USD, EUR, GBP, CAD, AUD, PHP, INR and JPY are one tap away, and you can type any other three-letter currency code, such as SGD, MXN or BRL.</p>
<p>You also choose a <strong>default currency</strong> in Settings, which is filled in for new subscriptions so you do not have to pick it every time.</p>
<p>Prices are stored exactly as you enter them. Reminders show the amount in the original currency, so the number in the notification matches the number on your statement.</p>

<h2 id="totals">Why totals stay per currency</h2>
<p>Exchange rates move every day, and banks and card networks add their own margins. A converted total can look precise and still be wrong by several percent.</p>
<p>So when your subscriptions use more than one currency, RenewRadar shows a <strong>separate monthly and yearly total for each currency</strong> instead of guessing a rate. Spend by category on the Stats screen works the same way. You always see real amounts you will actually be charged.</p>
<div class="note"><p>If all your subscriptions use one currency, you simply get one monthly and one yearly total.</p></div>

<h2 id="tips">Tips for tracking mixed currencies</h2>
<ul>
  <li><strong>Use the currency on the receipt</strong>, not the one your bank converts it into. That is the amount that will be charged again.</li>
  <li><strong>Check card fees.</strong> Some cards add a foreign transaction fee to every charge in another currency, which can make a local plan cheaper.</li>
  <li><strong>Watch app store pricing.</strong> Google Play and the App Store charge in your local currency, often at different prices from the service's website.</li>
  <li><strong>Review yearly plans.</strong> A large yearly charge in another currency is exposed to a whole year of exchange rate changes.</li>
</ul>
`,
  related: [
    ['/subscription-cost-calculator/', 'Subscription cost calculator', 'See monthly, yearly and five-year costs in your currency.'],
    ['/subscription-tracker/', 'Subscription tracker', 'Every subscription on one screen, sorted by what is charged next.'],
  ],
})}
${ctaBand()}
`,
};
