import { ICONS, PLAY, SITE, ctaBand, device, faq, play } from "../layout.mjs";
import { winterScene } from "../winter.mjs";

const tick = (b, t) => `<li><b>${b}</b> ${t}</li>`;

const questions = faq([
  [
    'Is RenewRadar free?',
    'Yes. You can track up to five active subscriptions and free trials for free, with reminders, trial countdowns and totals. Pro removes the limit and adds detailed reports and CSV export for a single one-time payment. Cancelled items do not count toward the free limit.',
  ],
  [
    'Does it connect to my bank?',
    'No. RenewRadar never asks for bank logins or card details. You add each subscription yourself, which takes about a minute, and nothing is read from your accounts.',
  ],
  [
    'Do I need to create an account?',
    'No. There is no sign-up, no email and no password. Your list is stored only on your phone.',
  ],
  [
    'Can RenewRadar cancel a subscription for me?',
    'No. It tells you in time, and you cancel with the service itself. Our <a href="/guides/cancel-free-trial-before-charged/">guide to cancelling a free trial</a> shows where to do it for the most common billing methods.',
  ],
  [
    'Will reminders arrive if I do not open the app?',
    'Yes. Reminders are scheduled as notifications on your phone, so they work without the app open and without internet. Some Android phones with strict battery saving can delay them; allowing notifications and setting RenewRadar to unrestricted battery use fixes that.',
  ],
  [
    'Which currencies does it support?',
    'Any currency with a three-letter code. USD, EUR, GBP, CAD, AUD, PHP, INR and JPY are one tap away. When you track more than one currency, totals are shown per currency, because RenewRadar does not guess exchange rates.',
  ],
  [
    'Is there an iPhone version?',
    'Not yet. RenewRadar is available for Android on Google Play first.',
  ],
  [
    'What happens to my list if I change phones?',
    'Your list stays on the old phone because nothing is synced to a server. Pro users can export a CSV copy at any time. Pro itself is linked to your Google account, so Restore purchase brings it back on a new phone.',
  ],
]);

const schema = [
  {
    '@context': 'https://schema.org',
    '@type': 'MobileApplication',
    name: 'RenewRadar: Subscriptions',
    operatingSystem: 'ANDROID',
    applicationCategory: 'FinanceApplication',
    description:
      'A private subscription and free trial tracker. Add subscriptions by hand, see monthly and yearly totals, and get reminders before every charge.',
    url: SITE + '/',
    downloadUrl: PLAY,
    installUrl: PLAY,
    image: SITE + '/assets/img/icon.png',
    offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
    publisher: { '@type': 'Organization', name: 'Pixel DevCode', url: 'https://pixeldevcode.com' },
  },
  {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: 'RenewRadar',
    url: SITE + '/',
    publisher: { '@type': 'Organization', name: 'Pixel DevCode' },
  },
  {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: 'Pixel DevCode',
    url: 'https://pixeldevcode.com',
    logo: SITE + '/assets/img/icon.png',
    email: 'renewradar@pixeldevcode.com',
  },
  questions.schema,
];

