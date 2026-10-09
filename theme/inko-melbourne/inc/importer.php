<?php
/**
 * Appearance > INKO Setup: creates the ONE V3 Elementor homepage from /elementor/inko-v3-homepage.json.
 * Needs only this theme + Elementor (free). No old page, template or plugin is read.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INKO_PAGE_OPTION', 'inko_home_page_id' );

/**
 * Add one image from the theme to the Media Library (once) and return its attachment ID.
 *
 * @param string $file Absolute path.
 * @param string $alt  Alt text.
 * @return int
 */
function inko_import_attachment( $file, $alt ) {
	$base  = basename( $file );
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_inko_asset',
			'meta_value'  => $base,
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$bits = wp_upload_bits( 'inko-' . $base, null, file_get_contents( $file ) );
	if ( ! empty( $bits['error'] ) ) {
		return 0;
	}
	$type = wp_check_filetype( $bits['file'] );
	$id   = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => pathinfo( $base, PATHINFO_FILENAME ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$bits['file']
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $bits['file'] ) );
	update_post_meta( $id, '_inko_asset', $base );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

/**
 * Replace the __URL_name__ / "__ID_name__" placeholders in the template JSON with Media Library URLs/IDs.
 *
 * @param string $raw JSON text.
 * @return string|WP_Error
 */
function inko_fill_images( $raw ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$alts = json_decode( (string) file_get_contents( get_template_directory() . '/elementor/inko-v3-alts.json' ), true );
	if ( ! is_array( $alts ) ) {
		$alts = array();
	}
	$files = array();
	foreach ( array( 'images', 'icons' ) as $folder ) {
		foreach ( array( 'webp', 'png' ) as $ext ) {
			$found = glob( get_template_directory() . '/assets/' . $folder . '/*.' . $ext );
			if ( $found ) {
				$files = array_merge( $files, $found );
			}
		}
	}
	foreach ( $files as $file ) {
		$name = pathinfo( $file, PATHINFO_FILENAME );
		if ( false === strpos( $raw, '__URL_' . $name . '__' ) ) {
			continue;
		}
		$id = inko_import_attachment( $file, isset( $alts[ $name ] ) ? $alts[ $name ] : '' );
		if ( ! $id ) {
			continue;
		}
		$url = wp_get_attachment_url( $id );
		$raw = str_replace( '"__ID_' . $name . '__"', (string) (int) $id, $raw );
		$raw = str_replace( '__URL_' . $name . '__', str_replace( '/', '\\/', (string) $url ), $raw );
	}
	if ( preg_match( '/__(URL|ID)_[A-Za-z0-9_\-]+__/', $raw ) ) {
		return new WP_Error( 'inko_assets', __( 'Some images could not be added to the Media Library. Check that wp-content/uploads is writable.', 'inko-melbourne' ) );
	}
	return $raw;
}

/**
 * Add the INKO colours and fonts to Elementor > Site Settings (Global Colors / Global Fonts). Idempotent.
 */
function inko_setup_kit() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id && isset( \Elementor\Plugin::$instance->kits_manager ) ) {
		$kit_id = (int) \Elementor\Plugin::$instance->kits_manager->get_active_id();
	}
	if ( ! $kit_id ) {
		return;
	}
	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}
	$sets = array(
		'custom_colors'     => array(
			array( '_id' => 'ikblack', 'title' => 'INKO Matte Black', 'color' => '#090B0F' ),
			array( '_id' => 'ikcharcoal', 'title' => 'INKO Charcoal', 'color' => '#15191F' ),
			array( '_id' => 'ikash', 'title' => 'INKO Ash', 'color' => '#23262B' ),
			array( '_id' => 'ikwhite', 'title' => 'INKO White', 'color' => '#F4F1EC' ),
			array( '_id' => 'ikmuted', 'title' => 'INKO Muted', 'color' => '#A8ADB5' ),
			array( '_id' => 'ikorange', 'title' => 'INKO Orange', 'color' => '#FF5A18' ),
		),
		'custom_typography' => array(
			array( '_id' => 'ikdisplay', 'title' => 'INKO Display', 'typography_typography' => 'custom', 'typography_font_family' => 'Archivo', 'typography_font_weight' => '900' ),
			array( '_id' => 'ikbody', 'title' => 'INKO Body', 'typography_typography' => 'custom', 'typography_font_family' => 'Manrope', 'typography_font_weight' => '500' ),
		),
	);
	foreach ( $sets as $key => $items ) {
		$existing = isset( $settings[ $key ] ) && is_array( $settings[ $key ] ) ? $settings[ $key ] : array();
		$keep     = array();
		foreach ( $existing as $row ) {
			if ( empty( $row['_id'] ) || 0 !== strpos( $row['_id'], 'ik' ) ) {
				$keep[] = $row;
			}
		}
		$settings[ $key ] = array_merge( $keep, $items );
	}
	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
}

