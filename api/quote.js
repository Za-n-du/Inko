// Vercel serverless function: POST /api/quote
// Receives the quotation form (multipart/form-data), validates it, and emails it to the studio through Resend
// (https://resend.com) with the client's reference file attached. Secrets come from environment variables only.

const RESEND_URL = 'https://api.resend.com/emails';
const DEFAULT_TO = 'inko.melbourne@gmail.com'; // studio contact address published on the site
const DEFAULT_FROM = 'INKO Melbourne Website <onboarding@resend.dev>';

// Vercel rejects request bodies over 4.5 MB, so the file limit leaves room for the text fields.
export const MAX_FILE_BYTES = 4 * 1024 * 1024;

// Allowed reference files, identified by their content (magic bytes), not by the name or type the browser sends.
const FILE_KINDS = [
  { ext: 'jpg', mime: 'image/jpeg', exts: ['jpg', 'jpeg'], test: (b) => b[0] === 0xff && b[1] === 0xd8 && b[2] === 0xff },
  { ext: 'png', mime: 'image/png', exts: ['png'], test: (b) => ascii(b, 1, 4) === 'PNG' && b[0] === 0x89 },
  { ext: 'webp', mime: 'image/webp', exts: ['webp'], test: (b) => ascii(b, 0, 4) === 'RIFF' && ascii(b, 8, 12) === 'WEBP' },
  { ext: 'heic', mime: 'image/heic', exts: ['heic', 'heif'], test: (b) => ascii(b, 4, 8) === 'ftyp' && ['heic', 'heix', 'hevc', 'heim', 'heis', 'mif1', 'msf1'].includes(ascii(b, 8, 12)) },
  { ext: 'pdf', mime: 'application/pdf', exts: ['pdf'], test: (b) => ascii(b, 0, 5) === '%PDF-' },
];

