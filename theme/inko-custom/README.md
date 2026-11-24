# INKO Melbourne Custom theme (v2.0.0)

11-slide layered homepage for INKO Melbourne Tattoo Studio.

## Where things live

| What | File |
|---|---|
| All business text (verbatim from inkomelbourne.com.au), prices, links | `inc/content.php` |
| Slide templates (01 hero … 11 contact + footer) | `template-parts/sections/` |
| Slide order and labels | `inko_slides()` in `inc/template-tags.php` |
| Image sizes (responsive WebP, generated from the client originals) | `assets/images/`, listed in `inko_image_manifest()` |
| Styles | `assets/css/main.css` |
| Behaviour (stacking, reveals, parallax, tilt, carousels, lightbox, form) | `assets/js/main.js` |
| Quotation form handler (emails the studio with the file attached) | `inc/quote-form.php` |

## Homepage setup

The homepage is `front-page.php`. The front page is set (Settings → Reading) to the empty page
"Home (INKO theme)". The previous Elementor Canvas homepage ("INKO Home", ID 1767) is untouched:
choose it again under Settings → Reading to switch back.

## Elementor

Pages built with Elementor render full width through `page.php`; Elementor Canvas and
Full Width templates work as usual.

## Quotation emails

Sent with `wp_mail()` to the studio address (filter: `inko_quote_recipient`). On a live host,
install an SMTP plugin (e.g. WP Mail SMTP) so messages are delivered reliably.

## Image rules

Only client-supplied photos are used. Artist photos are matched by the client's file names
(Swan.PNG, Elmundo.PNG, Yinkspire.PNG, Don.JPEG), which agree with the live site.
