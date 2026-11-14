// Shared page shell and building blocks. Pages in ./pages return their main content as HTML strings.

export const SITE = 'https://renewradar.pixeldevcode.com';
export const PLAY = 'https://play.google.com/store/apps/details?id=com.pixeldevcode.renewradar';
export const EMAIL = 'renewradar@pixeldevcode.com';

// Color mode for visitors who have not picked one: 'festive', 'light', 'dark', or '' to follow the system.
const DEFAULT_MODE = 'festive';

const esc = (s) =>
  String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

const PLAY_ICON =
  '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M1.337.924a1.486 1.486 0 0 0-.112.568v21.017c0 .217.045.419.124.6l11.155-11.087L1.337.924z"/><path fill="#34A853" d="M13.544 10.989l3.258-3.238L3.45.195a1.466 1.466 0 0 0-.946-.179l11.04 10.973z"/><path fill="#FBBC04" d="M22.018 13.298l-3.919 2.218-3.515-3.493 3.543-3.521 3.891 2.202a1.49 1.49 0 0 1 0 2.594z"/><path fill="#EA4335" d="M13.544 13.056l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54-3.23-3.21z"/></svg>';

export const ICONS = {
  check: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>',
  bell: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15z"/><path d="M10 20.5a2 2 0 0 0 4 0"/></svg>',
  clock: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>',
  globe: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.5 2.6 3.5 5.4 3.5 8.5s-1 5.9-3.5 8.5c-2.5-2.6-3.5-5.4-3.5-8.5s1-5.9 3.5-8.5z"/></svg>',
  lock: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10.5" width="14" height="10" rx="2.5"/><path d="M8.5 10.5V8a3.5 3.5 0 0 1 7 0v2.5"/></svg>',
  chart: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h16"/><path d="M7 16v-5M12 16V6M17 16v-8"/></svg>',
  calc: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="3.5" width="14" height="17" rx="2.5"/><path d="M8.5 7.5h7M8.5 12h1M12 12h1M15.5 12h0M8.5 16h1M12 16h1M15.5 16h0"/></svg>',
  book: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 5.5A2 2 0 0 1 6.5 3.5H19v15H6.5a2 2 0 0 0-2 2z"/><path d="M4.5 20.5v-15"/></svg>',
  swap: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h12l-3.5-3.5M17 17H5l3.5 3.5"/></svg>',
  radar: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><path d="M12 12l6-4"/></svg>',
  pause: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6v12M15 6v12"/></svg>',
  file: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 3.5h7l4 4v13h-11z"/><path d="M13.5 3.5v4h4M9 13h6M9 16.5h6"/></svg>',
};

export function play(size = '') {
  return `<a class="play${size ? ' play-' + size : ''}" href="${PLAY}">${PLAY_ICON}<span>${
    size ? '' : '<small>Get it on</small>'
  }<b>Google Play</b></span></a>`;
}

const SIZES = { "app-trial.jpg": [672, 2048] };

export function device(img, alt, cls = "device") {
  const [w, h] = SIZES[img] || [922, 2048];
  return `<div class="${cls}"><div><img src="/assets/img/${img}" alt="${esc(alt)}" width="${w}" height="${h}" loading="lazy" decoding="async"></div></div>`;
}

export function crumbsHtml(crumbs) {
  return `<ol class="crumbs">${crumbs
    .map(([name, href]) => `<li>${href ? `<a href="${href}">${name}</a>` : `<span aria-current="page">${name}</span>`}</li>`)
    .join('')}</ol>`;
}

export function crumbsSchema(crumbs, path) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: crumbs.map(([name, href], i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: name.replace(/<[^>]+>/g, ''),
      item: SITE + (href || path),
    })),
  };
}

export function pageHero({ crumbs, tag, h1, lede, meta, cta = true }) {
  return `<header class="page-hero"><div class="wrap">
    ${crumbs ? crumbsHtml(crumbs) : ''}
    ${tag ? `<p class="tag">${tag}</p>` : ''}
    <h1>${h1}</h1>
    ${lede ? `<p class="lede">${lede}</p>` : ''}
    ${cta ? `<div class="hero-cta">${play()}<a class="btn btn-ghost" href="/#how">See how it works</a></div>` : ''}
    ${meta ? `<p class="meta">${meta}</p>` : ''}
  </div></header>`;
}

