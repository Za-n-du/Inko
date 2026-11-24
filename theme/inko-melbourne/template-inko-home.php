<?php
/**
 * Template Name: INKO Home (editorial scrolling)
 * Template Post Type: page
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="ik-main">
	<?php
	while ( have_posts() ) {
		the_post();
		$elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true );
		if ( is_array( $elementor_data ) ) {
			$elementor_data = wp_json_encode( $elementor_data );
		}
		$has_editorial_elementor = is_string( $elementor_data ) && false !== strpos( $elementor_data, 'inko-home' );
		if ( inko_is_editorial_home() && ! $has_editorial_elementor ) {
			$homepage = get_template_directory() . '/homepage-content.html';
			if ( is_readable( $homepage ) ) {
				echo file_get_contents( $homepage ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme template fragment.
			}
		} else {
			the_content();
		}
	}
	?>
</main>
<?php
get_footer();
