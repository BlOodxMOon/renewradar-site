import { ICONS, PLAY, SITE, ctaBand, device, faq, play } from '../layout.mjs';

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
  <div class="wrap hero-grid">
    <div>
      <p class="tag rise" style="--i:0">Subscription and free trial tracker for Android</p>
      <h1 class="rise" style="--i:1">Know <span class="mark">before</span> you are charged.</h1>
      <p class="lede rise" style="--i:2">RenewRadar keeps every subscription and free trial on one screen, counts down the days, and reminds you before each charge. No bank login, no account, and your list never leaves your phone.</p>
      <div class="hero-cta rise" style="--i:3">${play()}<a class="btn btn-ghost" href="#how">How it works</a></div>
      <ul class="facts rise" style="--i:4">
        <li>${ICONS.check}Free for 5 subscriptions</li>
        <li>${ICONS.check}One-time Pro, no subscription</li>
        <li>${ICONS.check}Works offline</li>
      </ul>
    </div>
    <div class="stage rise" style="--i:2" aria-hidden="true">
      <div class="scope">
        <div class="sweep"></div>
        <i class="blip" style="--x:62%;--y:30%;--d:-5.1s"></i>
        <i class="blip" style="--x:30%;--y:38%;--d:-1.2s"></i>
        <i class="blip dim" style="--x:74%;--y:70%"></i>
        <i class="blip dim" style="--x:20%;--y:66%"></i>
        <i class="blip dim" style="--x:44%;--y:84%"></i>
      </div>
      <div class="phone"><div class="screen"><img src="/assets/img/app-home.jpg" alt="" width="922" height="2048" fetchpriority="high"></div></div>
      <div class="ping-card ping-a"><img src="/assets/img/icon.png" alt="" width="34" height="34"><div><small>RenewRadar &middot; now</small><b>Spotify free trial ends in 2 days</b>You will be charged $11.99 on Oct 30 unless you cancel.</div></div>
      <div class="ping-card ping-b"><img src="/assets/img/icon.png" alt="" width="34" height="34"><div><small>RenewRadar &middot; 9:00</small><b>Netflix renews tomorrow</b>$15.99 will be charged on Nov 2.</div></div>
    </div>
  </div>
</section>

