<?php
/**
 * INKO Melbourne theme bootstrap.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INKO_THEME_VERSION', '3.0.0' );

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/menu-setup.php';
require_once get_template_directory() . '/inc/importer.php';

/**
 * Theme supports and menu locations.
 */
function inko_setup() {
	load_theme_textdomain( 'inko-melbourne', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 134, 'width' => 200, 'flex-width' => true, 'flex-height' => true ) );
	register_nav_menus(
		array(
			'primary' => __( 'Primary (header + mobile menu)', 'inko-melbourne' ),
		)
	);
}
add_action( 'after_setup_theme', 'inko_setup' );

/**
 * Enqueue the editorial homepage bundle on the new home; legacy slide assets are limited to pages with .ik-slide data.
 */
function inko_enqueue_assets() {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	$ver = static function ( $rel ) use ( $dir ) {
		$file = $dir . $rel;
		return file_exists( $file ) ? (string) filemtime( $file ) : INKO_THEME_VERSION;
	};

	wp_enqueue_style( 'inko-fonts', 'https://fonts.googleapis.com/css2?family=Archivo:wght@300;500;700;800;900&family=Manrope:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap', array(), null );
	wp_enqueue_style( 'inko-base', $uri . '/assets/css/base.css', array( 'inko-fonts' ), $ver( '/assets/css/base.css' ) );
	wp_enqueue_style( 'inko-header', $uri . '/assets/css/header.css', array( 'inko-base' ), $ver( '/assets/css/header.css' ) );

	if ( inko_is_editorial_home() ) {
		wp_enqueue_style( 'inko-homepage', $uri . '/assets/css/homepage.css', array( 'inko-header' ), $ver( '/assets/css/homepage.css' ) );
		wp_enqueue_style( 'inko-home-quote', $uri . '/assets/css/home-quote.css', array( 'inko-homepage' ), $ver( '/assets/css/home-quote.css' ) );
	} elseif ( inko_is_stack_page() ) {
		wp_enqueue_style( 'inko-slides', $uri . '/assets/css/slides.css', array( 'inko-header' ), $ver( '/assets/css/slides.css' ) );
		wp_enqueue_style( 'inko-components', $uri . '/assets/css/components.css', array( 'inko-slides' ), $ver( '/assets/css/components.css' ) );
		wp_enqueue_style( 'inko-responsive', $uri . '/assets/css/responsive.css', array( 'inko-components' ), $ver( '/assets/css/responsive.css' ) );
		wp_enqueue_style( 'inko-editor', $uri . '/assets/css/editor.css', array( 'inko-responsive' ), $ver( '/assets/css/editor.css' ) );
		// Elementor's own stylesheet loads first so Elementor controls set in the editor keep winning over theme defaults.
		if ( wp_style_is( 'elementor-frontend', 'registered' ) ) {
			wp_styles()->registered['inko-slides']->deps[] = 'elementor-frontend';
		}
	}

	$strategy = array( 'in_footer' => true, 'strategy' => 'defer' );
	if ( inko_is_editorial_home() ) {
		wp_enqueue_script( 'inko-navigation', $uri . '/assets/js/navigation.js', array(), $ver( '/assets/js/navigation.js' ), $strategy );
		wp_enqueue_script( 'inko-homepage', $uri . '/assets/js/homepage.js', array( 'inko-navigation' ), $ver( '/assets/js/homepage.js' ), $strategy );
		wp_enqueue_script( 'inko-quote-form', $uri . '/assets/js/quote.js', array( 'inko-homepage' ), $ver( '/assets/js/quote.js' ), $strategy );
		wp_localize_script( 'inko-homepage', 'INKOHomeConfig', array(
			'wordpress' => true,
			'quoteUrl'  => rest_url( 'inko/v1/quote' ),
			'quoteNonce' => wp_create_nonce( 'wp_rest' ),
		) );
	} else {
		wp_enqueue_script( 'inko-slides', $uri . '/assets/js/slides.js', array(), $ver( '/assets/js/slides.js' ), $strategy );
		wp_enqueue_script( 'inko-navigation', $uri . '/assets/js/navigation.js', array( 'inko-slides' ), $ver( '/assets/js/navigation.js' ), $strategy );
		wp_enqueue_script( 'inko-main', $uri . '/assets/js/main.js', array( 'inko-slides', 'inko-navigation' ), $ver( '/assets/js/main.js' ), $strategy );
	}
}
add_action( 'wp_enqueue_scripts', 'inko_enqueue_assets', 20 );

