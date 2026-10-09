<?php
/**
 * 404.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="ik-main" class="ik-page">
	<div class="ik-page__in">
		<h1><?php esc_html_e( 'Page not found', 'inko-melbourne' ); ?></h1>
		<p><?php esc_html_e( 'That page does not exist.', 'inko-melbourne' ); ?></p>
		<a class="ik-hbtn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'inko-melbourne' ); ?></a>
	</div>
</main>
<?php
get_footer();
