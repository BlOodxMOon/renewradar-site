import { legalPage } from '../legal.mjs';

const mail = '<a href="mailto:renewradar@pixeldevcode.com">renewradar@pixeldevcode.com</a>';

export default legalPage({
  path: '/terms.html',
  file: 'terms.html',
  updated: '2026-11-14',
  title: 'Terms of Service | RenewRadar by Pixel DevCode',
  h1: 'Terms of Service',
  description: 'The terms for using RenewRadar, including the one-time Pro unlock, refunds and reminders.',
  summary:
    "RenewRadar helps you keep track of subscriptions and free trials, but you stay responsible for cancelling them in time. Pro is a one-time purchase through Google Play, tied to your Google account, and refunds follow Google Play's refund policy.",
  sections: [
    [
      'Agreement',
      `<p>These terms apply to the RenewRadar app ("the app"), published by Pixel DevCode ("we", "us"). By installing or using the app, you agree to these terms. If you do not agree, please do not use the app. Your use of Google Play is also covered by Google's own terms.</p>`,
    ],
    [
      'What the app does',
      `<p>RenewRadar lets you list your subscriptions and free trials by hand, see what they cost, and get reminders before renewal and trial end dates. The app does not connect to your bank, does not cancel subscriptions for you, and does not know about charges you have not entered.</p>
<p>Totals and renewal dates are estimates based on what you enter. They are for your own planning and are not financial advice.</p>`,
    ],
    [
      'Free version and Pro',
      `<ul>
  <li>The free version lets you track up to five active subscriptions and trials. Cancelled items do not count toward this limit.</li>
  <li><strong>Pro</strong> is an optional one-time purchase that unlocks unlimited items, detailed spending reports and CSV export. It is not a subscription and does not renew.</li>
  <li>Pro is linked to the Google account you used to buy it. You can restore it on a new phone or after reinstalling by signing in to Google Play with the same account and tapping <strong>Restore purchase</strong> in Settings.</li>
  <li>"Lifetime" means for as long as we offer the app on Google Play. Features we add later may or may not be part of Pro.</li>
</ul>`,
    ],
    [
      'Payments and refunds',
      `<p>All payments are handled by Google Play. We never see or store your payment details. Prices are shown in the app and on Google Play before you buy, and may vary by country.</p>
<p>Refunds are handled under <a href="https://support.google.com/googleplay/answer/2479637">Google Play's refund policy</a>. You can request one from Google Play directly. If you have a problem with your purchase, you can also email us and we will try to help.</p>`,
    ],
    [
      'Promo codes',
      `<p>We may give out Google Play promo codes for Pro. Each code works once, has no cash value, cannot be exchanged for money, and may have an expiry date. Codes are redeemed through Google Play and are subject to Google's terms.</p>`,
    ],
    [
      'Reminders',
      `<p>Reminders are local notifications scheduled on your phone. We make a reasonable effort to deliver them on time, but delivery depends on your phone. Notification permissions, battery-saving settings, Do Not Disturb, the phone being off, or changes made by the phone maker can delay or block them.</p>
<p>You are responsible for checking the actual renewal and trial end dates with each service and for cancelling in time. We are not responsible for charges you receive, including charges after a reminder was late, missed, or based on a date that was entered incorrectly.</p>`,
    ],
    [
      'Your data',
      `<p>Your subscriptions are stored only on your phone, as described in our <a href="/privacy.html">Privacy Policy</a>. Because we do not keep a copy, we cannot recover your data if your phone is lost or reset or the app is uninstalled. Pro users can export a CSV copy at any time.</p>`,
    ],
    [
      'Acceptable use',
      `<p>You may use the app for your own personal, work or business tracking. You may not copy, resell or redistribute the app, remove or bypass its purchase checks, or reverse engineer it except where the law allows this.</p>`,
    ],
    [
      'Ownership and trademarks',
      `<p>The app, its design and its content belong to Pixel DevCode. Names of subscription services shown in the app belong to their owners and are used only to identify those services. RenewRadar is not affiliated with or endorsed by any of them.</p>`,
    ],
    [
      'Changes to the app',
      `<p>We may update, change or stop offering the app or any feature, for example to fix problems, follow store rules or improve it. If we stop offering the app, the version on your phone will keep working with the data already on it for as long as your phone supports it, but it may no longer receive updates.</p>`,
    ],
    [
      'No warranty',
      `<p>The app is provided "as is" and "as available". As far as the law allows, we make no promises that it will be error-free, always available or suitable for a particular purpose.</p>`,
    ],
    [
      'Limitation of liability',
      `<p>As far as the law allows, Pixel DevCode is not liable for indirect or consequential losses, or for subscription charges, fees or lost savings related to your use of the app. Our total liability for any claim about the app is limited to the amount you paid for Pro, or US$10 if you did not buy Pro.</p>`,
    ],
    [
      'Your consumer rights',
      `<p>Nothing in these terms takes away rights you have under the consumer protection laws of the country where you live, if those laws do not allow them to be excluded or limited.</p>`,
    ],
    [
      'Governing law',
      `<p>These terms are governed by the laws of the Republic of the Philippines, without affecting any mandatory consumer protection laws of the country where you live.</p>`,
    ],
    [
      'Changes to these terms',
      `<p>If we change these terms, we will update this page and the date above. Material changes will also be noted in the app's release notes. If you keep using the app after a change, the updated terms apply.</p>`,
    ],
    ['Contact', `<p>Pixel DevCode<br>${mail}</p>`],
  ],
});
