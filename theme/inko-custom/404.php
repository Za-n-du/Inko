<?php
/**
 * 404.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="page-shell page-shell--404" tabindex="-1">
	<header class="page-hero">
		<p class="page-hero__meta">404</p>
		<h1 class="page-hero__title"><?php esc_html_e( 'Page not found', 'inko-custom' ); ?></h1>
	</header>
	<div class="page-content">
		<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Back to the homepage', 'inko-custom' ); ?></span> <?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></a></p>
	</div>
</main>

<?php
get_footer();
