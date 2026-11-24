<?php
/**
 * Slide 06 — Our Work: Fine-Line (original anchor #finelineshowcase).
 * Two original INKO fine-line photographs, large, captioned, with a
 * Fine-Line / Realism switcher shared with the next slide.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_t     = inko_content( 'styles' );
$inko_works = array(
	array( 'fineline-01', __( 'Ornamental spine', 'inko-custom' ), __( 'Fine line ornamental spine tattoo by INKO Melbourne', 'inko-custom' ) ),
	array( 'fineline-02', __( 'Ornamental shoulder', 'inko-custom' ), __( 'Fine line ornamental shoulder tattoo by INKO Melbourne', 'inko-custom' ) ),
);
?>
<section class="slide slide--work slide--fineline" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="fineline-title">
	<div class="slide__inner fineline__grid">
		<header class="work-head">
			<?php inko_eyebrow( $args['num'], __( 'Our Work', 'inko-custom' ) ); ?>
			<h2 class="display" id="fineline-title" data-reveal="mask"><span><?php echo inko_heading( $inko_t['fineline'], $inko_t['tattoos'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			<?php get_template_part( 'template-parts/work-tabs', null, array( 'current' => 'finelineshowcase' ) ); ?>
		</header>

		<ul class="fineline__works" role="list">
			<?php foreach ( $inko_works as $inko_k => $inko_w ) : ?>
				<li class="work" data-reveal style="--d:<?php echo esc_attr( 0.1 + $inko_k * 0.12 ); ?>s">
					<figure>
						<a class="work__link" href="<?php echo esc_url( inko_img_large( $inko_w[0] ) ); ?>" data-lightbox="fineline" data-caption="<?php echo esc_attr( $inko_w[2] ); ?>">
							<?php echo inko_img( $inko_w[0], $inko_w[2], '(min-width: 1024px) 28vw, (min-width: 768px) 45vw, 46vw', array( 'class' => 'work__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="screen-reader-text"><?php esc_html_e( '(enlarge)', 'inko-custom' ); ?></span>
						</a>
						<figcaption class="work__cap"><span class="work__num"><?php echo esc_html( sprintf( '%02d', $inko_k + 1 ) ); ?></span><?php echo esc_html( $inko_w[1] ); ?></figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
