// Builds the static site into the repository root. Run: node _src/build.mjs
import { createHash } from 'node:crypto';
import { mkdirSync, readFileSync, readdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { layout, SITE } from './layout.mjs';

const here = dirname(fileURLToPath(import.meta.url));
const root = join(here, '..');

const hash = (file) => createHash('md5').update(readFileSync(join(root, file))).digest('hex').slice(0, 8);
const assets = {
  css: hash('assets/site.css'),
  js: hash('assets/site.js'),
  '/assets/calc.js': hash('assets/calc.js'),
};

const pages = [];
for (const name of readdirSync(join(here, 'pages')).sort()) {
  if (!name.endsWith('.mjs')) continue;
  const mod = await import(pathToFileURL(join(here, 'pages', name)).href);
  pages.push(mod.default);
}

for (const page of pages) {
  const out = page.file || (page.path === '/' ? 'index.html' : page.path.replace(/^\//, '') + 'index.html');
  const target = join(root, out);
  mkdirSync(dirname(target), { recursive: true });
  writeFileSync(target, layout(page, assets));
  console.log('wrote', out);
}

const indexed = pages.filter((p) => !p.noindex);
const sitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${indexed.map((p) => `  <url><loc>${SITE}${p.path}</loc><lastmod>${p.updated}</lastmod></url>`).join('\n')}
</urlset>
`;
writeFileSync(join(root, 'sitemap.xml'), sitemap);
writeFileSync(join(root, 'robots.txt'), `User-agent: *\nAllow: /\nDisallow: /_src/\n\nSitemap: ${SITE}/sitemap.xml\n`);
console.log(`sitemap: ${indexed.length} pages`);
