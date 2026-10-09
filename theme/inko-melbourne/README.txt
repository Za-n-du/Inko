INKO MELBOURNE - V3 FINAL (standalone WordPress theme + native Elementor homepage)
==================================================================================
Folder: inko-melbourne   Version 3.0.0   Needs: WordPress 6.0+, PHP 7.4+, Elementor (free) 3.6+. No Elementor Pro, no other plugin.

This package is completely self-contained. It does NOT use or need V1/V2, any old LocalWP site, old pages, old
Elementor templates or any old plugin. Theme code, CSS, JS, every image and the full Elementor data for all 12 slides
are inside this one folder (assets/ + elementor/). All URLs are built with WordPress functions - nothing is hard-coded.

FRESH LOCALWP INSTALL (about 3 minutes)
---------------------------------------
 1. LocalWP: create a NEW site (any name, PHP 7.4+). Open its WordPress admin.
 2. Plugins > Add New > search "Elementor" > Install > Activate.
 3. Appearance > Themes > Add New > Upload Theme > choose INKO_Melbourne_V3_FINAL.zip > Install > Activate.
 4. Appearance > INKO Setup > keep "Set it as the static front page" ticked > click "Import the V3 homepage".
    (copies all images into the Media Library, adds INKO colours/fonts to Elementor Site Settings, creates ONE page
    "INKO Home" with all 12 slides and makes it the front page)
 5. Open the site URL = the finished V3 homepage. To edit: Pages > INKO Home > Edit with Elementor.
 If "Permalinks" show 404 for other pages: Settings > Permalinks > Save.

THE 12 SLIDES (each a top-level Elementor Container, CSS ID in brackets)
 01 Hero [home]  02 Fine-Line + Realism [specialities]  03 Fine-Line gallery [fine-line]  04 Realism gallery [realism]
 05 Meet Our Artists [artists]  06 Follow Us [follow-us]  07 Our Location [location]  08 Customer Reviews [reviews]
 09 Ink Now, Pay Later [pay-later]  10 Our Promotions [promotions]  11 Get a Quotation [quote]  12 Footer [footer]

WHERE TO EDIT
 Theme (Appearance): menu labels/links = Menus > "INKO Primary"; logos, Book Now = Customize > INKO Melbourne > Header;
 header/menu social icons = Customize > INKO Melbourne > Social links; contact in menu = Customize > Contact.
 Elementor (Pages > INKO Home): every heading, paragraph, button, image, star rating, carousel card, artist card, promotion
 card, payment logo, footer line is a normal widget. Replace images with Choose Image. Add/remove carousel cards in the
 Navigator by duplicating/deleting the card container inside "ik-car__tr". Colours: Site Settings > Global Colors
 (INKO ...). Fonts: Global Fonts. Spacing: slide inner container > Advanced > Padding (Desktop/Tablet/Mobile preset).
 Artists: the four artists' exact roles + descriptions are in the team panel (slide 05 > container "ik-team"); the panel
 opens from the MEET THE TEAM button. The three overlapping photos rotate (arrows on tablet/mobile, click the side photos).

SCROLLING: each slide is a fixed full-screen layer; one wheel/trackpad gesture, swipe or key (Up/Down, PageUp/PageDown,
 Space, Home/End) moves exactly one slide with a ~0.65s eased move; input is ignored while a move runs; Left/Right and
 sideways trackpad swipes drive the visible gallery. In the Elementor editor the engine is OFF (slides become a normal stack).

TROUBLESHOOTING
 - "Elementor is not active": install/activate it, then run Appearance > INKO Setup again.
 - Images missing after import: make sure wp-content/uploads is writable, then "Reset homepage to V3".
 - Colours/fonts not updating: Elementor > Tools > Regenerate CSS & Data; clear caching plugins.
 - Caching/minify plugins that delay or combine JS can break the slides: exclude /themes/inko-melbourne/assets/js/.

NOTES ON CONTENT (please read)
 - HERO and QUOTATION tattoo-machine photos and the FOOTER studio background were not supplied as separate files; they were
   cropped from your approved reference slides (hero/quotation/footer), so they are lower resolution than a new photo shoot.
   Replace them in Elementor with higher-resolution originals whenever you have them (hero image, quotation image, footer bg).
 - Promotions use the five original flyers; old prices ($2500 / $3500) were read from the printed flyers. The locked
   reference mock-ups show $1600/$2500 - please confirm which is correct. Flyer text repeats inside the card panel.
 - Payment logos were cut from the approved Slide 8 reference (no official files supplied). Payment logos stay on ONE
   horizontal row on every device (as requested).
 - The hero has no "Watch Video" button (no video was supplied). TikTok/YouTube/Threads URLs were not supplied ("#").
 - Artist photos are unlabeled in the supplied pack, so photos are not tied to names; the names/roles/descriptions are in the team panel.
