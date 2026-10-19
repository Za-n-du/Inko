<?php
/**
 * Helpers shared by templates.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL of a file inside the theme /assets folder (never a hard-coded domain).
 *
 * @param string $rel Path relative to /assets, e.g. icons/ic-facebook.png.
 * @return string
 */
function inko_asset( $rel ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $rel, '/' );
}

/**
 * Does the current singular page contain INKO slides (built with Elementor)?
 *
 * @return bool
 */
function inko_is_stack_page() {
	static $cache = array();
	if ( is_admin() || ! is_singular() ) {
		return false;
	}
	$id = (int) get_queried_object_id();
	if ( ! $id ) {
		return false;
	}
	if ( ! isset( $cache[ $id ] ) ) {
		$tpl  = get_page_template_slug( $id );
		$data = get_post_meta( $id, '_elementor_data', true );
		if ( is_array( $data ) ) {
			$data = wp_json_encode( $data );
		}
		$cache[ $id ] = ( 'template-inko-home.php' === $tpl ) || ( is_string( $data ) && false !== strpos( $data, 'ik-slide' ) );
	}
	return $cache[ $id ];
}

/**
 * Contact / studio details (editable in Customizer > INKO Contact).
 *
 * @param string $key name|address|email|phone.
 * @return string
 */
function inko_contact( $key ) {
	$defaults = array(
		'name'    => 'INKO Melbourne Tattoo Studio',
		'address' => '1c Cookson Street, Camberwell, VIC 3142',
		'email'   => 'inko.melbourne@gmail.com',
		'phone'   => '(+61) 481-082077',
	);
	return (string) get_theme_mod( 'inko_contact_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Header logo (Customizer > INKO Header), falling back to the theme's own files.
 *
 * @param string $variant box|plain.
 * @return array url,width,height.
 */
function inko_logo( $variant ) {
	$id = (int) get_theme_mod( 'inko_logo_' . $variant, 0 );
	if ( $id ) {
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( $src ) {
			return array( 'url' => $src[0], 'w' => (int) $src[1], 'h' => (int) $src[2] );
		}
	}
	if ( 'box' === $variant ) {
		return array( 'url' => inko_asset( 'icons/logo-box.png' ), 'w' => 464, 'h' => 332 );
	}
	return array( 'url' => inko_asset( 'icons/logo-plain.png' ), 'w' => 400, 'h' => 268 );
}

/**
 * Social links (Customizer > INKO Social). Missing URLs fall back to "#" so the V3 icon rail keeps its design.
 *
 * @return array
 */
function inko_socials() {
	$defaults = array(
		'facebook'  => 'https://www.facebook.com/inkomelbourne/',
		'instagram' => 'https://www.instagram.com/inko_melbourne/',
		'tiktok'    => 'https://www.tiktok.com/@inko.melbourne?_r=1&_t=ZS-9AgJRaJcvlk',
		'youtube'   => 'https://youtube.com/@inko-b5s?si=r4qYbwGlXBcjs4s2',
		'threads'   => 'https://www.threads.com/@inko_melbourne',
	);
	$labels = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'tiktok'    => 'TikTok',
		'youtube'   => 'YouTube',
		'threads'   => 'Threads',
	);
	$out = array();
	foreach ( $defaults as $key => $default ) {
		$url         = (string) get_theme_mod( 'inko_social_' . $key, $default );
		$out[ $key ] = array(
			'label' => $labels[ $key ],
			'url'   => '' !== $url ? $url : '#',
			'icon'  => inko_asset( 'icons/ic-' . $key . '.png' ),
		);
	}
	return $out;
}

/**
 * Default navigation (used until a menu is assigned to the Primary location).
 *
 * @return array of array( title, url ).
 */
function inko_default_nav() {
	return array(
		array( 'Home', '#home' ),
		array( 'Artists', '#artists' ),
		array( 'Our Work', '#fine-line' ),
		array( 'Studio', '#location' ),
		array( 'Contact', '#quote' ),
	);
}

/**
 * Navigation items: WordPress menu (Appearance > Menus, location "Primary") or the defaults.
 * "#slide-id" links work on the homepage and are rewritten to "/#slide-id" on every other page.
 *
 * @return array of array( title, url ).
 */
function inko_nav_items() {
	$items     = array();
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		$menu = wp_get_nav_menu_items( $locations['primary'] );
		if ( $menu ) {
			foreach ( $menu as $item ) {
				if ( (int) $item->menu_item_parent ) {
					continue;
				}
				$items[] = array( $item->title, $item->url );
			}
		}
	}
	if ( ! $items ) {
		$items = inko_default_nav();
	}
	$stack = inko_is_stack_page();
	foreach ( $items as $k => $item ) {
		if ( 0 === strpos( $item[1], '#' ) && ! $stack ) {
			$items[ $k ][1] = home_url( '/' ) . $item[1];
		}
	}
	return $items;
}
