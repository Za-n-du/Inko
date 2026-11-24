<?php
/**
 * Template helpers: responsive images, icons, section scaffolding.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generated image sizes (assets/images/{name}-{width}.webp).
 * Each family lists [width, height] pairs, smallest first.
 *
 * @return array<string, array<int, array{0:int,1:int}>>
 */
function inko_image_manifest() {
	static $m = null;

	if ( null !== $m ) {
		return $m;
	}

	$landscape = array( array( 640, 427 ), array( 1280, 853 ) );
	$promo     = array( array( 520, 650 ), array( 1000, 1250 ) );
	$realism   = array( array( 640, 853 ), array( 1100, 1467 ) );
	$square    = array( array( 480, 480 ), array( 900, 900 ) );

	$m = array(
		'studio-hero'      => array( array( 720, 1080 ), array( 1200, 1800 ) ),
		// Full-screen hero slides (client originals: IMG_1467 back piece, Don.JPEG, IMG_4893 sleeve).
		'hero-slide-1'     => array( array( 800, 1067 ), array( 1600, 2133 ) ),
		'hero-slide-2'     => array( array( 800, 1200 ), array( 1600, 2400 ) ),
		'hero-slide-3'     => array( array( 800, 1067 ), array( 1600, 2133 ) ),
		'artist-swan'      => $square,
		'artist-elmundo'   => $square,
		'artist-yinkspire' => $square,
		'artist-don'       => array( array( 480, 720 ), array( 900, 1350 ) ),
		'fineline-01'      => array( array( 640, 730 ), array( 1100, 1256 ) ),
		'fineline-02'      => array( array( 640, 960 ), array( 1024, 1536 ) ),
	);

	for ( $i = 1; $i <= 8; $i++ ) {
		$m[ sprintf( 'studio-%02d', $i ) ] = $landscape;
	}
	for ( $i = 1; $i <= 6; $i++ ) {
		$m[ sprintf( 'realism-%02d', $i ) ] = $realism;
	}
	foreach ( array( 'full-chest', 'full-upper-back', 'full-outer-sleeve', 'floral-full-sleeve', 'full-forearm', 'front-lower-leg', 'full-back' ) as $p ) {
		$m[ 'promo-' . $p ] = $promo;
	}

	return $m;
}

/**
 * URL of a theme asset.
 *
 * @param string $path Path relative to assets/.
 * @return string
 */
function inko_asset( $path ) {
	return get_theme_file_uri( 'assets/' . ltrim( $path, '/' ) );
}

/**
 * Builds a responsive <img> tag for a generated image family.
 *
 * @param string $name  Image family name from the manifest.
 * @param string $alt   Alt text ('' for decorative images).
 * @param string $sizes The sizes attribute.
 * @param array  $args  { class, eager (bool), fetchpriority, attrs (array) }.
 * @return string
 */
