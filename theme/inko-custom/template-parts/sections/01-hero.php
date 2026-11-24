<?php
/**
 * Slide 01 — Hero: full-screen cinematic image slider.
 *
 * Three verified client photographs (realism back piece, Don at work,
 * realism sleeve) crossfade behind a centred heading. The text is the same
 * on every slide: "INKO MELBOURNE" + the client-approved introduction +
 * the original "See Promos" call to action.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_h = inko_content( 'hero' );
$inko_t = inko_content( 'styles' );

$inko_slides = array(
	array( 'hero-slide-1', __( 'Black and grey realism back piece tattooed at INKO Melbourne', 'inko-custom' ), '50% 30%' ),
	array( 'hero-slide-2', __( 'Don, founder of INKO Melbourne, tattooing in the studio', 'inko-custom' ), '50% 38%' ),
	array( 'hero-slide-3', __( 'Black and grey realism full sleeve tattooed at INKO Melbourne', 'inko-custom' ), '50% 40%' ),
);
$inko_count = count( $inko_slides );
?>
<section class="slide slide--hero" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="hero-title">

	<div class="hero-slider" data-hero-slider aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'INKO Melbourne tattoo work', 'inko-custom' ); ?>">
		<div class="hero-slider__track">
			<?php foreach ( $inko_slides as $inko_i => $inko_s ) : ?>
				<figure class="hero-slide<?php echo 0 === $inko_i ? ' is-active' : ''; ?>" data-hero-slide role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: slide number, 2: total */ __( '%1$d of %2$d', 'inko-custom' ), $inko_i + 1, $inko_count ) ); ?>"<?php echo 0 === $inko_i ? '' : ' aria-hidden="true"'; ?> style="--pos:<?php echo esc_attr( $inko_s[2] ); ?>">
					<?php
					echo inko_img( // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
						$inko_s[0],
						$inko_s[1],
						'100vw',
						array(
							'eager'         => 0 === $inko_i,
							'fetchpriority' => 0 === $inko_i ? 'high' : '',
							'class'         => 'hero-slide__img',
						)
					);
					?>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="hero-slider__shade" aria-hidden="true"></div>
	</div>

	<div class="hero__content">
		<p class="hero__eyebrow" data-enter style="--i:0">
			<span class="hero__rule" aria-hidden="true"></span>
			<span><?php echo esc_html( $inko_t['fineline'] . ' & ' . $inko_t['realism'] ); ?></span>
			<span class="hero__rule" aria-hidden="true"></span>
		</p>

		<h1 class="hero__title" id="hero-title">
			<span class="mask"><span class="mask__in" data-enter style="--i:1">INKO <span class="hero__accent">MELBOURNE</span></span></span>
		</h1>

		<p class="hero__intro" data-enter style="--i:2"><?php echo esc_html( $inko_h['intro'] ); ?></p>

		<p class="hero__cta" data-enter style="--i:3">
			<a class="btn btn--primary btn--sm" href="#promotions"><span><?php echo esc_html( $inko_h['cta'] ); ?></span><?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</p>
	</div>

	<button class="hero-slider__arrow hero-slider__arrow--prev" type="button" data-hero-prev aria-controls="home" aria-label="<?php esc_attr_e( 'Previous image', 'inko-custom' ); ?>">
		<svg viewBox="0 0 24 48" width="20" height="40" aria-hidden="true" focusable="false"><path d="M18 4 6 24l12 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
	</button>
	<button class="hero-slider__arrow hero-slider__arrow--next" type="button" data-hero-next aria-controls="home" aria-label="<?php esc_attr_e( 'Next image', 'inko-custom' ); ?>">
		<svg viewBox="0 0 24 48" width="20" height="40" aria-hidden="true" focusable="false"><path d="m6 4 12 20L6 44" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
	</button>

	<div class="hero-slider__dots" role="group" aria-label="<?php esc_attr_e( 'Choose image', 'inko-custom' ); ?>">
		<?php for ( $inko_i = 0; $inko_i < $inko_count; $inko_i++ ) : ?>
			<button class="hero-slider__dot<?php echo 0 === $inko_i ? ' is-active' : ''; ?>" type="button" data-hero-dot="<?php echo esc_attr( $inko_i ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Show image %d', 'inko-custom' ), $inko_i + 1 ) ); ?>"<?php echo 0 === $inko_i ? ' aria-current="true"' : ''; ?>></button>
		<?php endfor; ?>
	</div>
</section>