export function article({ toc, body, related }) {
  const tocHtml = toc
    ? `<nav class="toc" aria-label="On this page"><p>On this page</p><ol>${toc
        .map(([id, label]) => `<li><a href="#${id}">${label}</a></li>`)
        .join('')}</ol></nav>`
    : '';
  return `<div class="wrap article${toc ? '' : ' no-toc'}">${tocHtml}<div class="prose">${body}${
    related ? relatedHtml(related) : ''
  }</div></div>`;
}

export function relatedHtml(items) {
  return `<aside class="related"><h2>Keep reading</h2><div class="grid-2">${items
    .map(
      ([href, title, text]) =>
        `<a class="card" href="${href}"><h3>${title}</h3><p>${text}</p><span class="go">Read more &rarr;</span></a>`
    )
    .join('')}</div></aside>`;
}

export function ctaBand(title = 'Know before you are charged.', text = 'Free for up to five subscriptions. Pro is a single one-time purchase.') {
  return `<section class="cta-band"><div class="wrap"><div class="cta-box reveal">
    <div><h2>${title}</h2><p>${text}</p></div>
    ${play()}
  </div></div></section>`;
}

export function faq(items) {
  const html = `<div class="faq">${items
    .map(([q, a]) => `<details><summary>${q}</summary><div>${a}</div></details>`)
    .join('')}</div>`;
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: items.map(([q, a]) => ({
      '@type': 'Question',
      name: q,
      acceptedAnswer: { '@type': 'Answer', text: a.replace(/<[^>]+>/g, '') },
    })),
  };
  return { html, schema };
}

export function articleSchema({ title, description, path, updated, published }) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: title,
    description,
    mainEntityOfPage: SITE + path,
    datePublished: published || updated,
    dateModified: updated,
    image: SITE + '/og-banner.png',
    author: { '@type': 'Organization', name: 'Pixel DevCode', url: 'https://pixeldevcode.com' },
    publisher: {
      '@type': 'Organization',
      name: 'Pixel DevCode',
      logo: { '@type': 'ImageObject', url: SITE + '/assets/img/icon.png' },
    },
  };
}

const NAV = [
  ['/subscription-tracker/', 'Features', 'features'],
  ['/free-trial-reminder/', 'Free trials', 'trials'],
  ['/subscription-cost-calculator/', 'Calculator', 'calculator'],
  ['/guides/', 'Guides', 'guides'],
  ['/#pricing', 'Pricing', 'pricing'],
];

const MODE_ICONS = {
  light:
    '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2M12 19.5v2M4.6 4.6L6 6M18 18l1.4 1.4M2.5 12h2M19.5 12h2M4.6 19.4L6 18M18 6l1.4-1.4"/></svg>',
  dark: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 13.2A8.5 8.5 0 1 1 10.8 3.5a6.6 6.6 0 0 0 9.7 9.7z"/></svg>',
  festive:
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5v19M3.8 7.2l16.4 9.6M3.8 16.8l16.4-9.6M9.5 4l2.5 2.5L14.5 4M9.5 20l2.5-2.5 2.5 2.5M4.2 10.3l3.4.9-.9 3.4M19.8 13.7l-3.4-.9.9-3.4"/></svg>',
};

function header(active) {
  const links = NAV.map(
    ([href, label, key]) => `<a href="${href}"${key === active ? ' aria-current="page"' : ''}>${label}</a>`
  ).join('');
  const modes = [
    ['light', 'Light mode'],
    ['dark', 'Dark mode'],
    ['festive', 'Christmas mode'],
  ]
    .map(([m, label]) => `<button type="button" data-mode="${m}" aria-pressed="false" aria-label="${label}" title="${label}">${MODE_ICONS[m]}</button>`)
    .join('');
  return `<header class="top"><div class="wrap bar">
    <a class="lockup" href="/" aria-label="RenewRadar, home"><img src="/assets/img/icon.png" alt="" width="38" height="38"><span><b>RenewRadar</b><small>by Pixel DevCode</small></span></a>
    <nav class="nav" id="site-nav" aria-label="Main">${links}</nav>
    <div class="bar-end">
      <div class="modes" role="group" aria-label="Color mode">${modes}</div>
      ${play('sm')}
      <button class="menu-btn" type="button" aria-label="Menu" aria-expanded="false" aria-controls="site-nav"><span></span><span></span><span></span></button>
    </div>
  </div></header>`;
}