function inko_img( $name, $alt, $sizes = '100vw', $args = array() ) {
	$manifest = inko_image_manifest();

	if ( empty( $manifest[ $name ] ) ) {
		return '';
	}

	$set    = $manifest[ $name ];
	$srcset = array();

	foreach ( $set as $s ) {
		$srcset[] = esc_url( inko_asset( "images/{$name}-{$s[0]}.webp" ) ) . " {$s[0]}w";
	}

	$default = $set[0];
	$eager   = ! empty( $args['eager'] );
	$attrs   = array(
		'src'      => inko_asset( "images/{$name}-{$default[0]}.webp" ),
		'srcset'   => implode( ', ', $srcset ),
		'sizes'    => $sizes,
		'width'    => $default[0],
		'height'   => $default[1],
		'alt'      => $alt,
		'loading'  => $eager ? 'eager' : 'lazy',
		'decoding' => $eager ? 'sync' : 'async',
	);

	if ( ! empty( $args['class'] ) ) {
		$attrs['class'] = $args['class'];
	}
	if ( ! empty( $args['fetchpriority'] ) ) {
		$attrs['fetchpriority'] = $args['fetchpriority'];
	}
	if ( ! empty( $args['attrs'] ) && is_array( $args['attrs'] ) ) {
		$attrs = array_merge( $attrs, $args['attrs'] );
	}

	$html = '<img';
	foreach ( $attrs as $k => $v ) {
		if ( 'src' === $k ) {
			$html .= ' src="' . esc_url( $v ) . '"';
		} elseif ( 'srcset' === $k ) {
			$html .= ' srcset="' . $v . '"'; // Each URL already escaped above.
		} else {
			$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
	}

	return $html . '>';
}

/**
 * URL of the largest size of an image family (for lightbox use).
 *
 * @param string $name Image family.
 * @return string
 */
function inko_img_large( $name ) {
	$manifest = inko_image_manifest();
	if ( empty( $manifest[ $name ] ) ) {
		return '';
	}
	$last = end( $manifest[ $name ] );
	return inko_asset( "images/{$name}-{$last[0]}.webp" );
}

/**
 * Small inline SVG icon set (UI glyphs only).
 *
 * @param string $name Icon name.
 * @return string
 */
function inko_icon( $name ) {
	$icons = array(
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-ur' => '<path d="M7 17 17 7M8 7h9v9"/>',
		'prev'     => '<path d="M15 6l-6 6 6 6"/>',
		'next'     => '<path d="M9 6l6 6-6 6"/>',
		'pin'      => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 7 9 6 9-6"/>',
		'phone'    => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'upload'   => '<path d="M12 16V4M7 9l5-5 5 5M4 16v4h16v-4"/>',
		'star'     => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$fill = 'star' === $name ? 'currentColor' : 'none';

	return '<svg class="icon icon-' . esc_attr( $name ) . '" viewBox="0 0 24 24" width="24" height="24" fill="' . $fill . '" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * Social links list (icons are alpha PNG masks tinted with currentColor).
 *
 * @param string     $class      Wrapper class.
 * @param bool       $show_label Show the network name next to the icon.
 * @param array|null $only       Optional list of network keys to include.
 */
function inko_social_links( $class = 'social', $show_label = false, $only = null ) {
	$social = inko_content( 'social' );
	if ( is_array( $only ) ) {
		$social = array_intersect_key( $social, array_flip( $only ) );
	}

	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $social as $key => $s ) {
		printf(
			'<li><a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s"><span class="social-icon" style="--icon:url(%3$s)" aria-hidden="true"></span>%4$s</a></li>',
			esc_url( $s['url'] ),
			esc_attr( sprintf( /* translators: %s: network name */ __( 'INKO Melbourne on %s (opens in a new tab)', 'inko-custom' ), $s['label'] ) ),
			esc_url( inko_asset( 'icons/ic-' . $key . '.png' ) ),
			$show_label ? '<span class="social-label">' . esc_html( $s['label'] ) . '</span><span class="social-handle">' . esc_html( $s['handle'] ) . '</span>' : ''
		);
	}
	echo '</ul>';
}

/**
 * Slide eyebrow: "03 — Promotions".
 *
 * @param int    $num   Slide number.
 * @param string $label Short label (interface text).
 */
function inko_eyebrow( $num, $label ) {
	// An empty label (Slide 02) prints the number alone.
	if ( '' === $label ) {
		printf( '<p class="eyebrow" data-reveal><span class="eyebrow-num">%s</span></p>', esc_html( sprintf( '%02d', $num ) ) );
		return;
	}
	printf(
		'<p class="eyebrow" data-reveal><span class="eyebrow-num">%1$s</span><span class="eyebrow-label">%2$s</span></p>',
		esc_html( sprintf( '%02d', $num ) ),
		esc_html( $label )
	);
}

/**
 * Editorial heading: plain words plus one italic orange accent phrase.
 * inko_heading( 'Meet Our', 'Artists' ) → Meet Our <em>Artists</em>.
 *
 * @param string $plain  Leading words (may be '').
 * @param string $accent Accent words.
 * @return string Escaped HTML.
 */
function inko_heading( $plain, $accent ) {
	$plain = trim( $plain );
	return ( '' !== $plain ? esc_html( $plain ) . ' ' : '' ) . '<em class="accent">' . esc_html( $accent ) . '</em>';
}

/**
 * Escaped text whose in-word hyphens (hyper-realistic, true-to-life) never
 * break across lines, so a hyphen can't end a line like a stray dash.
 * Display only: the stored content stays verbatim.
 *
 * @param string $text Text.
 * @return string Escaped HTML.
 */
function inko_text( $text ) {
	return preg_replace( '/(?<=\w)-(?=\w)/u', "\u{2011}", esc_html( $text ) );
}

/**
 * Splits a title on its last word: "Meet Our Artists" → ['Meet Our', 'Artists'].
 *
 * @param string $title Title.
 * @return array{0:string,1:string}
 */
function inko_split_last( $title ) {
	$title = trim( $title );
	$pos   = strrpos( $title, ' ' );
	return false === $pos ? array( '', $title ) : array( substr( $title, 0, $pos ), substr( $title, $pos + 1 ) );
}

/**
 * Link target for in-page sections: plain "#id" on the front page,
 * home URL + "#id" everywhere else.
 *
 * @param string $id Section id.
 * @return string
 */
function inko_section_url( $id ) {
	return is_front_page() ? '#' . $id : home_url( '/#' . $id );
}

/**
 * The homepage slide list: id => [template, nav label].
 * Nav labels are interface text, not business content.
 *
 * @return array
 */
function inko_slides() {
	return array(
		'home'              => array( '01-hero', __( 'Home', 'inko-custom' ) ),
		'about'             => array( '02-about', __( 'Fine Line & Realism', 'inko-custom' ) ),
		'promotions'        => array( '03-promotions', __( 'Promotions', 'inko-custom' ) ),
		'artists'           => array( '04-artists', __( 'Artists', 'inko-custom' ) ),
		// Client: Location follows Artists.
		'studio'            => array( '07-studio', __( 'Location', 'inko-custom' ) ),
		'finelineshowcase'  => array( '05-fineline', __( 'Our Work: Fine Line', 'inko-custom' ) ),
		'realism-showcase'  => array( '06-realism', __( 'Our Work: Realism', 'inko-custom' ) ),
		'reviews'           => array( '08-reviews', __( 'Reviews', 'inko-custom' ) ),
		'quote'             => array( '09-quote', __( 'Quotation', 'inko-custom' ) ),
		'pay-later'         => array( '10-pay-follow', __( 'Pay Later', 'inko-custom' ) ),
		'contact'           => array( '11-contact', __( 'Contact', 'inko-custom' ) ),
	);
}
