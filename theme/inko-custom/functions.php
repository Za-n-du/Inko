<?php
/**
 * INKO Melbourne Custom Theme — bootstrap.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

define( 'INKO_THEME_VERSION', '2.1.0' );

require_once get_theme_file_path( 'inc/content.php' );
require_once get_theme_file_path( 'inc/template-tags.php' );
require_once get_theme_file_path( 'inc/quote-form.php' );

/**
 * Register theme supports and menus.
 */
function inko_custom_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'inko-custom' ),
		)
	);
}
add_action( 'after_setup_theme', 'inko_custom_theme_setup' );

/**
 * Content width for embeds and Elementor's default boxed width.
 */
function inko_custom_content_width() {
	$GLOBALS['content_width'] = 1240;
}
add_action( 'after_setup_theme', 'inko_custom_content_width', 0 );

/**
 * Google Fonts URL — the whole type system is two families:
 * Playfair Display (editorial serif headings, italic accents) + Manrope (text, UI).
 *
 * @return string
 */
function inko_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..800;1,400..700&display=swap';
}

/**
 * Enqueue front-end assets.
 */
function inko_custom_enqueue_assets() {
	$css = get_theme_file_path( 'assets/css/main.css' );
	$js  = get_theme_file_path( 'assets/js/main.js' );

	wp_enqueue_style( 'inko-fonts', inko_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'inko-main', get_theme_file_uri( 'assets/css/main.css' ), array(), file_exists( $css ) ? (string) filemtime( $css ) : INKO_THEME_VERSION );

	wp_enqueue_script(
		'inko-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		file_exists( $js ) ? (string) filemtime( $js ) : INKO_THEME_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'inko_custom_enqueue_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Hint type.
 * @return array
 */
function inko_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'inko_resource_hints', 10, 2 );

/**
 * Front page: preload the hero image (LCP).
 */
function inko_head_extras() {
	if ( is_front_page() ) {
		$set = inko_image_manifest()['hero-slide-1'];
		$src = array();
		foreach ( $set as $s ) {
			$src[] = esc_url( inko_asset( "images/hero-slide-1-{$s[0]}.webp" ) ) . " {$s[0]}w";
		}
		printf(
			'<link rel="preload" as="image" type="image/webp" imagesrcset="%s" imagesizes="100vw" fetchpriority="high">' . "\n",
			esc_attr( implode( ', ', $src ) )
		);
	}

	echo '<meta name="theme-color" content="#050505">' . "\n";

	// Browser tab icon from the original INKO logo, unless a Site Icon is set in the Customizer.
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" type="image/png" href="%s">' . "\n", esc_url( inko_asset( 'images/logo-inko.png' ) ) );
	}
}
add_action( 'wp_head', 'inko_head_extras', 2 );

/**
 * True when a dedicated SEO plugin handles meta tags.
 *
 * @return bool
 */
function inko_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || class_exists( 'The_SEO_Framework\Load' );
}

/**
 * Meta description + Open Graph + LocalBusiness structured data (front page).
 * Uses only the studio's published facts.
 */
function inko_seo_meta() {
	if ( inko_seo_plugin_active() || ! is_front_page() ) {
		return;
	}

	$c     = inko_content();
	$i     = $c['intro'];
	$desc  = $i['before'] . $i['fineline'] . $i['and'] . $i['realism'] . $i['after'];
	$s     = $c['studio'];
	$image = inko_asset( 'images/studio-hero-1200.webp' );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	echo '<meta property="og:type" content="website">' . "\n";
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $s['name'] ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( home_url( '/' ) ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'TattooParlor',
		'name'        => $s['name'],
		'url'         => home_url( '/' ),
		'image'       => $image,
		'email'       => $s['email'],
		'telephone'   => $s['phone_tel'],
		'description' => $desc,
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $s['address_1'],
			'addressLocality' => 'Camberwell',
			'addressRegion'   => 'VIC',
			'postalCode'      => '3142',
			'addressCountry'  => 'AU',
		),
		'sameAs'      => array_values( wp_list_pluck( $c['social'], 'url' ) ),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'inko_seo_meta', 3 );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function inko_body_classes( $classes ) {
	$classes[] = is_front_page() ? 'inko-home' : 'inko-inner';
	return $classes;
}
add_filter( 'body_class', 'inko_body_classes' );

/**
 * Fallback for the primary menu: the homepage sections.
 */
function inko_menu_fallback() {
	$items = array(
		'home'             => __( 'Home', 'inko-custom' ),
		'promotions'       => __( 'Promotions', 'inko-custom' ),
		'artists'          => __( 'Artists', 'inko-custom' ),
		'finelineshowcase' => __( 'Our Work', 'inko-custom' ),
		'studio'           => __( 'Studio', 'inko-custom' ),
		'reviews'          => __( 'Reviews', 'inko-custom' ),
		'contact'          => __( 'Contact', 'inko-custom' ),
	);

	echo '<ul class="nav-list">';
	foreach ( $items as $id => $label ) {
		printf( '<li><a href="%1$s" data-nav="%2$s">%3$s</a></li>', esc_url( inko_section_url( $id ) ), esc_attr( $id ), esc_html( $label ) );
	}
	echo '</ul>';
}
