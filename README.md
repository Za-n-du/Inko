# INKO Melbourne V3

Website for INKO Melbourne Tattoo Studio: a single-page site made of 12 full-screen stacked slides.

This repository contains two things:

| Folder | What it is | How it runs |
|---|---|---|
| `site/` | Static HTML export of the live homepage (generated with the Simply Static plugin). | Any static host (Vercel, Netlify, GitHub Pages, etc.). No PHP or database. |
| `theme/inko-melbourne/` | The custom WordPress theme source (PHP templates, CSS, JS, images, Elementor homepage JSON). | Requires WordPress hosting (PHP 7.4+, MySQL) with Elementor installed. |

## Homepage slides

1. Hero
2. Intro
3. Fine Line gallery
4. Realism gallery
5. Artists
6. Follow Us
7. Location
8. Reviews
9. Payment options (Afterpay / Klarna / Zip)
10. Promotions
11. Quote request
12. Footer

## Deploying the static site to Vercel

`vercel.json` tells Vercel to serve the `site/` folder as-is: there is no install or build step.

1. Import this repository at https://vercel.com/new.
2. Leave **Framework Preset** as **Other**, then click **Deploy**.

The pages use root-relative URLs (`/wp-content/...`), so the site must be served from the domain root (a normal Vercel domain or a custom domain both work).

## Quotation form

The **GET A QUOTATION** button on the quote slide opens `/quote/` (`site/quote/`). The form posts to `/api/quote`
(`api/quote.js`), a Vercel serverless function that validates the request, checks the uploaded file's real type
(JPG, PNG, WEBP, HEIC or PDF, max 4 MB), and emails it to the studio through [Resend](https://resend.com) with the
file attached and **Reply-To** set to the client. The page shows "Thank you" only after Resend accepts the email.

Setup (required before the form can deliver anything):

1. Create a free Resend account **using the studio email** (`inko.melbourne@gmail.com`). Without a verified domain,
   Resend's test sender can only deliver to the account owner's address.
2. In Resend, open **API Keys → Create API Key** (permission: *Sending access*) and copy the key.
3. In Vercel, open the project's **Settings → Environment Variables**, add `RESEND_API_KEY` with that key for
   *Production* (and *Preview* if wanted), then redeploy.
4. Send one test request from the live `/quote/` page and confirm it arrives in the studio inbox.

Optional variables are listed in `.env.example` (`QUOTE_TO_EMAIL`, `QUOTE_FROM_EMAIL`). To send from an address like
`quotes@inkomelbourne.com.au`, verify that domain in Resend and set `QUOTE_FROM_EMAIL`.

The WordPress theme does not include this form. On a WordPress host, create a page with the slug `quote` using a form
plugin (for example WPForms or Contact Form 7 with a file-upload field) plus an SMTP plugin for reliable delivery, then
point the quote slide's button at `/quote/` in Elementor.

## Using the WordPress theme

1. Zip the `theme/inko-melbourne` folder and upload it in **Appearance → Themes → Add New → Upload Theme**.
2. Install and activate the **Elementor** plugin.
3. Run **Appearance → INKO Setup** to create the Elementor homepage and import the images.

## Updating the static site

1. Edit the site in WordPress (LocalWP).
2. Re-export with **Simply Static → Generate**.
3. Replace the contents of `site/` with the new export, then commit and push. Vercel redeploys automatically.

## Not included (intentionally)

- `wp-config.php` (database credentials and security salts)
- WordPress core, Elementor, and plugin source code (install these from wordpress.org)
- The database, LocalWP server config, logs, and caches