/** Public quotation endpoint used by the editorial homepage form. */
function inko_register_quote_route() {
	register_rest_route(
		'inko/v1',
		'/quote',
		array(
			'methods'             => 'POST',
			'callback'            => 'inko_receive_quote',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'inko_register_quote_route' );

/** Validate and email the homepage quotation form through the WordPress mail configuration. */
function inko_receive_quote( WP_REST_Request $request ) {
	$ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$limiter = 'inko_quote_' . md5( $ip );
	if ( get_transient( $limiter ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'Please wait a moment before sending another request.' ), 429 );
	}
	$website = sanitize_text_field( (string) $request->get_param( 'website' ) );
	if ( $website ) {
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}
	$fields = array(
		'firstName' => sanitize_text_field( (string) $request->get_param( 'firstName' ) ),
		'lastName'  => sanitize_text_field( (string) $request->get_param( 'lastName' ) ),
		'email'     => sanitize_email( (string) $request->get_param( 'email' ) ),
		'phone'     => sanitize_text_field( (string) $request->get_param( 'phone' ) ),
		'details'   => sanitize_textarea_field( (string) $request->get_param( 'details' ) ),
	);
	$errors = array();
	if ( '' === $fields['firstName'] || strlen( $fields['firstName'] ) > 80 ) {
		$errors['firstName'] = 'Please enter a valid first name.';
	}
	if ( '' === $fields['lastName'] || strlen( $fields['lastName'] ) > 80 ) {
		$errors['lastName'] = 'Please enter a valid last name.';
	}
	if ( ! is_email( $fields['email'] ) || strlen( $fields['email'] ) > 254 ) {
		$errors['email'] = 'Please enter a valid email address.';
	}
	if ( $fields['phone'] && ! preg_match( '/^[+()0-9 .-]{6,25}$/', $fields['phone'] ) ) {
		$errors['phone'] = 'Please enter a valid phone number.';
	}
	if ( strlen( $fields['details'] ) < 10 || strlen( $fields['details'] ) > 5000 ) {
		$errors['details'] = 'Please describe the size, placement and color preference (10 to 5000 characters).';
	}
	$upload      = $request->get_file_params();
	$attachment  = '';
	$file        = isset( $upload['file'] ) ? $upload['file'] : null;
	if ( $file && UPLOAD_ERR_OK === (int) $file['error'] && (int) $file['size'] > 0 ) {
		if ( (int) $file['size'] > 4 * MB_IN_BYTES ) {
			$errors['file'] = 'The file is larger than 4 MB. Please upload a smaller file.';
		} else {
			$allowed = array( 'jpg|jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'pdf' => 'application/pdf', 'heic|heif' => 'image/heic' );
			$check   = wp_check_filetype( $file['name'], $allowed );
			$mime    = function_exists( 'mime_content_type' ) ? mime_content_type( $file['tmp_name'] ) : $check['type'];
			$ext     = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
			$types   = array(
				'jpg'  => array( 'image/jpeg' ),
				'jpeg' => array( 'image/jpeg' ),
				'png'  => array( 'image/png' ),
				'webp' => array( 'image/webp' ),
				'pdf'  => array( 'application/pdf' ),
				'heic' => array( 'image/heic', 'image/heif', 'application/octet-stream' ),
				'heif' => array( 'image/heic', 'image/heif', 'application/octet-stream' ),
			);
			$valid   = $check['type'] && isset( $types[ $ext ] ) && in_array( $mime, $types[ $ext ], true );
			if ( ! $valid ) {
				$errors['file'] = 'Please upload a JPG, PNG, WEBP, HEIC or PDF file.';
			} else {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				$saved = wp_upload_bits( sanitize_file_name( $file['name'] ), null, file_get_contents( $file['tmp_name'] ) );
				if ( ! empty( $saved['error'] ) ) {
					$errors['file'] = 'The reference file could not be attached. Please try again.';
				} else {
					$attachment = $saved['file'];
				}
			}
		}
	} elseif ( $file && UPLOAD_ERR_NO_FILE !== (int) $file['error'] ) {
		$errors['file'] = 'The reference file could not be read. Please choose it again.';
	}
	if ( $errors ) {
		if ( $attachment ) {
			wp_delete_file( $attachment );
		}
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'Please check the highlighted fields.', 'fields' => $errors ), 400 );
	}
	$name    = trim( $fields['firstName'] . ' ' . $fields['lastName'] );
	$subject = 'Tattoo quotation request from ' . $name;
	$body    = "New tattoo quotation request\n\nName: {$name}\nEmail: {$fields['email']}\nPhone: " . ( $fields['phone'] ? $fields['phone'] : 'Not provided' ) . "\n\nDetails:\n{$fields['details']}";
	$headers = array( 'Reply-To: ' . $name . ' <' . $fields['email'] . '>' );
	$sent    = wp_mail( inko_contact( 'email' ), $subject, $body, $headers, $attachment ? array( $attachment ) : array() );
	if ( $attachment ) {
		wp_delete_file( $attachment );
	}
	if ( ! $sent ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'We could not send your request right now. Please try again or email inko.melbourne@gmail.com.' ), 502 );
	}
	set_transient( $limiter, 1, 90 );
	return new WP_REST_Response( array( 'ok' => true ), 200 );
}