export default {
  path: '/',
  file: 'index.html',
  updated: '2026-11-14',
  title: 'RenewRadar: Subscription and Free Trial Tracker for Android',
  ogTitle: 'RenewRadar: know before you are charged',
  description:
    'Track every subscription and free trial on one screen and get reminded before each charge. No bank login, no account. Free for five subscriptions on Android.',
  schema,
  main: `
<section class="hero">
  ${winterScene}
  <div class="wrap">
    <a class="eyebrow rise" style="--i:0" href="${PLAY}"><span class="badge">Now on Google Play</span>Subscription and free trial tracker for Android<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
    <h1 class="rise" style="--i:1">Know <span class="mark">before</span> you are charged.</h1>
    <p class="lede rise" style="--i:2">Every subscription and free trial on one screen, with a reminder before each charge. No bank login, no account, and your list never leaves your phone.</p>
    <div class="hero-cta rise" style="--i:3">${play()}<a class="btn btn-primary" href="#how">How it works</a></div>
    <ul class="facts rise" style="--i:4">
      <li>${ICONS.check}Free for 5 subscriptions</li>
      <li>${ICONS.check}One-time Pro, no subscription</li>
      <li>${ICONS.check}Works offline</li>
    </ul>
    <div class="rise-phone rise" style="--i:3">
      <div class="frame"><div><img src="/assets/img/app-home.jpg" alt="RenewRadar home screen with monthly and yearly totals, a Spotify trial with 2 days left and subscriptions sorted by renewal date" width="922" height="2048" fetchpriority="high"></div></div>
    </div>
  </div>
</section>

<section class="section" id="how">
  <div class="wrap">
    <div class="section-head reveal">
      <p class="tag">How it works</p>
      <h2>Three steps, about a minute per subscription.</h2>
    </div>
    <div class="grid-3 reveal">
      <div class="card"><div class="dot">1</div><h3>Add it</h3><p>Type a name and pick from suggestions for popular services. Set the price, currency, billing cycle and next charge date.</p></div>
      <div class="card"><div class="dot">2</div><h3>Choose your reminders</h3><p>14, 7, 3 or 1 day before, or on the day. Every subscription can have its own timing.</p></div>
      <div class="card"><div class="dot">3</div><h3>Decide in time</h3><p>Keep it, pause it or mark it cancelled. Renewal dates roll forward on their own.</p></div>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="wrap">
    <div class="split reveal">
      <div>
        <p class="tag">Free trials</p>
        <h2>Free trials get their own countdown.</h2>
        <p class="lede">Most surprise charges start as a free trial you forgot about. RenewRadar puts every trial at the top, with the days left and the price that starts afterwards.</p>
        <ul class="ticks">
          ${tick('Trials come first,', 'sorted by end date.')}
          ${tick('The real price', 'after the trial, next to the days left.')}
          ${tick('Reminders before the trial ends,', 'not after the first charge.')}
        </ul>
        <p style="margin-top:22px"><a class="btn btn-ghost btn-sm" href="/free-trial-reminder/">More about trial reminders &rarr;</a></p>
      </div>
      <div class="shot">${device('app-trial.jpg', 'RenewRadar detail screen for a Spotify free trial ending in 2 days, then $11.99 a month')}</div>
    </div>

    <div class="split flip reveal">
      <div>
        <p class="tag">Totals</p>
        <h2>See what it all costs per month and per year.</h2>
        <p class="lede">Weekly, monthly, quarterly and yearly plans are converted to one monthly figure and split by category.</p>
        <ul class="ticks">
          ${tick('Monthly and yearly totals', 'at the top of Home.')}
          ${tick('Spend by category:', 'streaming, music, software and more.')}
          ${tick('With Pro:', 'the next 12 months of charges and a CSV export.')}
        </ul>
        <p style="margin-top:22px"><a class="btn btn-ghost btn-sm" href="/subscription-cost-calculator/">Try the free cost calculator &rarr;</a></p>
      </div>
      <div class="shot">${device('app-stats.jpg', 'RenewRadar stats screen showing $50.56 a month and $606.76 a year, split by category')}</div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="privacy reveal">
      <div>
        <p class="tag">Private by design</p>
        <h2>Your subscriptions are nobody else&rsquo;s business.</h2>
        <p>RenewRadar never asks for your bank login, your email or even your name. What you enter stays on your phone, and reminders are scheduled on your phone too.</p>
        <a class="btn btn-ghost btn-sm" href="/private-subscription-tracker/">How we keep it private &rarr;</a>
      </div>
      <ul class="zeros">
        <li><b>0</b><span>bank connections</span></li>
        <li><b>0</b><span>accounts to create</span></li>
        <li><b>0</b><span>ads or trackers</span></li>
        <li><b>1</b><span>one-time price for Pro</span></li>
      </ul>
    </div>
  </div>
</section>

<section class="section section-alt" id="pricing">
  <div class="wrap">
    <div class="section-head reveal">
      <p class="tag">Pricing</p>
      <h2>No subscription for your subscription tracker.</h2>
      <p>Start free. If you track more than five things, Pro is a single payment that never renews.</p>
    </div>
    <div class="plans reveal">
      <div class="plan">
        <h3>Free</h3>
        <p class="fine">Everything you need to start.</p>
        <p class="price">$0</p>
        <ul class="ticks">
          <li>Up to 5 active subscriptions and trials</li>
          <li>Reminders before every charge</li>
          <li>Free trial countdowns</li>
          <li>Monthly and yearly totals by category</li>
        </ul>
      </div>
      <div class="plan plan-pro">
        <h3>Pro</h3>
        <p class="fine">Everything in Free, without the limit.</p>
        <p class="price">US$9.99 <small>one time</small></p>
        <ul class="ticks">
          <li>Unlimited subscriptions and trials</li>
          <li>Detailed reports for the next 12 months</li>
          <li>CSV export of your full list</li>
          <li>No renewal, ever</li>
        </ul>
      </div>
    </div>
    <p class="fine center" style="margin-top:22px;max-width:46em">Prices on Google Play are shown in your local currency and can differ by country. Cancelled items do not count toward the free limit. Pro is linked to your Google account and can be restored on a new phone.</p>
  </div>
</section>

<section class="section" id="faq">
  <div class="wrap">
    <div class="section-head reveal">
      <p class="tag">Questions</p>
      <h2>Frequently asked questions</h2>
    </div>
    ${questions.html}
  </div>
</section>

${ctaBand()}
`,
};
