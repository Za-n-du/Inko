<?php
/**
 * Header: <head>, skip link, the V3 header + mobile menu + (on slide pages) side label, icon rail, slide counter and scroll cue.
 * Rendered once here; Elementor never adds another header.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#090b0f">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#ik-main"><?php esc_html_e( 'Skip to content', 'inko-melbourne' ); ?></a>
<?php
get_template_part( 'template-parts/header/header' );
get_template_part( 'template-parts/header/menu-overlay' );
if ( inko_is_stack_page() ) {
	get_template_part( 'template-parts/components/chrome' );
}
