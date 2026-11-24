<?php
/**
 * Fallback template: blog index, archives, search.
 *
 * The homepage uses front-page.php.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="page-shell" tabindex="-1">
	<header class="page-hero">
		<h1 class="page-hero__title">
			<?php
			if ( is_home() && ! is_front_page() ) {
				single_post_title();
			} elseif ( is_search() ) {
				/* translators: %s: search query */
				printf( esc_html__( 'Search: %s', 'inko-custom' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				bloginfo( 'name' );
			}
			?>
		</h1>
	</header>

	<div class="page-content page-content--list">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content' );
			endwhile;

			the_posts_pagination(
				array(
					'prev_text' => esc_html__( 'Previous', 'inko-custom' ),
					'next_text' => esc_html__( 'Next', 'inko-custom' ),
				)
			);
		else :
			echo '<p>' . esc_html__( 'Nothing found.', 'inko-custom' ) . '</p>';
		endif;
		?>
	</div>
</main>

<?php
get_footer();
