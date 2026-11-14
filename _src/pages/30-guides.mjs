import { ICONS, crumbsHtml, crumbsSchema, ctaBand } from '../layout.mjs';

const path = '/guides/';
const crumbs = [
  ['Home', '/'],
  ['Guides', null],
];

const guides = [
  [
    '/guides/cancel-free-trial-before-charged/',
    ICONS.clock,
    'How to cancel a free trial before you are charged',
    'Find out who bills you, where to cancel on Google Play, the App Store and websites, and how early to do it.',
  ],
  [
    '/guides/cancel-google-play-subscription/',
    ICONS.swap,
    'How to cancel a subscription on Google Play',
    'Step by step on an Android phone and on the web, plus what happens after you cancel.',
  ],
  [
    '/guides/subscription-audit/',
    ICONS.book,
    'The 20-minute subscription audit',
    'Find every recurring charge, decide what stays, and stop paying for the rest.',
  ],
  [
    '/subscription-cost-calculator/',
    ICONS.calc,
    'Subscription cost calculator',
    'A free tool that shows what your subscriptions cost per month, per year and over five years.',
  ],
];

export default {
  path,
  nav: 'guides',
  updated: '2026-11-14',
  title: 'Subscription Guides: Cancel Trials, Audit Your Spending | RenewRadar',
  description:
    'Practical guides to cancelling free trials in time, cancelling Google Play subscriptions and auditing what you pay for every month.',
  schema: [crumbsSchema(crumbs, path)],
  main: `
<header class="page-hero"><div class="wrap">
  ${crumbsHtml(crumbs)}
  <p class="tag">Guides</p>
  <h1>Pay only for what you actually use.</h1>
  <p class="lede">Short, practical guides for keeping subscriptions under control, written by the team behind RenewRadar.</p>
</div></header>
<section class="section"><div class="wrap">
  <div class="grid-2">${guides
    .map(
      ([href, icon, title, text]) =>
        `<a class="card" href="${href}"><div class="ic">${icon}</div><h3>${title}</h3><p>${text}</p><span class="go">Read &rarr;</span></a>`
    )
    .join('')}</div>
</div></section>
${ctaBand()}
`,
};
