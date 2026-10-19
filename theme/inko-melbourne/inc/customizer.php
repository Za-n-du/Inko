<?php
/**
 * Customizer: logo, Book Now button, social links and contact details.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function inko_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'inko_panel',
		array(
			'title'    => __( 'INKO Melbourne', 'inko-melbourne' ),
			'priority' => 30,
		)
	);

	// Header.
	$wp_customize->add_section( 'inko_header', array( 'title' => __( 'Header', 'inko-melbourne' ), 'panel' => 'inko_panel' ) );
	foreach ( array(
		'box'   => __( 'Logo on the hero slide (orange box version)', 'inko-melbourne' ),
		'plain' => __( 'Logo on all other slides (orange, transparent)', 'inko-melbourne' ),
	) as $variant => $label ) {
		$wp_customize->add_setting( 'inko_logo_' . $variant, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'inko_logo_' . $variant,
				array(
					'label'     => $label,
					'section'   => 'inko_header',
					'mime_type' => 'image',
				)
			)
		);
	}
	$wp_customize->add_setting( 'inko_book_label', array( 'default' => 'Book Now', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'inko_book_label', array( 'label' => __( 'Book button text', 'inko-melbourne' ), 'section' => 'inko_header', 'type' => 'text' ) );
	$wp_customize->add_setting( 'inko_book_url', array( 'default' => '#quote', 'sanitize_callback' => 'inko_sanitize_link' ) );
	$wp_customize->add_control( 'inko_book_url', array( 'label' => __( 'Book button link (#quote jumps to the quotation slide)', 'inko-melbourne' ), 'section' => 'inko_header', 'type' => 'text' ) );

	// Social.
	$wp_customize->add_section( 'inko_social', array( 'title' => __( 'Social links', 'inko-melbourne' ), 'panel' => 'inko_panel' ) );
	foreach ( array(
		'facebook'  => array( 'Facebook', 'https://www.facebook.com/inkomelbourne/' ),
		'instagram' => array( 'Instagram', 'https://www.instagram.com/inko_melbourne/' ),
		'tiktok'    => array( 'TikTok', 'https://www.tiktok.com/@inko.melbourne?_r=1&_t=ZS-9AgJRaJcvlk' ),
		'youtube'   => array( 'YouTube', 'https://youtube.com/@inko-b5s?si=r4qYbwGlXBcjs4s2' ),
		'threads'   => array( 'Threads', 'https://www.threads.com/@inko_melbourne' ),
	) as $key => $info ) {
		$wp_customize->add_setting( 'inko_social_' . $key, array( 'default' => $info[1], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'inko_social_' . $key, array( 'label' => $info[0], 'section' => 'inko_social', 'type' => 'url' ) );
	}

	// Contact.
	$wp_customize->add_section( 'inko_contact', array( 'title' => __( 'Contact (menu + simple footer)', 'inko-melbourne' ), 'panel' => 'inko_panel' ) );
	foreach ( array(
		'name'    => __( 'Studio name', 'inko-melbourne' ),
		'address' => __( 'Address', 'inko-melbourne' ),
		'email'   => __( 'Email', 'inko-melbourne' ),
		'phone'   => __( 'Phone', 'inko-melbourne' ),
	) as $key => $label ) {
		$wp_customize->add_setting( 'inko_contact_' . $key, array( 'default' => inko_contact( $key ), 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( 'inko_contact_' . $key, array( 'label' => $label, 'section' => 'inko_contact', 'type' => 'text' ) );
	}
}
add_action( 'customize_register', 'inko_customize_register' );

/**
 * Allow #anchors as well as normal URLs.
 *
 * @param string $value Raw value.
 * @return string
 */
function inko_sanitize_link( $value ) {
	$value = trim( (string) $value );
	if ( 0 === strpos( $value, '#' ) ) {
		return '#' . sanitize_html_class( substr( $value, 1 ) );
	}
	return esc_url_raw( $value );
}
