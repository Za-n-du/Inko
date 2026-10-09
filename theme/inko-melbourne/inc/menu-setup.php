<?php
/**
 * Creates the default Primary menu (editable in Appearance > Menus) when the theme is activated.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * On activation: create "INKO Primary" with the five V3 links and assign it, unless a Primary menu already exists.
 */
function inko_create_default_menu() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! empty( $locations['primary'] ) ) {
		return;
	}
	$menu = wp_get_nav_menu_object( 'INKO Primary' );
	$id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( 'INKO Primary' );
	if ( ! $id || is_wp_error( $id ) ) {
		return;
	}
	if ( ! $menu ) {
		foreach ( inko_default_nav() as $item ) {
			wp_update_nav_menu_item(
				$id,
				0,
				array(
					'menu-item-title'  => $item[0],
					'menu-item-url'    => $item[1],
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
				)
			);
		}
	}
	$locations['primary'] = $id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
add_action( 'after_switch_theme', 'inko_create_default_menu' );
