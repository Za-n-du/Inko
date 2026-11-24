<?php
/**
 * Single post.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="page-shell" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="page-hero">
				<p class="page-hero__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
				<?php the_title( '<h1 class="page-hero__title">', '</h1>' ); ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="page-feature"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>

			<div class="page-content entry-content">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>

			<nav class="post-nav" aria-label="<?php esc_attr_e( 'Posts', 'inko-custom' ); ?>">
				<?php
				previous_post_link( '<span class="post-nav__prev">%link</span>' );
				next_post_link( '<span class="post-nav__next">%link</span>' );
				?>
			</nav>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