/**
 * Clear Elementor's generated CSS so the new page/kit CSS is rebuilt.
 */
function inko_clear_elementor_cache() {
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

/**
 * The generated homepage, if it still exists.
 *
 * @return int Page ID or 0.
 */
function inko_home_page_id() {
	$id = (int) get_option( INKO_PAGE_OPTION, 0 );
	if ( $id && 'page' === get_post_type( $id ) && 'trash' !== get_post_status( $id ) ) {
		return $id;
	}
	return 0;
}

/**
 * Import the V3 homepage. There is only ever ONE generated page:
 *  - first run: creates it
 *  - $reset = true: rewrites that same page's Elementor content back to V3 (previous content is kept in a custom field)
 *
 * @param bool $set_front Make it the static front page.
 * @param bool $reset     Overwrite the existing generated page.
 * @return int|WP_Error Page ID.
 */
function inko_import_homepage( $set_front = true, $reset = false ) {
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return new WP_Error( 'inko_no_elementor', __( 'Elementor is not active. Install and activate Elementor (free), then run the import again.', 'inko-melbourne' ) );
	}
	$existing = inko_home_page_id();
	if ( $existing && ! $reset ) {
		return new WP_Error( 'inko_exists', sprintf( __( 'The V3 homepage already exists (page ID %d). Use "Reset homepage to V3" to restore it.', 'inko-melbourne' ), $existing ) );
	}
	$tpl = json_decode( (string) file_get_contents( get_template_directory() . '/elementor/inko-v3-homepage.json' ), true );
	if ( ! is_array( $tpl ) || empty( $tpl['content'] ) ) {
		return new WP_Error( 'inko_template', __( 'The Elementor data file could not be read.', 'inko-melbourne' ) );
	}
	$raw = inko_fill_images( wp_json_encode( $tpl['content'] ) );
	if ( is_wp_error( $raw ) ) {
		return $raw;
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return new WP_Error( 'inko_json', __( 'The Elementor data became invalid after the image import.', 'inko-melbourne' ) );
	}
	inko_setup_kit();

	if ( $existing ) {
		$pid = $existing;
		update_post_meta( $pid, '_inko_backup_previous_elementor_data', get_post_meta( $pid, '_elementor_data', true ) );
	} else {
		$pid = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'INKO Home',
				'post_name'    => 'inko-home',
				'post_content' => '',
			),
			true
		);
		if ( is_wp_error( $pid ) ) {
			return $pid;
		}
		update_option( INKO_PAGE_OPTION, (int) $pid );
	}
	update_post_meta( $pid, '_wp_page_template', 'template-inko-home.php' );
	update_post_meta( $pid, '_elementor_edit_mode', 'builder' );
	update_post_meta( $pid, '_elementor_template_type', 'wp-page' );
	update_post_meta( $pid, '_elementor_version', ELEMENTOR_VERSION );
	update_post_meta( $pid, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	if ( $set_front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $pid );
	}
	inko_clear_elementor_cache();
	return (int) $pid;
}