function footer() {
  const col = (title, items) =>
    `<div><h2>${title}</h2><ul>${items.map(([href, label]) => `<li><a href="${href}">${label}</a></li>`).join('')}</ul></div>`;
  return `<footer class="foot"><div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <a class="lockup" href="/" aria-label="RenewRadar, home"><img src="/assets/img/icon.png" alt="" width="38" height="38"><span><b>RenewRadar</b><small>by Pixel DevCode</small></span></a>
        <p>A private subscription and free trial tracker for Android. Your list stays on your phone.</p>
        ${play()}
      </div>
      ${col('Product', [
        ['/subscription-tracker/', 'Subscription tracker'],
        ['/free-trial-reminder/', 'Free trial reminders'],
        ['/multi-currency-subscription-tracker/', 'Multi-currency tracking'],
        ['/private-subscription-tracker/', 'Private by design'],
        ['/#pricing', 'Pricing'],
      ])}
      ${col('Tools and guides', [
        ['/subscription-cost-calculator/', 'Cost calculator'],
        ['/guides/cancel-free-trial-before-charged/', 'Cancel a free trial in time'],
        ['/guides/cancel-google-play-subscription/', 'Cancel on Google Play'],
        ['/guides/subscription-audit/', 'Subscription audit'],
        ['/guides/', 'All guides'],
      ])}
      ${col('Compare', [
        ['/rocket-money-alternative/', 'Rocket Money alternative'],
        ['/bobby-alternative-android/', 'Bobby for Android'],
      ])}
      ${col('Company', [
        ['https://pixeldevcode.com', 'Pixel DevCode'],
        ['/privacy.html', 'Privacy policy'],
        ['/terms.html', 'Terms of service'],
        [`mailto:${EMAIL}`, 'Contact'],
      ])}
    </div>
    <div class="foot-base"><span>&copy; 2026 <a href="https://pixeldevcode.com">Pixel DevCode</a>. Google Play and the Google Play logo are trademarks of Google LLC.</span><span>Made for Android</span></div>
  </div></footer>`;
}

export function layout(page, assets) {
  const url = SITE + page.path;
  const title = page.title;
  const schema = [].concat(page.schema || []);
  return `<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>${esc(title)}</title>
  <meta name="description" content="${esc(page.description)}">
  ${page.noindex ? '<meta name="robots" content="noindex">' : `<link rel="canonical" href="${url}">`}
${
    DEFAULT_MODE === 'festive'
      ? '  <meta name="theme-color" content="#13284b">'
      : '  <meta name="theme-color" content="#f7f7fb" media="(prefers-color-scheme: light)">\n  <meta name="theme-color" content="#0d0c20" media="(prefers-color-scheme: dark)">'
  }
  <meta property="og:type" content="${page.ogType || 'website'}">
  <meta property="og:site_name" content="RenewRadar">
  <meta property="og:locale" content="en_US">
  <meta property="og:title" content="${esc(page.ogTitle || title)}">
  <meta property="og:description" content="${esc(page.description)}">
  <meta property="og:url" content="${url}">
  <meta property="og:image" content="${SITE}/og-banner.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="RenewRadar by Pixel DevCode. Know before you are charged.">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="${esc(page.ogTitle || title)}">
  <meta name="twitter:description" content="${esc(page.description)}">
  <meta name="twitter:image" content="${SITE}/og-banner.png">
  <link rel="icon" href="/assets/img/icon.png">
  <link rel="apple-touch-icon" href="/assets/img/icon.png">
  <link rel="preload" href="/assets/fonts/plus-jakarta-sans.woff2" as="font" type="font/woff2" crossorigin>
  <script>document.documentElement.className="js";try{var t=localStorage.getItem("rr-theme");}catch(e){}if(t!=="light"&&t!=="dark"&&t!=="festive")t="${DEFAULT_MODE}";if(t)document.documentElement.setAttribute("data-theme",t)</script>
  <link rel="stylesheet" href="/assets/site.css?v=${assets.css}">
${schema.map((s) => `  <script type="application/ld+json">${JSON.stringify(s)}</script>`).join('\n')}
</head>
<body>
  <a class="skip" href="#main">Skip to content</a>
  ${header(page.nav)}
  <main id="main">
${page.main}
  </main>
  ${footer()}
  <script src="/assets/site.js?v=${assets.js}" defer></script>
${(page.scripts || []).map((s) => `  <script src="${s}?v=${assets[s] || assets.js}" defer></script>`).join('\n')}
</body>
</html>
`;
}
