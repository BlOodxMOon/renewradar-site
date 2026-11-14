import { article, crumbsSchema, pageHero } from './layout.mjs';

// Builds a legal page with numbered sections and a table of contents.
export function legalPage({ path, file, title, h1, description, updated, summary, sections }) {
  const crumbs = [
    ['Home', '/'],
    [h1, null],
  ];
  const body =
    `<div class="note"><p><strong>In short:</strong> ${summary}</p></div>` +
    sections
      .map(([t, html], i) => `<section id="s${i + 1}"><h2><span class="n">${i + 1}</span>${t}</h2>${html}</section>`)
      .join('');
  return {
    path,
    file,
    updated,
    title,
    description,
    schema: [crumbsSchema(crumbs, path)],
    main: `<div class="legal">${pageHero({
      crumbs,
      tag: 'Legal',
      h1,
      meta: `RenewRadar by Pixel DevCode &middot; Last updated ${new Date(updated + 'T00:00:00Z').toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        timeZone: 'UTC',
      })}`,
      cta: false,
    })}${article({ toc: sections.map(([t], i) => [`s${i + 1}`, t]), body })}</div>`,
  };
}
