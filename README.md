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
