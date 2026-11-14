import { article, crumbsHtml, crumbsSchema, ctaBand, faq, play } from '../layout.mjs';

const path = '/subscription-cost-calculator/';
const crumbs = [
  ['Home', '/'],
  ['Subscription cost calculator', null],
];

const chips = [
  ['Video streaming', 'monthly'],
  ['Music', 'monthly'],
  ['Cloud storage', 'monthly'],
  ['AI assistant', 'monthly'],
  ['Gym', 'monthly'],
  ['News', 'monthly'],
  ['Gaming', 'monthly'],
  ['Software', 'yearly'],
  ['VPN', 'yearly'],
];

const currencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'PHP', 'INR', 'JPY', 'SGD', 'NZD', 'ZAR', 'MXN', 'BRL'];

const questions = faq([
  [
    'How is the monthly cost calculated?',
    'Every price is converted to a monthly figure. Monthly prices stay the same, yearly prices are divided by 12, quarterly prices by 3, and weekly prices are multiplied by 52 and divided by 12, because a month is slightly longer than four weeks.',
  ],
  [
    'Is my data saved or sent anywhere?',
    'No. The calculator runs entirely in your browser. Nothing you type is stored or sent to a server, and it is gone when you close the page.',
  ],
  [
    'Why is the five-year figure so high?',
    'Because subscriptions repeat. A $12 monthly plan looks small, but it is $720 over five years. The five-year number is simply the monthly total times 60, before any price increases.',
  ],
  [
    'Can I mix currencies?',
    'The calculator uses one currency for all rows. If you pay in several currencies, run it once per currency, or use RenewRadar, which keeps a separate total for each currency.',
  ],
]);

export default {
  path,
  nav: 'calculator',
  updated: '2026-11-14',
  title: 'Subscription Cost Calculator: Monthly, Yearly and 5-Year Cost | RenewRadar',
  description:
    'Free subscription cost calculator. Add your streaming, music, software and other plans to see what they cost per month, per year and over five years.',
  scripts: ['/assets/calc.js'],
  schema: [
    crumbsSchema(crumbs, path),
    {
      '@context': 'https://schema.org',
      '@type': 'WebApplication',
      name: 'Subscription cost calculator',
      url: 'https://renewradar.pixeldevcode.com' + path,
      applicationCategory: 'FinanceApplication',
      operatingSystem: 'Any',
      offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
    },
    questions.schema,
  ],
  main: `
<header class="page-hero"><div class="wrap">
  ${crumbsHtml(crumbs)}
  <p class="tag">Free tool</p>
  <h1>What do your subscriptions really cost?</h1>
  <p class="lede">Add what you pay for, in any billing cycle. The calculator shows the true monthly, yearly and five-year cost. Nothing you type leaves your browser.</p>
</div></header>

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
        <select id="currency" style="width:auto">${currencies.map((c) => `<option>${c}</option>`).join('')}</select>
      </div>
      <div class="chips" aria-label="Quick add">${chips
        .map(([n, c]) => `<button type="button" class="chip" data-add="${n}" data-cycle="${c}">+ ${n}</button>`)
        .join('')}</div>
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
      ${play()}
    </aside>
  </form>
</div>

${article({
  toc: null,
  body: `
<h2>How to use the result</h2>
<p>The yearly figure is the one to look at. Most people underestimate it, because each subscription is charged separately and never shows up as one number. Once you see it, ask three questions about each line:</p>
<ol>
  <li><strong>Did I use it in the last month?</strong> If not, it is a candidate to cancel or pause.</li>
  <li><strong>Am I paying for the same thing twice?</strong> Two music services, two cloud storage plans, or a family plan and a personal one.</li>
  <li><strong>Is there a cheaper way to pay?</strong> Yearly billing, a family plan, or a bundle often costs less than monthly plans on their own.</li>
</ol>
<p>Our <a href="/guides/subscription-audit/">20-minute subscription audit</a> walks through this step by step.</p>

<h2>Frequently asked questions</h2>
${questions.html}
`,
})}
${ctaBand('Turn the number into reminders.', 'RenewRadar tracks each subscription and warns you before every charge. Free for up to five.')}
`,
};