const ascii = (b, s, e) => String.fromCharCode(...b.subarray(s, e));
const EMAIL_RE = /^[^\s@<>()",;:]+@[^\s@<>()",;:]+\.[^\s@<>()",;:]{2,}$/;
const PHONE_RE = /^[+()0-9 .-]{6,25}$/;
const ID_RE = /^[A-Za-z0-9-]{8,64}$/;

const json = (status, body) => Response.json(body, { status, headers: { 'Cache-Control': 'no-store' } });
const esc = (s) => s.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const text = (form, key) => {
  const v = form.get(key);
  return typeof v === 'string' ? v.trim() : '';
};

export function validate(fields) {
  const errors = {};
  const { firstName, lastName, email, phone, details } = fields;
  if (!firstName) errors.firstName = 'Please enter your first name.';
  else if (firstName.length > 80) errors.firstName = 'First name is too long.';
  if (!lastName) errors.lastName = 'Please enter your last name.';
  else if (lastName.length > 80) errors.lastName = 'Last name is too long.';
  if (!email) errors.email = 'Please enter your email address.';
  else if (email.length > 254 || !EMAIL_RE.test(email)) errors.email = 'Please enter a valid email address.';
  if (phone && !PHONE_RE.test(phone)) errors.phone = 'Please enter a valid phone number.';
  if (!details) errors.details = 'Please describe the size, placement and color preference.';
  else if (details.length < 10) errors.details = 'Please add a little more detail (at least 10 characters).';
  else if (details.length > 5000) errors.details = 'Details are too long (5000 characters max).';
  return errors;
}

export async function checkFile(file) {
  if (!file || typeof file === 'string' || file.size === 0) return { file: null };
  if (file.size > MAX_FILE_BYTES) return { error: 'The file is larger than 4 MB. Please upload a smaller file.' };
  const bytes = new Uint8Array(await file.arrayBuffer());
  const kind = FILE_KINDS.find((k) => k.test(bytes));
  const ext = (file.name.split('.').pop() || '').toLowerCase();
  if (!kind || !kind.exts.includes(ext)) return { error: 'Please upload a JPG, PNG, WEBP, HEIC or PDF file.' };
  const base = file.name.slice(0, file.name.length - ext.length - 1).replace(/[^A-Za-z0-9_-]+/g, '_').slice(0, 80) || 'reference';
  return { file: { filename: `${base}.${kind.ext}`, content: Buffer.from(bytes).toString('base64'), size: bytes.length, mime: kind.mime } };
}

function buildEmail(f, file) {
  const name = `${f.firstName} ${f.lastName}`;
  const rows = [['Name', name], ['Email', f.email], ['Phone', f.phone || 'Not provided'], ['Reference file', file ? `${file.filename} (${Math.round(file.size / 1024)} KB, attached)` : 'None']];
  const html =
    `<h2 style="font-family:Arial,sans-serif">New tattoo quotation request</h2>` +
    `<table style="font-family:Arial,sans-serif;border-collapse:collapse">` +
    rows.map(([k, v]) => `<tr><td style="padding:4px 12px 4px 0;color:#666">${k}</td><td style="padding:4px 0">${esc(v)}</td></tr>`).join('') +
    `</table><h3 style="font-family:Arial,sans-serif">Details (size, placement, color preference)</h3>` +
    `<p style="font-family:Arial,sans-serif;white-space:pre-wrap">${esc(f.details)}</p>` +
    `<p style="font-family:Arial,sans-serif;color:#888;font-size:12px">Sent from the quotation form on the INKO Melbourne website. Reply to this email to answer the client.</p>`;
  const txt = `New tattoo quotation request\n\n${rows.map(([k, v]) => `${k}: ${v}`).join('\n')}\n\nDetails (size, placement, color preference):\n${f.details}\n`;
  return { subject: `Tattoo quotation request from ${name}`, html, text: txt };
}

export async function POST(request) {
  const apiKey = process.env.RESEND_API_KEY;
  if (!apiKey) {
    console.error('quote: RESEND_API_KEY is not set');
    return json(503, { ok: false, error: 'The quotation service is not available right now. Please email us instead.' });
  }

  if (Number(request.headers.get('content-length') || 0) > MAX_FILE_BYTES + 256 * 1024) {
    return json(413, { ok: false, error: 'The upload is too large. Please attach a file under 4 MB.' });
  }

  let form;
  try {
    form = await request.formData();
  } catch {
    return json(400, { ok: false, error: 'The form could not be read. Please try again.' });
  }

  // Honeypot: real visitors never see or fill this field.
  if (text(form, 'website')) return json(200, { ok: true });

  const fields = {
    firstName: text(form, 'firstName'),
    lastName: text(form, 'lastName'),
    email: text(form, 'email'),
    phone: text(form, 'phone'),
    details: text(form, 'details'),
  };
  const errors = validate(fields);
  const fileCheck = await checkFile(form.get('file'));
  if (fileCheck.error) errors.file = fileCheck.error;
  if (Object.keys(errors).length) {
    return json(400, { ok: false, error: 'Please check the highlighted fields.', fields: errors });
  }

  const message = buildEmail(fields, fileCheck.file);
  const submissionId = text(form, 'submissionId');
  const headers = { Authorization: `Bearer ${apiKey}`, 'Content-Type': 'application/json' };
  // Resend ignores a repeated request with the same key, so a retried submission is not emailed twice.
  if (ID_RE.test(submissionId)) headers['Idempotency-Key'] = `quote-${submissionId}`;

  let res;
  try {
    res = await fetch(RESEND_URL, {
      method: 'POST',
      headers,
      body: JSON.stringify({
        from: process.env.QUOTE_FROM_EMAIL || DEFAULT_FROM,
        to: [process.env.QUOTE_TO_EMAIL || DEFAULT_TO],
        reply_to: fields.email,
        ...message,
        attachments: fileCheck.file ? [{ filename: fileCheck.file.filename, content: fileCheck.file.content }] : undefined,
      }),
    });
  } catch (err) {
    console.error('quote: could not reach Resend', err);
    return json(502, { ok: false, error: 'We could not send your request right now. Please try again in a minute.' });
  }

  const result = await res.json().catch(() => ({}));
  if (!res.ok || !result.id) {
    console.error('quote: Resend rejected the email', res.status, result && (result.message || result.name));
    return json(502, { ok: false, error: 'We could not send your request right now. Please try again in a minute.' });
  }
  return json(200, { ok: true });
}
