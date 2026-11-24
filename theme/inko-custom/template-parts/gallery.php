<?php
/**
 * Shared portfolio gallery grid (Our Work: Fine Line / Realism).
 * Uniform 4:5 tiles, caption under each photo, lightbox per category.
 *
 * Args: group (lightbox group / category key), items: [ [image, caption, alt, focal point], … ].
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_group = $args['group'];
?>
<ul class="gallery gallery--<?php echo esc_attr( $inko_group ); ?>" role="list">
	<?php foreach ( $args['items'] as $inko_k => $inko_w ) : ?>
		<li class="gallery__item" data-reveal style="--d:<?php echo esc_attr( ( $inko_k % 3 ) * 0.1 ); ?>s; --pos:<?php echo esc_attr( $inko_w[3] ); ?>">
			<figure>
				<a class="gallery__link" href="<?php echo esc_url( inko_img_large( $inko_w[0] ) ); ?>" data-lightbox="<?php echo esc_attr( $inko_group ); ?>" data-caption="<?php echo esc_attr( $inko_w[2] ); ?>">
					<?php echo inko_img( $inko_w[0], $inko_w[2], '(min-width: 1024px) 30vw, (min-width: 768px) 30vw, 46vw', array( 'class' => 'gallery__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="screen-reader-text"><?php esc_html_e( '(enlarge)', 'inko-custom' ); ?></span>
				</a>
				<figcaption class="gallery__cap"><span class="gallery__num"><?php echo esc_html( sprintf( '%02d', $inko_k + 1 ) ); ?></span><?php echo esc_html( $inko_w[1] ); ?></figcaption>
			</figure>
		</li>
	<?php endforeach; ?>
</ul>
