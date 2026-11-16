// Builds the scheduled articles from the Markdown files in this folder.
// Writes renewradar-core/articles/articles.json (read by the plugin) and images.json (rendered by
// renewradar-promo/render-articles.mjs into renewradar-core/articles/img/).
// Usage: node _wp/articles/build.mjs
import { readFileSync, readdirSync, writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const outDir = join(here, '..', 'renewradar-core', 'articles');
mkdirSync(join(outDir, 'img'), { recursive: true });

// Existing pages and guides that articles can link to as related reading.
const PAGES = {
  '/free-trial-reminder/': ['Free trial reminders', 'Countdowns and reminders before a free trial turns into a charge.'],
  '/subscription-tracker/': ['Subscription tracker app', 'Every subscription on one screen, sorted by what is charged next.'],
  '/subscription-reminder-app/': ['Subscription reminder app', 'A notification before every subscription charge, at the timing you choose.'],
  '/renewal-reminder-app/': ['Renewal reminders', 'Early warnings for yearly plans and renewals that are easy to forget.'],
  '/private-subscription-tracker/': ['Private by design', 'Why your subscription list never leaves your phone.'],
  '/multi-currency-subscription-tracker/': ['Multi-currency tracking', 'Subscriptions in several currencies, with honest totals.'],
  '/subscription-cost-calculator/': ['Subscription cost calculator', 'See what your subscriptions cost per month, per year and over five years.'],
  '/rocket-money-alternative/': ['A Rocket Money alternative', 'A subscription tracker that never sees your bank account.'],
  '/bobby-alternative-android/': ['Like Bobby, but for Android', 'A manual subscription tracker for Android phones.'],
  '/guides/cancel-free-trial-before-charged/': ['Cancel a free trial in time', 'Where to cancel on Google Play, the App Store and websites.'],
  '/guides/cancel-google-play-subscription/': ['Cancel on Google Play', 'Step by step, on the phone and on the web.'],
  '/guides/subscription-audit/': ['The 20-minute subscription audit', 'Find every subscription you pay for and decide what stays.'],
};

// Phone screenshots made by renewradar-promo/render-steps.mjs, used as ![alt](shot:name "caption").
const imgDir = join(outDir, 'img');
function jpegSize(file) {
  const b = readFileSync(file);
  for (let i = 2; i < b.length; ) {
    const marker = b[i + 1];
    const len = b.readUInt16BE(i + 2);
    if (marker >= 0xc0 && marker <= 0xc3) return { width: b.readUInt16BE(i + 7), height: b.readUInt16BE(i + 5) };
    i += 2 + len;
  }
  throw new Error(file + ': no JPEG size found');
}

const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
const slugify = (s) => s.toLowerCase().replace(/<[^>]+>/g, '').replace(/&[a-z]+;/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

function inline(s) {
  return esc(s)
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/(^|[^*])\*([^*]+)\*/g, '$1<em>$2</em>')
    .replace(/\[([^\]]+)\]\(([^)]+)\)/g, (_, t, u) => `<a href="${u}">${t}</a>`)
    .replace(/'/g, '&#8217;');
}