<section class="section section-alt" id="how">
  <div class="wrap">
    <div class="section-head reveal">
      <p class="tag">How it works</p>
      <h2>Three steps, about a minute per subscription.</h2>
      <p>You add what you pay for once. RenewRadar keeps the dates current and taps you on the shoulder before money leaves your account.</p>
    </div>
    <ol class="steps reveal">
      <li><h3>Add it</h3><p>Type a name and pick from suggestions for popular services. Set the price, currency, billing cycle and next charge date.</p></li>
      <li><h3>Choose your reminders</h3><p>Get a notification 14, 7, 3 or 1 day before, or on the day. The default is 3 days and 1 day before, and every subscription can have its own timing.</p></li>
      <li><h3>Decide in time</h3><p>Keep it, pause it or mark it cancelled. Renewal dates roll forward on their own, so the list stays right without upkeep.</p></li>
    </ol>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="split reveal">
      <div>
        <p class="tag">Free trials</p>
        <h2>Free trials get their own countdown.</h2>
        <p class="lede">Most surprise charges start as a free trial you forgot about. RenewRadar puts every trial at the top, with the days left and the price that starts afterwards.</p>
        <ul class="ticks">
          ${tick('A Trials section first.', 'Sorted by end date, so the next one to end is always on top.')}
          ${tick('The real price.', 'See what you will pay after the trial, right next to the days left.')}
          ${tick('Reminders for the trial end.', 'Not the first renewal, when it is already too late.')}
        </ul>
        <p><a class="btn btn-ghost btn-sm" href="/free-trial-reminder/">More about trial reminders &rarr;</a></p>
      </div>
      <div class="shot">${device('app-trial.jpg', 'RenewRadar detail screen for a Spotify free trial ending in 2 days, then $11.99 a month')}</div>
    </div>

    <div class="split flip reveal">
      <div>
        <p class="tag">Totals</p>
        <h2>See what it all costs per month and per year.</h2>
        <p class="lede">A $4.99 weekly plan, a $139 yearly plan and a few monthly ones are hard to add up in your head. RenewRadar does it for you.</p>
        <ul class="ticks">
          ${tick('One monthly figure.', 'Weekly, quarterly and yearly plans are converted, so totals compare like with like.')}
          ${tick('Spend by category.', 'Streaming, music, software, storage, gaming and more.')}
          ${tick('Pro reports.', 'The next 12 months of charges, your biggest costs and a CSV export.')}
        </ul>
        <p><a class="btn btn-ghost btn-sm" href="/subscription-cost-calculator/">Try the free cost calculator &rarr;</a></p>
      </div>
      <div class="shot">${device('app-stats.jpg', 'RenewRadar stats screen showing $50.56 a month and $606.76 a year, split by category')}</div>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="wrap">
    <div class="section-head reveal">
      <p class="tag">Also inside</p>
      <h2>Small details that make it fit your life.</h2>
    </div>
    <div class="grid-3 reveal">
      <div class="card"><div class="ic">${ICONS.globe}</div><h3>Any currency</h3><p>Enter each subscription in the currency you are billed in. Totals stay per currency instead of guessing rates.</p></div>
      <div class="card"><div class="ic">${ICONS.radar}</div><h3>Due this week, due this month</h3><p>Home groups what is coming up next, so a glance tells you what is about to be charged.</p></div>
      <div class="card"><div class="ic">${ICONS.pause}</div><h3>Pause, cancel, restore</h3><p>Pause a subscription you stopped for a while, or mark it cancelled and restore it later if you sign up again.</p></div>
      <div class="card"><div class="ic">${ICONS.swap}</div><h3>Personal, work or business</h3><p>Tag what each subscription is for, so work tools do not get mixed up with your own.</p></div>
      <div class="card"><div class="ic">${ICONS.clock}</div><h3>Any billing cycle</h3><p>Weekly, monthly, quarterly, yearly, or a custom number of days.</p></div>
      <div class="card"><div class="ic">${ICONS.file}</div><h3>Export to a spreadsheet</h3><p>With Pro, export your full list as a CSV file and keep your own copy.</p></div>
    </div>
  </div>
</section>

<section class="band">
  <div class="wrap">
    <p class="tag">Private by design</p>
    <h2>Your subscriptions are nobody else&rsquo;s business.</h2>
    <p class="lede">RenewRadar never asks for your bank login, your email or even your name. What you enter is stored on your phone, and reminders are scheduled on your phone too.</p>
    <ul class="nots reveal">
      <li><b>0</b><span>bank connections</span></li>
      <li><b>0</b><span>accounts to create</span></li>
      <li><b>0</b><span>ads or tracking tools</span></li>
      <li><b>1</b><span>one-time price for Pro</span></li>
    </ul>
    <p style="margin-top:36px"><a class="btn btn-ghost" style="color:inherit;border-color:currentColor" href="/private-subscription-tracker/">How RenewRadar keeps your data private</a></p>
  </div>
</section>

<section class="section" id="pricing">
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
    <p class="fine" style="margin-top:20px;max-width:46em">Prices on Google Play are shown in your local currency and can differ by country. Cancelled items do not count toward the free limit. Pro is linked to your Google account and can be restored on a new phone.</p>
  </div>
</section>

<section class="section section-alt">
  <div class="wrap">
    <div class="section-head reveal">
      <p class="tag">Tools and guides</p>
      <h2>Spend less on things you forgot you pay for.</h2>
    </div>
    <div class="grid-3 reveal">
      <a class="card" href="/subscription-cost-calculator/"><div class="ic">${ICONS.calc}</div><h3>Subscription cost calculator</h3><p>Add what you pay for and see the true monthly, yearly and five-year cost.</p><span class="go">Open the calculator &rarr;</span></a>
      <a class="card" href="/guides/cancel-free-trial-before-charged/"><div class="ic">${ICONS.clock}</div><h3>Cancel a free trial in time</h3><p>Where to cancel for Google Play, the App Store and websites, and when to do it.</p><span class="go">Read the guide &rarr;</span></a>
      <a class="card" href="/guides/subscription-audit/"><div class="ic">${ICONS.book}</div><h3>The 20-minute subscription audit</h3><p>Find every recurring charge, decide what stays, and stop paying for the rest.</p><span class="go">Read the guide &rarr;</span></a>
    </div>
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
