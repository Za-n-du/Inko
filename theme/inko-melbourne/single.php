<?php
/**
 * Single post.
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
		<?php
		while ( have_posts() ) {
			the_post();
			echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
			the_content();
		}
		?>
	</div>
</main>
<?php
get_footer();
