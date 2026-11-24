<?php
/**
 * Slide 05 — Location (placed after Artists at the client's request).
 * Content: original "📍INKO Melbourne Tattoo Studio" section (both
 * paragraphs, verbatim), the studio interiors and street photographs.
 * The original "Sweep to locate us." is replaced with "GET DIRECTIONS",
 * linking to Google Maps directions for the published address.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_l = inko_content( 'location' );
$inko_s = inko_content( 'studio' );

$inko_interior = array(
	array( 'studio-07', __( 'Tattoo room with purple lighting at INKO Melbourne', 'inko-custom' ) ),
	array( 'studio-08', __( 'Tattoo room with warm lighting at INKO Melbourne', 'inko-custom' ) ),
	array( 'studio-06', __( 'INKO Melbourne entrance on Cookson Street', 'inko-custom' ) ),
);
?>
<section class="slide slide--studio" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="studio-title">
	<div class="slide__inner studio__grid">
		<div class="studio__copy">
			<?php inko_eyebrow( $args['num'], __( 'Location', 'inko-custom' ) ); ?>
			<h2 class="display display--md" id="studio-title" data-reveal="mask">
				<span><span class="studio__pin" aria-hidden="true"><?php echo inko_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $inko_l['title'] ); ?></span>
			</h2>
			<?php foreach ( $inko_l['paragraphs'] as $inko_k => $inko_p ) : ?>
				<p class="lede" data-reveal style="--d:<?php echo esc_attr( 0.1 + $inko_k * 0.1 ); ?>s"><?php echo inko_text( $inko_p ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></p>
			<?php endforeach; ?>

			<div class="studio__address" data-reveal style="--d:.3s">
				<p>
					<strong><?php echo esc_html( $inko_s['address_1'] ); ?></strong><br>
					<?php echo esc_html( $inko_s['address_2'] ); ?>
				</p>
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $inko_s['directions'] ); ?>" target="_blank" rel="noopener noreferrer">
					<span><?php echo esc_html( $inko_l['directions'] ); ?></span><?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="screen-reader-text"><?php esc_html_e( '(opens Google Maps in a new tab)', 'inko-custom' ); ?></span>
				</a>
			</div>
		</div>

		<ul class="studio__mosaic" role="list">
			<?php foreach ( $inko_interior as $inko_k => $inko_img ) : ?>
				<li class="studio__tile studio__tile--<?php echo esc_attr( $inko_k + 1 ); ?>" data-reveal="image" style="--d:<?php echo esc_attr( $inko_k * 0.12 ); ?>s">
					<a href="<?php echo esc_url( inko_img_large( $inko_img[0] ) ); ?>" data-lightbox="studio" data-caption="<?php echo esc_attr( $inko_img[1] ); ?>">
						<?php echo inko_img( $inko_img[0], $inko_img[1], '(min-width: 1024px) 30vw, 90vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="slide__inner sweep">
		<div class="sweep__head" data-reveal>
			<a class="sweep__link" href="<?php echo esc_url( $inko_s['directions'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo inko_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( $inko_l['directions'] ); ?></span>
				<?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="screen-reader-text"><?php esc_html_e( '(opens Google Maps in a new tab)', 'inko-custom' ); ?></span>
			</a>
		</div>
		<ul class="sweep__track" role="list" tabindex="0" aria-label="<?php esc_attr_e( 'Street views around the studio', 'inko-custom' ); ?>" data-drag>
			<?php for ( $inko_i = 1; $inko_i <= 5; $inko_i++ ) : ?>
				<?php
				/* translators: %d: photo number */
				$inko_alt = sprintf( __( 'Street view near INKO Melbourne, Camberwell, photo %d of 5', 'inko-custom' ), $inko_i );
				?>
				<li class="sweep__item">
					<a href="<?php echo esc_url( inko_img_large( sprintf( 'studio-%02d', $inko_i ) ) ); ?>" data-lightbox="street" data-caption="<?php echo esc_attr( $inko_alt ); ?>">
						<?php echo inko_img( sprintf( 'studio-%02d', $inko_i ), $inko_alt, '(min-width: 1024px) 26vw, 70vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				</li>
			<?php endfor; ?>
		</ul>
	</div>
</section>
