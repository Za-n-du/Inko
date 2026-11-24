<?php
/**
 * Slide 07 — Our Work: Realism (original anchor #realism-showcase).
 * Six original INKO realism photographs in an editorial mosaic
 * (equal-height rows, varied widths), captioned, opening in the lightbox.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_t     = inko_content( 'styles' );
$inko_works = array(
	array( 'realism-01', __( 'Back piece', 'inko-custom' ), __( 'Black and grey realism back piece by INKO Melbourne', 'inko-custom' ), '50% 35%' ),
	array( 'realism-02', __( 'Winged chest piece', 'inko-custom' ), __( 'Realism winged chest piece with cross by INKO Melbourne', 'inko-custom' ), '50% 40%' ),
	array( 'realism-03', __( 'Crowned character, leg', 'inko-custom' ), __( 'Black and grey realism leg tattoo with crowned character by INKO Melbourne', 'inko-custom' ), '50% 45%' ),
	array( 'realism-05', __( 'Statue portrait chest piece', 'inko-custom' ), __( 'Realism chest piece with statue portrait and lettering by INKO Melbourne', 'inko-custom' ), '50% 40%' ),
	array( 'realism-04', __( 'Crowned ape chest piece', 'inko-custom' ), __( 'Realism crowned ape chest piece by INKO Melbourne', 'inko-custom' ), '50% 45%' ),
	array( 'realism-06', __( 'Full sleeve', 'inko-custom' ), __( 'Black and grey realism full sleeve by INKO Melbourne', 'inko-custom' ), '50% 40%' ),
);
?>
<section class="slide slide--work slide--realism" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="realism-title">
	<div class="slide__inner">
		<header class="work-head work-head--row">
			<div>
				<?php inko_eyebrow( $args['num'], __( 'Our Work', 'inko-custom' ) ); ?>
				<h2 class="display" id="realism-title" data-reveal="mask"><span><?php echo inko_heading( $inko_t['realism'], $inko_t['tattoos'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			</div>
			<?php get_template_part( 'template-parts/work-tabs', null, array( 'current' => 'realism-showcase' ) ); ?>
		</header>

		<ul class="realism__grid" role="list">
			<?php foreach ( $inko_works as $inko_k => $inko_w ) : ?>
				<li class="work work--m<?php echo esc_attr( $inko_k + 1 ); ?>" data-reveal style="--d:<?php echo esc_attr( ( $inko_k % 3 ) * 0.1 ); ?>s; --pos:<?php echo esc_attr( $inko_w[3] ); ?>">
					<a class="work__link" href="<?php echo esc_url( inko_img_large( $inko_w[0] ) ); ?>" data-lightbox="realism" data-caption="<?php echo esc_attr( $inko_w[2] ); ?>">
						<?php echo inko_img( $inko_w[0], $inko_w[2], '(min-width: 1024px) 34vw, (min-width: 768px) 45vw, 46vw', array( 'class' => 'work__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="work__overlay" aria-hidden="true"><span class="work__num"><?php echo esc_html( sprintf( '%02d', $inko_k + 1 ) ); ?></span><?php echo esc_html( $inko_w[1] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
