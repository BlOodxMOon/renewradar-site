// Extracts page content from the built static site into JSON for the WordPress seeder.
// Run from the site repo root: node _wp/export-content.mjs
import { readFileSync, writeFileSync } from 'node:fs';
import { pathToFileURL } from 'node:url';
import { join } from 'node:path';

const root = process.cwd();
const pick = (html, re) => (html.match(re) || [, ''])[1].trim();

const entries = [
  { file: 'subscription-tracker/index.html', type: 'page', slug: 'subscription-tracker', mod: '10-subscription-tracker' },
  { file: 'free-trial-reminder/index.html', type: 'page', slug: 'free-trial-reminder', mod: '11-free-trial-reminder' },
  { file: 'multi-currency-subscription-tracker/index.html', type: 'page', slug: 'multi-currency-subscription-tracker', mod: '12-multi-currency' },
  { file: 'private-subscription-tracker/index.html', type: 'page', slug: 'private-subscription-tracker', mod: '13-private' },
  { file: 'subscription-cost-calculator/index.html', type: 'page', slug: 'subscription-cost-calculator', mod: '20-calculator', template: 'page-calculator.php' },
  { file: 'guides/cancel-free-trial-before-charged/index.html', type: 'post', slug: 'cancel-free-trial-before-charged', mod: '31-cancel-free-trial' },
  { file: 'guides/cancel-google-play-subscription/index.html', type: 'post', slug: 'cancel-google-play-subscription', mod: '32-cancel-google-play' },
  { file: 'guides/subscription-audit/index.html', type: 'post', slug: 'subscription-audit', mod: '33-subscription-audit' },
  { file: 'rocket-money-alternative/index.html', type: 'page', slug: 'rocket-money-alternative', mod: '40-rocket-money-alternative' },
  { file: 'bobby-alternative-android/index.html', type: 'page', slug: 'bobby-alternative-android', mod: '41-bobby-alternative' },
  { file: 'privacy.html', type: 'page', slug: 'privacy', mod: '90-privacy', legal: true },
  { file: 'terms.html', type: 'page', slug: 'terms', mod: '91-terms', legal: true },
];

const out = [];
for (const e of entries) {
  const html = readFileSync(join(root, e.file), 'utf8');
  const page = (await import(pathToFileURL(join(root, '_src/pages', e.mod + '.mjs')).href)).default;
  const hero = pick(html, /<header class="page-hero">([\s\S]*?)<\/header>/);
  const h1 = pick(hero, /<h1>([\s\S]*?)<\/h1>/);
  const tag = pick(hero, /<p class="tag">([\s\S]*?)<\/p>/);
  const lede = pick(hero, /<p class="lede">([\s\S]*?)<\/p>/);
  let body;
  if (e.template) {
    body = pick(html, /<div class="prose">([\s\S]*?)<\/div><\/div>\s*<section class="cta-band">/);
  } else {
    body = pick(html, /<div class="prose">([\s\S]*?)(?:<aside class="related">|<\/div><\/div>\s*(?:<\/div>)?\s*(?:<section class="cta-band">|<\/main>))/);
  }
  const relatedHtml = pick(html, /<aside class="related">([\s\S]*?)<\/aside>/);
  const related = [...relatedHtml.matchAll(/<a class="card" href="([^"]+)"><h3>([\s\S]*?)<\/h3><p>([\s\S]*?)<\/p>/g)].map((m) => ({
    href: m[1],
    title: m[2],
    text: m[3],
  }));
  out.push({
    type: e.type,
    slug: e.slug,
    template: e.template || '',
    legal: !!e.legal,
    title: h1.replace(/<[^>]+>/g, ''),
    seoTitle: page.title,
    description: page.description,
    tag,
    lede,
    content: body.replace(/(src|href)="\/assets\//g, '$1="{{assets}}/'),
    related,
  });
}
writeFileSync(join(root, '_wp/renewradar-core/content.json'), JSON.stringify(out, null, 1));
console.log(out.map((o) => `${o.type} ${o.slug}: ${o.content.length} chars, ${o.related.length} related`).join('\n'));