/**
 * Admin page.
 */
function inko_setup_menu() {
	add_theme_page( __( 'INKO Setup', 'inko-melbourne' ), __( 'INKO Setup', 'inko-melbourne' ), 'manage_options', 'inko-setup', 'inko_setup_page' );
}
add_action( 'admin_menu', 'inko_setup_menu' );

/**
 * Render the Appearance > INKO Setup screen.
 */
function inko_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$msg = '';
	if ( isset( $_POST['inko_do'] ) && check_admin_referer( 'inko_setup' ) ) {
		$reset = 'reset' === sanitize_key( wp_unslash( $_POST['inko_do'] ) );
		$res   = inko_import_homepage( ! empty( $_POST['inko_front'] ), $reset );
		if ( is_wp_error( $res ) ) {
			$msg = '<div class="notice notice-error"><p>' . esc_html( $res->get_error_message() ) . '</p></div>';
		} else {
			$msg = '<div class="notice notice-success"><p>' . esc_html__( 'Done. The V3 homepage is ready.', 'inko-melbourne' ) . ' <a href="' . esc_url( get_permalink( $res ) ) . '">' . esc_html__( 'View', 'inko-melbourne' ) . '</a> | <a href="' . esc_url( admin_url( 'post.php?post=' . (int) $res . '&action=elementor' ) ) . '">' . esc_html__( 'Edit with Elementor', 'inko-melbourne' ) . '</a></p></div>';
		}
	}
	$pid = inko_home_page_id();
	echo '<div class="wrap"><h1>' . esc_html__( 'INKO Setup', 'inko-melbourne' ) . '</h1>' . wp_kses_post( $msg );
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'Elementor (free) is not active. Install and activate it first: Plugins > Add New > search "Elementor".', 'inko-melbourne' ) . '</p></div>';
	}
	echo '<p>' . esc_html__( 'Creates the single V3 homepage (12 native Elementor slides). Images are added to the Media Library, INKO colours/fonts are added to Elementor Site Settings.', 'inko-melbourne' ) . '</p>';
	echo '<form method="post">';
	wp_nonce_field( 'inko_setup' );
	echo '<p><label><input type="checkbox" name="inko_front" value="1" checked> ' . esc_html__( 'Set it as the static front page', 'inko-melbourne' ) . '</label></p>';
	if ( $pid ) {
		echo '<p>' . sprintf( esc_html__( 'Homepage page ID: %d.', 'inko-melbourne' ), (int) $pid ) . ' <a href="' . esc_url( admin_url( 'post.php?post=' . $pid . '&action=elementor' ) ) . '">' . esc_html__( 'Edit with Elementor', 'inko-melbourne' ) . '</a></p>';
		echo '<p><button class="button" name="inko_do" value="reset" onclick="return confirm(\'' . esc_js( __( 'This replaces the homepage content with the original V3 version. Your current content is kept in the page custom field _inko_backup_previous_elementor_data. Continue?', 'inko-melbourne' ) ) . '\');">' . esc_html__( 'Reset homepage to V3 (overwrites)', 'inko-melbourne' ) . '</button></p>';
	} else {
		echo '<p><button class="button button-primary" name="inko_do" value="import">' . esc_html__( 'Import the V3 homepage', 'inko-melbourne' ) . '</button></p>';
	}
	echo '</form></div>';
}

/**
 * After activation, point the admin to the setup screen.
 */
function inko_activation_notice() {
	if ( ! current_user_can( 'manage_options' ) || inko_home_page_id() ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'appearance_page_inko-setup' === $screen->id ) {
		return;
	}
	echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__( 'INKO Melbourne theme is active.', 'inko-melbourne' ) . ' <a href="' . esc_url( admin_url( 'themes.php?page=inko-setup' ) ) . '">' . esc_html__( 'Go to Appearance > INKO Setup to import the V3 homepage.', 'inko-melbourne' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'inko_activation_notice' );
