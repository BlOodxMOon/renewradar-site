import { article, articleSchema, crumbsSchema, ctaBand, pageHero } from '../layout.mjs';

const path = '/guides/subscription-audit/';
const title = 'The 20-Minute Subscription Audit';
const description =
  'A simple routine to find every subscription you pay for, decide what stays, cancel the rest and keep it that way, in about 20 minutes.';
const crumbs = [
  ['Home', '/'],
  ['Guides', '/guides/'],
  ['Subscription audit', null],
];

export default {
  path,
  nav: 'guides',
  updated: '2026-11-14',
  ogType: 'article',
  title: title + ': Find and Cut Recurring Charges | RenewRadar',
  description,
  schema: [crumbsSchema(crumbs, path), articleSchema({ title, description, path, updated: '2026-11-14' })],
  main: `
${pageHero({
  crumbs,
  tag: 'Guide',
  h1: title,
  lede: 'Set a timer, grab your phone and your last two bank or card statements. By the end you will know exactly what you pay for, and you will probably pay for less.',
  meta: 'Updated November 14, 2026 &middot; 7 minute read',
  cta: false,
})}
${article({
  toc: [
    ['find', 'Step 1: Find everything (8 min)'],
    ['list', 'Step 2: Write it down (5 min)'],
    ['decide', 'Step 3: Keep, downgrade or cancel (5 min)'],
    ['act', 'Step 4: Act on it (2 min)'],
    ['keep', 'Keep it that way'],
  ],
  body: `
<h2 id="find">Step 1: Find everything (8 minutes)</h2>
<p>Subscriptions hide in several places. Check each of these:</p>
<ul>
  <li><strong>Bank and card statements</strong> for the last two months. Look for the same merchant appearing every month, and for small amounts you do not recognize.</li>
  <li><strong>Google Play:</strong> Play Store, profile picture, Payments &amp; subscriptions, Subscriptions.</li>
  <li><strong>Apple:</strong> Settings, your name, Subscriptions, if you have an iPhone or iPad.</li>
  <li><strong>Your email:</strong> search for <em>receipt</em>, <em>renewal</em>, <em>subscription</em> and <em>trial</em>.</li>
  <li><strong>PayPal or other wallets:</strong> look for automatic payments or approved merchants.</li>
  <li><strong>Phone and internet bills:</strong> bundles and add-ons are often charged here.</li>
</ul>
<p>Two months of statements matter because quarterly and yearly plans will not show up in a single month.</p>

<h2 id="list">Step 2: Write it down in one place (5 minutes)</h2>
<p>For each subscription, note the name, the price, how often it is charged and the next charge date. Converting everything to a monthly figure makes them comparable: a yearly price divided by 12, a weekly price times 52 divided by 12.</p>
<p>You can do this on paper, in a spreadsheet, in our <a href="/subscription-cost-calculator/">subscription cost calculator</a>, or directly in RenewRadar, which also reminds you before each charge.</p>

<h2 id="decide">Step 3: Keep, downgrade or cancel (5 minutes)</h2>
<p>Go down the list and give each item one label:</p>
<div class="table-wrap"><table>
  <thead><tr><th>Label</th><th>When to use it</th></tr></thead>
  <tbody>
    <tr><td>Keep</td><td>You used it in the last two weeks and would sign up again today at this price.</td></tr>
    <tr><td>Downgrade</td><td>You use it, but a cheaper tier, a family plan, an ad-supported plan or yearly billing would do.</td></tr>
    <tr><td>Rotate</td><td>Streaming services you watch in bursts. Keep one at a time and switch every month or two.</td></tr>
    <tr><td>Cancel</td><td>You have not used it in a month, or you pay for something similar elsewhere.</td></tr>
  </tbody>
</table></div>
<p>Watch for duplicates: two cloud storage plans, music included in a bundle you already pay for, or both a personal and a family plan.</p>

<h2 id="act">Step 4: Act on it right away (2 minutes)</h2>
<p>Cancel the items marked Cancel now, while you are motivated. Most services let you keep access until the end of the period you already paid for, so there is nothing to gain by waiting.</p>
<p>See <a href="/guides/cancel-google-play-subscription/">how to cancel on Google Play</a> and <a href="/guides/cancel-free-trial-before-charged/">how to find who bills you</a>.</p>

<h2 id="keep">Keep it that way</h2>
<p>An audit works once. Staying on top of subscriptions needs two habits:</p>
<ol>
  <li><strong>Log every new subscription and free trial the day you sign up</strong>, with its end date or next charge date.</li>
  <li><strong>Get a reminder before every charge</strong>, so each renewal becomes a small decision instead of a surprise.</li>
</ol>
<p>That is what RenewRadar is for. It is free for up to five subscriptions, has no account and never connects to your bank.</p>
`,
  related: [
    ['/subscription-cost-calculator/', 'Subscription cost calculator', 'See the monthly, yearly and five-year total.'],
    ['/subscription-tracker/', 'Subscription tracker', 'Every subscription on one screen, sorted by what is charged next.'],
  ],
})}
${ctaBand()}
`,
};
