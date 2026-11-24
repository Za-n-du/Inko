<?php
/**
 * Slide 03 — Services / pricing: "OUR PROMOTIONS".
 * Content: the original section heading + the client's seven promotion
 * flyers. Captions repeat exactly what each flyer prints.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_p = inko_content( 'promotions' );
$inko_q = inko_content( 'quote' );
?>
<section class="slide slide--promos" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="promos-title">
	<div class="ghost-num" aria-hidden="true" data-depth="0.15"><?php echo esc_html( sprintf( '%02d', $args['num'] ) ); ?></div>

	<div class="slide__inner">
		<header class="slide-head slide-head--split">
			<div>
				<?php inko_eyebrow( $args['num'], __( 'Promotions', 'inko-custom' ) ); ?>
				<?php $inko_tt = inko_split_last( ucwords( strtolower( $inko_p['title'] ) ) ); ?>
				<h2 class="display" id="promos-title" data-reveal="mask"><span><?php echo inko_heading( $inko_tt[0], $inko_tt[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			</div>
			<div class="carousel-controls" data-reveal style="--d:.2s">
				<button class="icon-btn" type="button" data-carousel-prev="promo-track" aria-label="<?php esc_attr_e( 'Previous promotions', 'inko-custom' ); ?>"><?php echo inko_icon( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<span class="carousel-progress" aria-hidden="true"><i data-carousel-bar="promo-track"></i></span>
				<button class="icon-btn" type="button" data-carousel-next="promo-track" aria-label="<?php esc_attr_e( 'Next promotions', 'inko-custom' ); ?>"><?php echo inko_icon( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</div>
		</header>

		<ul class="promo-track" id="promo-track" role="list" tabindex="0" aria-label="<?php echo esc_attr( $inko_p['title'] ); ?>">
			<?php
			foreach ( $inko_p['items'] as $inko_k => $inko_item ) :
				/* translators: 1: tattoo, 2: sittings, 3: old price, 4: new price */
				$inko_alt = sprintf( __( '%1$s promotion flyer: %2$s, was %3$s, now %4$s', 'inko-custom' ), $inko_item['title'], strtolower( $inko_item['sittings'] ), $inko_item['was'], $inko_item['now'] );
				?>
				<li class="promo" data-reveal style="--d:<?php echo esc_attr( min( $inko_k, 4 ) * 0.08 ); ?>s">
					<a class="promo__media" href="<?php echo esc_url( inko_img_large( $inko_item['img'] ) ); ?>" data-lightbox="promos" data-caption="<?php echo esc_attr( $inko_alt ); ?>">
						<?php echo inko_img( $inko_item['img'], $inko_alt, '(min-width: 1440px) 400px, (min-width: 768px) 40vw, 84vw', array( 'class' => 'promo__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
					<div class="promo__meta">
						<h3 class="promo__title"><?php echo esc_html( $inko_item['title'] ); ?></h3>
						<p class="promo__sittings"><?php echo esc_html( $inko_item['sittings'] ); ?></p>
						<p class="promo__price">
							<s><span class="screen-reader-text"><?php esc_html_e( 'Was', 'inko-custom' ); ?> </span><?php echo esc_html( $inko_item['was'] ); ?></s>
							<strong><span class="screen-reader-text"><?php esc_html_e( 'Now', 'inko-custom' ); ?> </span><?php echo esc_html( $inko_item['now'] ); ?></strong>
						</p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="slide-foot" data-reveal>
			<p class="slide-foot__text"><?php echo esc_html( $inko_q['text'] ); ?></p>
			<a class="btn btn--primary" href="#quote"><span><?php echo esc_html( $inko_q['cta'] ); ?></span><?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
	</div>
</section>
