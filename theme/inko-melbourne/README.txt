INKO Melbourne — Editorial Homepage
==================================

This theme package implements the vertically scrolling INKO homepage in the static Vercel site and the WordPress theme.
The page keeps the black, white and orange identity and uses Don's supplied artist, tattoo, promotion, location and logo assets.

Homepage files
--------------
* elementor/inko-v3-homepage.json — Elementor page export, with the editorial page in one HTML widget.
* homepage-content.html — shared PHP-template fallback used before an Elementor page is imported.
* assets/css/homepage.css and assets/js/homepage.js — responsive layout, mobile navigation, scroll-state and artist carousel.
* assets/css/home-quote.css and assets/js/quote.js — quotation form styling and validation.
* assets/images/ — optimized original images used by the theme. Original source files remain in don-original-assets/.

WordPress setup
---------------
1. Install and activate the INKO theme and Elementor Free.
2. Open Appearance > INKO Setup and import the homepage; assign it as the static front page.
3. The homepage content is available as an Elementor HTML widget. The theme template renders the shared HTML fragment if the
   page still contains the previous slide content, so the redesigned page displays before the import is run.
4. Set up WordPress outbound mail (SMTP or a mail provider). The homepage submits to /wp-json/inko/v1/quote and uses wp_mail;
   email delivery depends on the server's mail configuration.

Homepage sections
-----------------
Hero, studio specialties, original promotions, tattoo work gallery, four artists, location and original studio photos,
Google Reviews link, quotation form and footer. The former full-screen slide takeover is not loaded on this homepage. Legacy
pages that still contain .ik-slide markup retain the existing slide engine.

Reviews
-------
Only a link to Google Maps is shown until Don supplies a confirmed Google Business Profile/reviews URL or an approved embed.
No review excerpts, author names or ratings are fabricated.

Email configuration
-------------------
The Vercel static quotation form continues using the existing /api/quote Resend function and environment settings. WordPress
uses wp_mail; configure SMTP/provider delivery in WordPress before relying on production submissions.
