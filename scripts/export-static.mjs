#!/usr/bin/env node
/**
 * Static export of the inko-custom WordPress homepage for Vercel.
 *
 * inko-custom (the WordPress theme) is the single source of truth. This script:
 *   1. syncs the theme from the Local WordPress site into theme/inko-custom/ (repo copy of the source),
 *   2. renders the homepage from the running Local site,
 *   3. keeps only the theme's own assets (drops WordPress core/plugin CSS, JS and widgets),
 *   4. points the quotation form at the Vercel function /api/quote,
 *   5. copies the theme assets next to the page,
 *   6. verifies that every local reference resolves and no WordPress/plugin traces remain.
 *
 * Usage:
 *   node scripts/export-static.mjs [--out site] [--source http://inko-melbourne.local]
 *                                  [--theme "<path to Local inko-custom theme>"] [--no-sync]
 * Requires the Local WordPress site to be running. No dependencies (Node 18+).
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const args = process.argv.slice(2);
const opt = (name, def) => { const i = args.indexOf(`--${name}`); return i >= 0 ? args[i + 1] : def; };
const OUT = path.resolve(ROOT, opt('out', 'site'));
const SOURCE = opt('source', 'http://inko-melbourne.local').replace(/\/$/, '');
const THEME_SRC = opt('theme', path.join(process.env.USERPROFILE || process.env.HOME || '', 'Local Sites', 'inko-melbourne', 'app', 'public', 'wp-content', 'themes', 'inko-custom'));
const THEME_REPO = path.join(ROOT, 'theme', 'inko-custom');
const THEME_URL = '/wp-content/themes/inko-custom';
const SYNC = !args.includes('--no-sync');

const fail = (msg) => { console.error(`\n✖ ${msg}`); process.exit(1); };
const log = (msg) => console.log(`• ${msg}`);

// 1. Sync the theme source into the repo (never touches the theme's own .git).
if (SYNC) {
	if (!fs.existsSync(path.join(THEME_SRC, 'style.css'))) fail(`Theme not found at ${THEME_SRC} (pass --theme).`);
	fs.cpSync(THEME_SRC, THEME_REPO, { recursive: true, force: true, filter: (src) => !src.split(path.sep).includes('.git') });
	log(`theme synced → ${path.relative(ROOT, THEME_REPO)}`);
}

// 2. Render the homepage.
let html;
try {
	const res = await fetch(`${SOURCE}/`, { headers: { 'User-Agent': 'inko-static-export' } });
	if (!res.ok) fail(`${SOURCE}/ returned HTTP ${res.status}`);
	html = await res.text();
} catch (e) {
	fail(`Could not reach ${SOURCE}/ — is the Local site running? (${e.message})`);
}
if (!html.includes('class="stack"')) fail('Rendered page is not the inko-custom homepage.');
log(`rendered ${SOURCE}/ (${Math.round(html.length / 1024)} KB)`);

// 3. Keep only the theme's own output.
const keepScript = (tag) => {
	const open = tag.slice(0, tag.indexOf('>') + 1);
	if (/src=["'][^"']*\/wp-content\/themes\/inko-custom\//.test(open)) return true; // theme script file
	if (/type=["']application\/ld\+json["']/.test(open)) return true; // structured data
	return !/\bsrc=/.test(open) && !/type=/.test(open) && tag.includes("classList.add('js')"); // the theme's js flag
};
const keepLink = (tag) => {
	const rel = (tag.match(/rel=["']([^"']+)["']/) || [])[1] || '';
	const href = (tag.match(/href=["']([^"']+)["']/) || [])[1] || '';
	const id = (tag.match(/id=["']([^"']+)["']/) || [])[1] || '';
	if (rel === 'stylesheet') return id.startsWith('inko-'); // the theme's own CSS + its Google Fonts

	if (rel === 'preconnect') return /fonts\.(googleapis|gstatic)\.com/.test(href);
	if (rel === 'preload') return /as=["']image["']/.test(tag);
	if (rel === 'icon' || rel === 'apple-touch-icon') return true;
	return false;
};

// Body: everything WordPress/plugins print after the theme's lightbox dialog is dropped.
const lb = html.indexOf('</dialog>', html.indexOf('class="lightbox"'));
if (lb < 0) fail('Lightbox dialog not found; cannot locate the end of the theme markup.');
const tail = html.slice(lb + '</dialog>'.length);
const themeScripts = (tail.match(/<script\b[^>]*>[\s\S]*?<\/script>/g) || []).filter(keepScript).join('\n');
html = html.slice(0, lb + '</dialog>'.length) + '\n' + themeScripts + '\n</body>\n</html>\n';

html = html
	.replace(/<script\b[^>]*>[\s\S]*?<\/script>/g, (t) => (keepScript(t) ? t : ''))
	.replace(/<link\b[^>]*>/g, (t) => (keepLink(t) ? t : ''))
	.replace(/<style\b[^>]*>[\s\S]*?<\/style>/g, '')
	.replace(/<meta name="generator"[^>]*>/g, '')
	.replace(/<body class="[^"]*"/, '<body class="home inko-home"'); // drop plugin body classes

// 4. Quotation form → Vercel function (field names already match api/quote.js).
html = html
	.replace(/<form class="quote-form"([^>]*?) action="[^"]*" data-static-action="([^"]+)"/, '<form class="quote-form"$1 action="$2" data-async')
	.replace(/<input type="hidden" name="action" value="inko_quote">/, '')
	.replace(/<input type="hidden" id="inko_quote_nonce"[^>]*>/, '')
	.replace(/<input type="hidden" name="_wp_http_referer"[^>]*>/, '');

// Root-relative URLs (served from the domain root on Vercel).
const host = SOURCE.replace(/^https?:/, '');
html = html
	.split(`https:${host}`).join('').split(`http:${host}`).join('')
	.split(`https:${host.replace(/\//g, '\\/')}`).join('').split(`http:${host.replace(/\//g, '\\/')}`).join('')
	.replace(/href=""/g, 'href="/"')
	.replace(/\n\s*\n+/g, '\n');

// 5. Write the page and copy the theme assets.
fs.mkdirSync(OUT, { recursive: true });
fs.writeFileSync(path.join(OUT, 'index.html'), html);
const assetsSrc = path.join(SYNC ? THEME_REPO : THEME_SRC, 'assets');
const assetsOut = path.join(OUT, 'wp-content', 'themes', 'inko-custom', 'assets');
fs.cpSync(assetsSrc, assetsOut, { recursive: true, force: true });
// Media-library files the page uses (e.g. the custom logo set in the Customizer).
const WP_ROOT = path.resolve(THEME_SRC, '..', '..', '..');
let uploads = 0;
for (const m of html.matchAll(/\/wp-content\/uploads\/[^"'\s,)?]+/g)) {
	const rel = decodeURIComponent(m[0]);
	const from = path.join(WP_ROOT, rel);
	const to = path.join(OUT, rel);
	if (fs.existsSync(from) && !fs.existsSync(to)) { fs.mkdirSync(path.dirname(to), { recursive: true }); fs.copyFileSync(from, to); uploads++; }
}
if (uploads) log(`copied ${uploads} media-library file(s)`);
log(`wrote ${path.relative(ROOT, path.join(OUT, 'index.html'))} (${Math.round(html.length / 1024)} KB) + theme assets`);

// 6. Verify.
const problems = [];
for (const bad of ['inko-melbourne.local', '/wp-admin', '/wp-json', 'wp-includes', 'siteleads', 'spectra', 'elementor', 'inko_quote_nonce']) {
	if (html.toLowerCase().includes(bad)) problems.push(`page still contains "${bad}"`);
}
const refs = new Set();
for (const m of html.matchAll(/(?:src|href)="(\/[^"#?]*)/g)) refs.add(m[1]);
for (const m of html.matchAll(/srcset="([^"]+)"/g)) m[1].split(',').forEach((c) => refs.add(c.trim().split(/\s+/)[0].split('?')[0]));
for (const m of html.matchAll(/url\((\/[^)?'"]+)/g)) refs.add(m[1]);
const css = fs.readFileSync(path.join(assetsOut, 'css', 'main.css'), 'utf8');
for (const m of css.matchAll(/url\(["']?(?!data:|https?:)([^)"'?#]+)/g)) refs.add(path.posix.join(`${THEME_URL}/assets/css`, m[1]));
let checked = 0;
for (const r of refs) {
	if (!r.startsWith('/') || r === '/' || r.startsWith('/api/') || r.startsWith('//')) continue;
	checked++;
	if (!fs.existsSync(path.join(OUT, decodeURIComponent(r)))) problems.push(`missing file for ${r}`);
}
if (!/<form class="quote-form"[^>]*action="\/api\/quote"[^>]*data-async/.test(html)) problems.push('quote form is not wired to /api/quote');
for (const id of ['home', 'about', 'promotions', 'artists', 'studio', 'finelineshowcase', 'realism-showcase', 'reviews', 'quote', 'pay-later', 'contact']) {
	if (!html.includes(`id="${id}"`)) problems.push(`section #${id} missing`);
}
if (problems.length) fail(`Export verification failed:\n  - ${problems.join('\n  - ')}`);
log(`verified: ${checked} local references resolve, 11 sections present, form → /api/quote, no WordPress/plugin traces`);
console.log('\n✔ Static export complete.');
