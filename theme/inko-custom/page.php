<?php
/**
 * Single page. Elementor-compatible: pages built with Elementor render
 * their content full width; regular pages get the editorial text column.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$inko_is_elementor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor();
?>

<main id="main" class="page-shell<?php echo $inko_is_elementor ? ' page-shell--elementor' : ''; ?>" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( ! $inko_is_elementor ) : ?>
				<header class="page-hero">
					<?php the_title( '<h1 class="page-hero__title">', '</h1>' ); ?>
				</header>
			<?php endif; ?>

			<div class="<?php echo $inko_is_elementor ? 'elementor-wrap' : 'page-content entry-content'; ?>">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
