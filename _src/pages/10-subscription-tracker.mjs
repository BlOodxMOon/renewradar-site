import { article, crumbsSchema, ctaBand, device, pageHero } from '../layout.mjs';

const path = '/subscription-tracker/';
const crumbs = [
  ['Home', '/'],
  ['Subscription tracker', null],
];

export default {
  path,
  nav: 'features',
  updated: '2026-11-14',
  title: 'Subscription Tracker App for Android | RenewRadar',
  description:
    'RenewRadar is a subscription tracker for Android. List what you pay for, see monthly and yearly totals, and get a reminder before every renewal. No bank login needed.',
  schema: [crumbsSchema(crumbs, path)],
  main: `
${pageHero({
  crumbs,
  tag: 'Subscription tracker',
  h1: 'A subscription tracker that shows every renewal before it happens.',
  lede: 'Streaming, music, cloud storage, software, gym, news. RenewRadar puts all of it on one screen, sorted by what is charged next, with a reminder before each payment.',
})}
${article({
  toc: [
    ['why', 'Why track subscriptions'],
    ['add', 'Adding a subscription'],
    ['home', 'One screen, sorted by date'],
    ['totals', 'Monthly and yearly totals'],
    ['manage', 'Pause, cancel and restore'],
    ['plans', 'Free and Pro'],
  ],
  body: `
<h2 id="why">Why track subscriptions at all</h2>
<p>Subscriptions are designed to be forgotten. Each one is small, the charge arrives on a different day of the month, and the receipt goes to an inbox nobody reads. Over a year, a handful of $5 to $15 plans quietly adds up to hundreds.</p>
<p>A subscription tracker fixes the two things that make this happen: you cannot see everything in one place, and nothing warns you before a charge. RenewRadar does both, without asking for access to your bank or cards.</p>

<h2 id="add">Adding a subscription takes about a minute</h2>
${device('app-home.jpg', 'RenewRadar home screen with a Spotify trial and four subscriptions sorted by renewal date', 'inline-shot')}
<p>Start typing a name and RenewRadar suggests popular services, with a category and a usual billing cycle filled in. Then enter:</p>
<ul>
  <li><strong>Price and currency.</strong> Any currency with a three-letter code, such as USD, EUR, GBP or PHP.</li>
  <li><strong>Billing cycle.</strong> Weekly, monthly, quarterly, yearly, or a custom number of days.</li>
  <li><strong>Next charge date.</strong> After it passes, RenewRadar moves it forward by one cycle on its own.</li>
  <li><strong>Category and who it is for.</strong> Personal, work or business, so you can tell them apart later.</li>
  <li><strong>Reminders.</strong> 14, 7, 3 or 1 day before, or on the day. Pick as many as you like.</li>
</ul>
<p>If the subscription starts with a free trial, turn on the trial switch and add the trial end date and the price after the trial. Trials then get their own countdown. See <a href="/free-trial-reminder/">free trial reminders</a>.</p>

<h2 id="home">One screen, sorted by what is charged next</h2>
<p>Home is split into a few short sections so you can read it at a glance:</p>
<ul>
  <li><strong>Trials</strong> come first, sorted by end date, with the days left.</li>
  <li><strong>Due this week</strong> holds anything charged in the next seven days.</li>
  <li><strong>Later this month</strong> holds the rest of the current month.</li>
  <li><strong>Subscriptions</strong> lists everything else in renewal order.</li>
</ul>
<p>Each row shows the service, the price and how many days are left, with a color that turns warmer as the date gets close.</p>

<h2 id="totals">Monthly and yearly totals that add up</h2>
<p>Comparing a weekly plan with a yearly one is hard in your head. RenewRadar converts every billing cycle to a monthly figure (a yearly price divided by 12, a weekly price times 52 divided by 12) and shows a monthly and yearly total at the top of Home.</p>
<p>The Stats screen breaks that down by category, so you can see that streaming is half of your spend before you decide what to cut. Free trials are not counted until they turn into a paid plan, and paused or cancelled items are left out.</p>
<p>Want a quick number before installing anything? Try the <a href="/subscription-cost-calculator/">subscription cost calculator</a>.</p>

<h2 id="manage">Pause, cancel and restore</h2>
<p>Real subscriptions are messy. You pause a gym for the summer, cancel a streaming service and sign up again for one show, or switch a plan from monthly to yearly. RenewRadar handles each case:</p>
<ul>
  <li><strong>Pause</strong> keeps the item but stops reminders and leaves it out of totals.</li>
  <li><strong>Mark as cancelled</strong> moves it to a Cancelled list at the bottom of Home.</li>
  <li><strong>Restore</strong> brings a cancelled item back and moves a past renewal date forward.</li>
  <li><strong>Edit</strong> any detail. Reminders are rescheduled every time you save.</li>
</ul>

<h2 id="plans">Free and Pro</h2>
<p>The free version tracks up to five active subscriptions and trials, with reminders, countdowns and totals. Cancelled items do not count toward the five.</p>
<p>Pro is a single one-time purchase (US$9.99 in the United States, shown in your currency on Google Play). It removes the limit and adds detailed reports for the next 12 months, your biggest costs, and a CSV export of your full list. It never renews.</p>
`,
  related: [
    ['/free-trial-reminder/', 'Free trial reminders', 'Catch every trial before it turns into a paid plan.'],
    ['/guides/subscription-audit/', 'The 20-minute subscription audit', 'Find every recurring charge and decide what stays.'],
  ],
})}
${ctaBand()}
`,
};
