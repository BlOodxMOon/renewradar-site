import { legalPage } from '../legal.mjs';

const mail = '<a href="mailto:renewradar@pixeldevcode.com">renewradar@pixeldevcode.com</a>';

export default legalPage({
  path: '/privacy.html',
  file: 'privacy.html',
  updated: '2026-11-14',
  title: 'Privacy Policy | RenewRadar by Pixel DevCode',
  h1: 'Privacy Policy',
  description: 'How RenewRadar handles your information. Your subscription data stays on your device.',
  summary:
    'the subscriptions and free trials you add stay on your phone. We do not run any server that receives them, and RenewRadar has no accounts, no ads, no analytics and no bank connection.',
  sections: [
    ['Who we are', `<p>RenewRadar ("the app") is published by Pixel DevCode ("we", "us"). You can contact us at ${mail}.</p>`],
    [
      'Information that stays on your device',
      `<p>Everything you enter is stored only on your phone, in the app's private storage:</p>
<ul>
  <li>Your subscriptions and free trials: names, prices, currencies, billing cycles, renewal dates, categories, reminder settings, notes and colors.</li>
  <li>Your settings, such as your default currency, default reminder timing and light or dark theme.</li>
</ul>
<p>We cannot see this information and do not receive a copy. If you uninstall the app, it is deleted from your phone. If you want to keep a copy first, use the export feature described below.</p>`,
    ],
    [
      'Reminders',
      `<p>Reminders are local notifications created by the app on your phone. They do not use a push notification service, and no information is sent to us to produce them. The app asks for your permission to show notifications, and you can turn them off at any time in your phone's settings.</p>`,
    ],
    [
      'Network connections the app makes',
      `<p>The app makes only two kinds of network requests, and neither contains your subscription data.</p>
<ul>
  <li><strong>Service list update.</strong> About once a week the app downloads a plain list of popular service names, categories and colors from <code>renewradar.pixeldevcode.com</code> to power the name suggestions when you add a subscription. The request contains no information about you or your subscriptions. As with any web request, the server that hosts the file (GitHub Pages) can see your IP address and standard request details.</li>
  <li><strong>Purchases.</strong> The optional one-time Pro unlock is processed by Google Play. We use RevenueCat, a purchase-management service, to confirm whether the unlock is active on your device and to restore it if you reinstall. RevenueCat receives a randomly generated anonymous app user ID, your purchase status and technical details such as the app version, device type and IP address. We do not receive your payment details, and the app does not send it your subscriptions. See <a href="https://www.revenuecat.com/privacy/">RevenueCat's privacy policy</a> and <a href="https://policies.google.com/privacy">Google's privacy policy</a>.</li>
</ul>`,
    ],
    [
      'Exporting your data',
      `<p>With Pro, you can export your subscriptions as a spreadsheet (CSV) file. The file is created on your phone and handed to your phone's share sheet. It goes only where you choose to send it, and we never receive it.</p>`,
    ],
    [
      'What we do not do',
      `<ul>
  <li>We do not create accounts or ask for your name, email address or phone number.</li>
  <li>We do not connect to your bank or read your payments.</li>
  <li>We do not show ads, use advertising identifiers or sell any data.</li>
  <li>We do not use analytics or tracking tools inside the app.</li>
</ul>`,
    ],
    ['Children', `<p>RenewRadar is not directed at children under 13, and we do not knowingly collect information from them.</p>`],
    [
      'Your choices and rights',
      `<p>Because your subscription data never leaves your phone, you control it directly: you can edit or delete any item in the app, export it, or remove everything by uninstalling the app. For questions about purchase data held by RevenueCat or Google, or to exercise any data rights you may have where you live, contact us at ${mail}.</p>`,
    ],
    [
      'This website',
      `<p>This website is a set of static pages. It sets no cookies and uses no analytics or third-party scripts or fonts. If you pick a color mode, that choice is remembered in your browser's local storage on your device. The subscription cost calculator runs entirely in your browser, and nothing you type into it is stored or sent anywhere. Like any web host, GitHub Pages may log visitors' IP addresses for security and operations.</p>`,
    ],
    [
      'Changes to this policy',
      `<p>If we change how the app handles information, we will update this page and the date above. Material changes will also be noted in the app's release notes.</p>`,
    ],
    ['Contact', `<p>Pixel DevCode<br>${mail}</p>`],
  ],
});
