<?php
/**
 * Fallback template (blog index, archives, search).
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
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) {
				the_post();
				echo '<article><h1><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h1>';
				the_excerpt();
				echo '</article>';
			}
			the_posts_pagination();
			?>
		<?php else : ?>
			<h1><?php esc_html_e( 'Nothing found', 'inko-melbourne' ); ?></h1>
			<p><?php esc_html_e( 'Try the menu above.', 'inko-melbourne' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