function markdown(md, slug, figures) {
  const out = [];
  const lines = md.replace(/\r/g, '').split('\n');
  let i = 0;
  while (i < lines.length) {
    const line = lines[i];
    if (!line.trim()) {
      i++;
      continue;
    }
    let m;
    if ((m = line.match(/^(#{2,3}) (.+)$/))) {
      const tag = m[1].length === 2 ? 'h2' : 'h3';
      out.push(`<${tag} id="${slugify(m[2])}">${inline(m[2])}</${tag}>`);
      i++;
    } else if ((m = line.match(/^!\[([^\]]*)\]\(fig:([a-z0-9]+)(?: "([^"]+)")?\)$/))) {
      const key = m[2];
      if (!figures[key]) throw new Error(`${slug}: figure "${key}" is not defined`);
      figures[key].used = true;
      const cap = m[3] ? `<figcaption>${inline(m[3])}</figcaption>` : '';
      out.push(`<figure class="fig"><img src="{{articles}}/img/${slug}-${key}.jpg" alt="${esc(m[1])}" width="1200" height="675" loading="lazy" decoding="async">${cap}</figure>`);
      i++;
    } else if ((m = line.match(/^!\[([^\]]*)\]\(shot:([a-z0-9-]+)(?: "([^"]+)")?\)$/))) {
      const file = join(imgDir, `shot-${m[2]}.jpg`);
      const { width, height } = jpegSize(file);
      const cap = m[3] ? `<figcaption>${inline(m[3])}</figcaption>` : '';
      out.push(`<figure class="fig fig-shot"><img src="{{articles}}/img/shot-${m[2]}.jpg" alt="${esc(m[1])}" width="${width}" height="${height}" loading="lazy" decoding="async">${cap}</figure>`);
      i++;
    } else if (line.startsWith('> ')) {
      const buf = [];
      while (i < lines.length && lines[i].startsWith('> ')) buf.push(lines[i++].slice(2));
      out.push(`<div class="note"><p>${inline(buf.join(' '))}</p></div>`);
    } else if (/^- /.test(line)) {
      const buf = [];
      while (i < lines.length && /^- /.test(lines[i])) buf.push(`<li>${inline(lines[i++].slice(2))}</li>`);
      out.push(`<ul>${buf.join('')}</ul>`);
    } else if (/^\d+\. /.test(line)) {
      const buf = [];
      while (i < lines.length && /^\d+\. /.test(lines[i])) buf.push(`<li>${inline(lines[i++].replace(/^\d+\. /, ''))}</li>`);
      out.push(`<ol>${buf.join('')}</ol>`);
    } else {
      const buf = [];
      while (i < lines.length && lines[i].trim() && !/^(#{2,3} |!\[|> |- |\d+\. )/.test(lines[i])) buf.push(lines[i++]);
      out.push(`<p>${inline(buf.join(' '))}</p>`);
    }
  }
  return out.join('\n');
}

const files = readdirSync(here).filter((f) => /^\d{2}-.+\.md$/.test(f)).sort();
const parsed = files.map((f) => {
  const src = readFileSync(join(here, f), 'utf8').replace(/\r/g, '');
  const m = src.match(/^---\n([\s\S]+?)\n---\n([\s\S]*)$/);
  if (!m) throw new Error(`${f}: missing front matter`);
  return { file: f, meta: JSON.parse(m[1]), body: m[2] };
});
const bySlug = Object.fromEntries(parsed.map((p) => [p.meta.slug, p.meta]));

const articles = [];
const images = [];
const dates = new Set();
for (const { file, meta, body } of parsed) {
  for (const k of ['slug', 'date', 'title', 'seoTitle', 'description', 'tag', 'lede', 'cover', 'figures']) {
    if (!meta[k]) throw new Error(`${file}: missing ${k}`);
  }
  if (dates.has(meta.date)) throw new Error(`${file}: two articles on ${meta.date}`);
  dates.add(meta.date);
  if (meta.seoTitle.length > 60) console.warn(`${file}: seoTitle is ${meta.seoTitle.length} characters`);
  if (meta.description.length > 160) console.warn(`${file}: description is ${meta.description.length} characters`);
  const figures = Object.fromEntries(Object.entries(meta.figures).map(([k, v]) => [k, { ...v }]));
  const content = markdown(body, meta.slug, figures);
  for (const [k, v] of Object.entries(figures)) {
    if (!v.used) throw new Error(`${file}: figure "${k}" is never used`);
    delete v.used;
    images.push({ id: `${meta.slug}-${k}`, kind: 'figure', props: { figure: v } });
  }
  images.push({ id: `${meta.slug}-cover`, kind: 'cover', props: { cover: { title: meta.coverTitle ?? meta.title, ...meta.cover, tone: meta.tag } } });
  const related = (meta.related ?? []).filter((r) => {
    if (r.startsWith('/') || bySlug[r]) return true;
    console.warn(`${file}: related article ${r} does not exist yet, skipped`);
    return false;
  }).map((r) => {
    if (r.startsWith('/')) {
      if (!PAGES[r]) throw new Error(`${file}: unknown related page ${r}`);
      return { href: r, title: PAGES[r][0], text: PAGES[r][1] };
    }
    if (!bySlug[r]) throw new Error(`${file}: unknown related article ${r}`);
    return { href: `/guides/${r}/`, title: bySlug[r].title, text: bySlug[r].description };
  });
  const words = content.replace(/<[^>]+>/g, ' ').split(/\s+/).filter(Boolean).length;
  articles.push({
    slug: meta.slug,
    date: meta.date,
    title: meta.title,
    seoTitle: meta.seoTitle,
    description: meta.description,
    tag: meta.tag,
    lede: meta.lede,
    content,
    related,
    words,
  });
}

writeFileSync(join(outDir, 'articles.json'), JSON.stringify(articles, null, 1) + '\n');
writeFileSync(join(here, 'images.json'), JSON.stringify(images, null, 1) + '\n');
const total = articles.reduce((n, a) => n + a.words, 0);
console.log(`${articles.length} articles (${total} words, ${Math.round(total / Math.max(1, articles.length))} average), ${images.length} images`);
for (const a of articles) console.log(`${a.date}  ${String(a.words).padStart(5)}  ${a.slug}`);
