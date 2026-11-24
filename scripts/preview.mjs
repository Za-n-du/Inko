#!/usr/bin/env node
/**
 * Local preview of the Vercel deployment: serves a static folder and runs the real
 * api/quote.js for POST /api/quote — the same pieces Vercel uses.
 *
 *   node scripts/preview.mjs [--dir site] [--port 4173] [--mock-email]
 *
 * --mock-email   Sets a dummy RESEND_API_KEY and answers Resend's API locally instead of
 *                sending mail; each would-be email is logged (and saved to .build/last-email.json).
 *                Without it, the function behaves exactly as on Vercel (needs RESEND_API_KEY).
 */
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const args = process.argv.slice(2);
const opt = (n, d) => { const i = args.indexOf(`--${n}`); return i >= 0 ? args[i + 1] : d; };
const DIR = path.resolve(ROOT, opt('dir', 'site'));
const PORT = Number(opt('port', 4173));

if (args.includes('--mock-email')) {
	process.env.RESEND_API_KEY = process.env.RESEND_API_KEY || 're_local_mock';
	const realFetch = globalThis.fetch;
	globalThis.fetch = async (url, init = {}) => {
		if (String(url).startsWith('https://api.resend.com/')) {
			const body = JSON.parse(init.body);
			const summary = { to: body.to, subject: body.subject, attachments: (body.attachments || []).map((a) => a.filename), text: body.text };
			fs.mkdirSync(path.join(ROOT, '.build'), { recursive: true });
			fs.writeFileSync(path.join(ROOT, '.build', 'last-email.json'), JSON.stringify(summary, null, 2));
			console.log(`[mock-email] ${summary.subject} → ${summary.to} (${summary.attachments.length} attachment(s))`);
			return Response.json({ id: 'mock-email-id' });
		}
		return realFetch(url, init);
	};
}

const { POST } = await import(pathToFileURL(path.join(ROOT, 'api', 'quote.js')).href);
const TYPES = { '.html': 'text/html; charset=utf-8', '.css': 'text/css', '.js': 'text/javascript', '.json': 'application/json', '.svg': 'image/svg+xml', '.webp': 'image/webp', '.png': 'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.ico': 'image/x-icon', '.woff2': 'font/woff2', '.pdf': 'application/pdf' };

http.createServer(async (req, res) => {
	const url = new URL(req.url, `http://localhost:${PORT}`);
	if (url.pathname === '/api/quote') {
		if (req.method !== 'POST') { res.writeHead(405).end(); return; }
		const chunks = [];
		for await (const c of req) chunks.push(c);
		const request = new Request(url, { method: 'POST', headers: req.headers, body: Buffer.concat(chunks) });
		const out = await POST(request);
		res.writeHead(out.status, Object.fromEntries(out.headers));
		res.end(Buffer.from(await out.arrayBuffer()));
		console.log(`POST /api/quote → ${out.status}`);
		return;
	}
	let file = path.join(DIR, decodeURIComponent(url.pathname));
	if (!file.startsWith(DIR)) { res.writeHead(403).end(); return; }
	if (fs.existsSync(file) && fs.statSync(file).isDirectory()) file = path.join(file, 'index.html');
	if (!fs.existsSync(file)) { res.writeHead(404, { 'Content-Type': 'text/plain' }).end('Not found'); return; }
	res.writeHead(200, { 'Content-Type': TYPES[path.extname(file).toLowerCase()] || 'application/octet-stream' });
	fs.createReadStream(file).pipe(res);
}).listen(PORT, () => console.log(`Preview: http://localhost:${PORT}/  (serving ${path.relative(ROOT, DIR) || '.'})`));