/**
 * Preconnect for Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation type.
 * @return array
 */
function inko_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'inko_resource_hints', 10, 2 );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function inko_body_class( $classes ) {
	if ( inko_is_editorial_home() ) {
		$classes[] = 'inko-home-page';
	}
	if ( inko_is_stack_page() ) {
		$classes[] = 'ik-stack';
	}
	return $classes;
}
add_filter( 'body_class', 'inko_body_class' );

/**
 * Keep the fixed header below the WordPress admin bar.
 */
function inko_admin_bar_css() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	echo '<style id="inko-adminbar">body.admin-bar .ik-header{top:32px}@media(max-width:782px){body.admin-bar .ik-header{top:46px}}</style>' . "\n";
}
add_action( 'wp_head', 'inko_admin_bar_css', 99 );

/**
 * Slide 01 background photo: always serve the full-size file (125 KB). With object-fit:cover in a tall box it is shown
 * much wider than the viewport on tablets/phones, so srcset/sizes would pick a copy that is too small. It is the first
 * thing visitors see, so it also loads eagerly with high priority.
 *
 * @param array|false $sources       Srcset sources.
 * @param array       $size_array    Requested size.
 * @param string      $image_src     Image URL.
 * @param array       $image_meta    Attachment meta.
 * @param int         $attachment_id Attachment ID.
 * @return array|false
 */
function inko_hero_bg_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
	// Full-slide background photos of Slides 01-04 and 06 (cropped by object-fit:cover on tablets/phones).
	return in_array( get_post_meta( $attachment_id, '_inko_asset', true ), array( 'hero-bg.webp', 'intro-bg.webp', 'gal-bg.webp', 'follow-bg.webp' ), true ) ? false : $sources;
}
add_filter( 'wp_calculate_image_srcset', 'inko_hero_bg_srcset', 10, 5 );

/**
 * Eager, high-priority loading for the Slide 01 background photo.
 *
 * @param array   $attr       Image attributes.
 * @param WP_Post $attachment Attachment.
 * @return array
 */
function inko_hero_bg_attributes( $attr, $attachment ) {
	if ( 'hero-bg.webp' === get_post_meta( $attachment->ID, '_inko_asset', true ) ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'inko_hero_bg_attributes', 10, 2 );
