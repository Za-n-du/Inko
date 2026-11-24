<?php
/**
 * Slide 08 — Google reviews.
 * Content: the original website's Google reviews section (heading, 5.0
 * rating and the four reviews it displays, verbatim). The heading is split
 * at its dash: "Trusted by Our Customers" + "Read Google Reviews" link.
 * Nothing here is invented; live Google data would need a Places API key.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_r  = inko_content( 'reviews' );
$inko_s  = inko_content( 'studio' );
$inko_tt = inko_split_last( $inko_r['title'] );
$inko_g  = inko_asset( 'icons/google-g.svg' );

$inko_stars = str_repeat( inko_icon( 'star' ), 5 );
?>
<section class="slide slide--reviews" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="reviews-title">
	<div class="slide__inner">
		<header class="slide-head slide-head--split reviews__head">
			<div>
				<?php inko_eyebrow( $args['num'], __( 'Reviews', 'inko-custom' ) ); ?>
				<h2 class="display" id="reviews-title" data-reveal="mask"><span><?php echo inko_heading( $inko_tt[0], $inko_tt[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			</div>

			<a class="rating" href="<?php echo esc_url( $inko_s['maps_url'] ); ?>" target="_blank" rel="noopener noreferrer" data-reveal style="--d:.15s">
				<span class="rating__brand">
					<img src="<?php echo esc_url( $inko_g ); ?>" width="32" height="32" alt="" loading="lazy">
					<span class="rating__label"><?php esc_html_e( 'Google Reviews', 'inko-custom' ); ?></span>
				</span>
				<span class="rating__row">
					<span class="rating__score"><?php echo esc_html( $inko_r['rating'] ); ?></span>
					<span class="rating__stars" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: rating */ __( 'Rated %s out of 5 on Google', 'inko-custom' ), $inko_r['rating'] ) ); ?>"><?php echo $inko_stars; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</span>
				<span class="rating__link"><?php echo esc_html( $inko_r['link'] ); ?> <?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="screen-reader-text"><?php esc_html_e( '(opens Google Maps in a new tab)', 'inko-custom' ); ?></span>
			</a>
		</header>

		<ul class="reviews" role="list">
			<?php foreach ( $inko_r['items'] as $inko_k => $inko_review ) : ?>
				<li class="review" data-reveal style="--d:<?php echo esc_attr( $inko_k * 0.1 ); ?>s">
					<figure>
						<div class="review__top">
							<span class="review__stars" role="img" aria-label="<?php esc_attr_e( '5 out of 5 stars', 'inko-custom' ); ?>"><?php echo $inko_stars; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<img class="review__g" src="<?php echo esc_url( $inko_g ); ?>" width="18" height="18" alt="<?php esc_attr_e( 'Google review', 'inko-custom' ); ?>" loading="lazy">
						</div>
						<blockquote class="review__text"><p>&ldquo;<?php echo esc_html( $inko_review['text'] ); ?>&rdquo;</p></blockquote>
						<figcaption class="review__name">
							<span class="review__avatar" aria-hidden="true"><?php echo esc_html( mb_substr( $inko_review['name'], 0, 1 ) ); ?></span>
							<?php echo esc_html( $inko_review['name'] ); ?>
						</figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
