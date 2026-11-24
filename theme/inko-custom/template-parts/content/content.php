<?php
/**
 * Post summary in lists.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="entry-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'medium_large' ); ?></a>
	<?php endif; ?>
	<div class="entry-card__body">
		<p class="entry-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<?php the_title( '<h2 class="entry-card__title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
		<?php the_excerpt(); ?>
	</div>
</article>
