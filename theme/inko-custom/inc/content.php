<?php
/**
 * INKO Melbourne — original business content.
 *
 * Every string below is copied verbatim from the original website
 * (https://www.inkomelbourne.com.au/, captured 23 Nov 2026) or, for the
 * promotion flyers, transcribed exactly from the client-supplied artwork.
 * Edit here only when the client changes their real content: templates read
 * from this single source, so nothing business-related is hard-coded twice.
 *
 * Interface labels (navigation names, "Open in Google Maps", form field
 * help) are not business content and live in the templates.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the content array (or one top-level key of it).
 *
 * @param string|null $key Optional top-level key.
 * @return mixed
 */
function inko_content( $key = null ) {
	static $c = null;

	if ( null === $c ) {
		$c = array(

			'studio'     => array(
				'name'          => 'INKO Melbourne Tattoo Studio',
				'brand'         => 'INKO Melbourne',
				'address_1'     => '1c Cookson Street',
				'address_2'     => 'Camberwell, VIC 3142',
				'email'         => 'inko.melbourne@gmail.com',
				'phone_display' => '(+61) 481-082077',
				'phone_tel'     => '+61481082077',
				// Functional links built from the published address (no new facts).
				'maps_url'      => 'https://www.google.com/maps/search/?api=1&query=INKO+Melbourne%2C+1C+Cookson+Street%2C+Camberwell+VIC+3142',
				'directions'    => 'https://www.google.com/maps/dir/?api=1&destination=INKO+Melbourne%2C+1C+Cookson+Street%2C+Camberwell+VIC+3142',
			),

			// Client: Instagram and Facebook only (YouTube, TikTok and Threads removed 24 Nov 2026).
			'social'     => array(
				'instagram' => array( 'label' => 'Instagram', 'handle' => '@inko_melbourne', 'url' => 'https://www.instagram.com/inko_melbourne' ),
				'facebook'  => array( 'label' => 'Facebook', 'handle' => 'INKO Melbourne', 'url' => 'https://www.facebook.com/share/16p5Pb5HkR/?mibextid=wwXIfr' ),
			),

			'hero'       => array(
				// Client-approved Slide 01 introduction (supplied 23 Nov 2026). Differs from the
				// original site's wording, which slide 02 keeps verbatim.
				'intro'   => 'At INKO, we specialise in fine-line and realism tattoos, combining delicate detail with bold storytelling.',
				// Slide 01 social links (client: Instagram and Facebook only).
				'social'  => array( 'instagram', 'facebook' ),
				'title_1' => 'BALI',
				'title_2' => 'PRICES',
				'text'    => 'and get quality work done by your local artists.',
				'cta'     => 'See Promos',
			),

			'intro'      => array(
				// Client-approved wording (23 Nov 2026), hyphen removed at the client's request.
				// Original site: "At INKO, we specialise in Fine-line and Realism tattoos - delicate detail, bold storytelling."
				'before'   => 'At INKO, we specialise in ',
				'fineline' => 'fine-line',
				'and'      => ' and ',
				'realism'  => 'realism',
				'after'    => ' tattoos, combining delicate detail with bold storytelling.',
			),

			'styles'     => array(
				'fineline' => 'Fine Line', // Client: decorative hyphens removed from visible labels.
				'realism'  => 'Realism',
				'tattoos'  => 'Tattoos',
			),

			'promotions' => array(
				'title' => 'OUR PROMOTIONS',
				// Transcribed from the client's promotion flyers (titles, sittings, prices as printed).
				'items' => array(
					array( 'img' => 'promo-full-chest', 'title' => 'Full Chest', 'sittings' => 'In one sitting', 'was' => '$2500', 'now' => '$1000' ),
					array( 'img' => 'promo-full-upper-back', 'title' => 'Full Upper Back', 'sittings' => 'In one sitting', 'was' => '$2000', 'now' => '$1000' ),
					array( 'img' => 'promo-full-outer-sleeve', 'title' => 'Full Outer Sleeve', 'sittings' => 'Two sittings', 'was' => '$2500', 'now' => '$1500' ),
					array( 'img' => 'promo-floral-full-sleeve', 'title' => 'Floral Full Sleeve', 'sittings' => 'Two sittings', 'was' => '$3500', 'now' => '$1500' ),
					array( 'img' => 'promo-full-forearm', 'title' => 'Full Forearm', 'sittings' => 'Two sittings', 'was' => '$2500', 'now' => '$1500' ),
					array( 'img' => 'promo-front-lower-leg', 'title' => 'Front Lower Leg', 'sittings' => 'Two sittings', 'was' => '$3000', 'now' => '$1500' ),
					array( 'img' => 'promo-full-back', 'title' => 'Full Back', 'sittings' => 'Three sittings', 'was' => '$5000', 'now' => '$2900' ),
				),
			),

			/*
			 * Artists — name, alias, role and bio are the original website's (verified 25 Nov 2026).
			 * 'specialties' repeat the styles each artist's own role/bio names; nothing added.
			 * 'gallery' is the studio gallery for that style (studio work, not a personal portfolio).
			 * NOT published anywhere yet — leave empty until the client supplies them:
			 *   'instagram'  => personal Instagram URL (shown as "Instagram" + used for "View Portfolio")
			 *   'experience' => years of experience text
			 */
			'artists'    => array(
				'title' => 'Meet Our Artists',
				'items' => array(
					array(
						'img'         => 'artist-swan',
						'name'        => 'Swan',
						'aka'         => 'BunnyGirlInk',
						// Client: unnecessary dashes removed from captions (original: "Fine-Line Artist – Floral & Realism").
						'role'        => 'Fine Line Artist, Floral & Realism',
						'specialties' => array( 'Fine Line', 'Floral', 'Soft Realism' ),
						'gallery'     => 'finelineshowcase',
						'instagram'   => '',
						'experience'  => '',
						'bio'         => 'Specialising in fine line floral and soft realism tattoos, we create clean, delicate designs with subtle depth—timeless, minimal, and effortlessly elegant.',
					),
					array(
						'img'         => 'artist-elmundo',
						'name'        => 'Elmundo',
						'aka'         => 'ELM J',
						'role'        => 'Realism Artist',
						'specialties' => array( 'Realism', 'Fine Line Realism' ),
						'gallery'     => 'realism-showcase',
						'instagram'   => '',
						'experience'  => '',
						'bio'         => 'Specialist in Realism and Fine Line Realism tattoos, executing flawless detail and understated depth. Committed to crafting timeless designs that ensure each piece is uniquely personal.',
					),
					array(
						'img'         => 'artist-yinkspire',
						'name'        => 'Yinkspire',
						'aka'         => 'Yosufi',
						'role'        => 'Realism Artist',
						'specialties' => array( 'Realism' ),
						'gallery'     => 'realism-showcase',
						'instagram'   => '',
						'experience'  => '',
						'bio'         => 'Specialises in detailed realism tattoos with striking depth and precision, creating lifelike designs that stand the test of time.',
					),
					array(
						'img'         => 'artist-don',
						'name'        => 'Don',
						'aka'         => 'OIDONNY',
						'role'        => 'Founder, INKO', // Original: "Founder - INKO".
						'specialties' => array( 'Realism', 'Fine Line Realism' ),
						'gallery'     => 'realism-showcase',
						'instagram'   => '',
						'experience'  => '',
						'bio'         => 'Specialises Realism and Fine Line realism tattoos with flawless detail and subtle depth. Dedicated to timeless, true-to-life designs that make every piece unique.',
					),
				),
			),

			'location'   => array(
				'title'      => 'INKO Melbourne Tattoo Studio',
				'paragraphs' => array(
					'Tucked into the creative pocket of Cookson Street, INKO Melbourne is more than just a tattoo studio — it’s a destination for self-expression. Surrounded by tram and bus lines and just a quick 2-minute walk from Camberwell Station, we’re easy to find and even easier to vibe with.',
					'Step into our studio and you’ll find clean lines, calming energy, and artists who live for the craft. Whether you\'re after fine line elegance or hyper-realistic depth, INKO is where your vision becomes wearable art.',
				),
				// Client: "Sweep to locate us." replaced with "GET DIRECTIONS".
				'directions' => 'Get Directions',
			),

			'follow'     => array(
				'title' => 'Follow Us',
			),

			'reviews'    => array(
				// Original heading "Trusted by Our Customers – Read Google Reviews", split at the dash.
				'title'  => 'Trusted by Our Customers',
				'link'   => 'Read Google Reviews',
				'rating' => '5.0',
				'items'  => array(
					array( 'name' => 'Anneliese Moore', 'text' => '10 tattoos in one session and couldn’t be happier! The artist was patient, professional, and really cares about her work. Amazing fine line detail, great prices, and overall an incredible experience. Highly recommend!' ),
					array( 'name' => 'James Bradfield', 'text' => 'Got my Chicano-style tattoo today after a year of research, and he absolutely delivered. The quality speaks for itself (see photo). Pricing is fair—you get what you pay for, and he’s the best at what he does!' ),
					array( 'name' => 'Isuru Chathuranga', 'text' => 'Absolutely loved my experience! Talented, professional artists who made me feel comfortable throughout. The detail is incredible and my tattoo came out even better than I imagined. Highly recommend!' ),
					array( 'name' => 'Jennifer Lean', 'text' => 'Amazing first tattoo experience! Super gentle, made sure I was 100% comfortable, and even provided water and snacks. Highly recommend Inko for quality tattoos and the best experience! I will make sure to come back.' ),
				),
			),

			'pay_later'  => array(
				'title' => 'Ink Now, Pay Later.',
				'logos' => array(
					array( 'file' => 'pay-afterpay.webp', 'label' => 'Afterpay' ),
					array( 'file' => 'pay-klarna.webp', 'label' => 'Klarna' ),
					array( 'file' => 'pay-zip.webp', 'label' => 'Zip' ),
				),
			),

			'quote'      => array(
				'title'   => 'Get a Quotation.',
				'text'    => 'Have a tattoo idea in mind? Send us your design and we’ll get back to you with a personalised quote.',
				'cta'     => 'Get a Quotation',
				'contact' => 'Contact Us',
				// Field help as written on the original Contact_us form.
				'details_help' => array( 'Size', 'Placement', 'Color Preference' ),
			),

			'footer'     => array(
				'location_label' => 'LOCATION',
				'contact_label'  => 'CONTACT',
			),
		);
	}

	if ( null === $key ) {
		return $c;
	}

	return isset( $c[ $key ] ) ? $c[ $key ] : null;
}
