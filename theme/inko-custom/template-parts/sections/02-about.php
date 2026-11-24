<?php
/**
 * Slide 02 — Fine-Line & Realism (refined editorial design).
 *
 * Left: "02" indicator, serif heading "Fine-Line & / Realism Tattoos" with
 * "Tattoos" in italic orange, the exact client-approved introduction in its
 * own block with an orange vertical accent, and the "See Promos" CTA.
 * Right: two equal image cards (original INKO fine-line and realism photos,
 * different images) with category labels beneath, linking to the matching
 * Our Work slides.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_h = inko_content( 'hero' );
$inko_t = inko_content( 'styles' );

$inko_cards = array(
	array(
		'img'    => 'fineline-01',
		'alt'    => __( 'Fine line ornamental spine tattoo by INKO Melbourne', 'inko-custom' ),
		'label'  => $inko_t['fineline'],
		'target' => 'finelineshowcase',
		'pos'    => '50% 40%',
	),
	array(
		'img'    => 'realism-04',
		'alt'    => __( 'Realism crowned ape chest piece by INKO Melbourne', 'inko-custom' ),
		'label'  => $inko_t['realism'],
		'target' => 'realism-showcase',
		'pos'    => '50% 45%',
	),
);
?>
<section class="slide slide--about" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="about-title">
	<div class="slide__inner about__grid">
		<div class="about__copy">
			<?php inko_eyebrow( $args['num'], '' ); ?>

			<h2 class="about__title" id="about-title" data-reveal="masks">
				<span class="mask"><span class="mask__in"><?php echo esc_html( $inko_t['fineline'] ); ?> &amp;</span></span>
				<span class="mask"><span class="mask__in" style="--d:.1s"><?php echo esc_html( $inko_t['realism'] ); ?> <em class="accent"><?php echo esc_html( $inko_t['tattoos'] ); ?></em></span></span>
			</h2>

			<p class="about__intro" data-reveal style="--d:.2s"><?php echo esc_html( $inko_h['intro'] ); ?></p>

			<p class="about__cta" data-reveal style="--d:.3s">
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( inko_section_url( 'promotions' ) ); ?>"><span><?php echo esc_html( $inko_h['cta'] ); ?></span><?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</p>
		</div>

		<ul class="style-tiles" role="list">
			<?php foreach ( $inko_cards as $inko_k => $inko_card ) : ?>
				<li class="style-tile" data-reveal style="--d:<?php echo esc_attr( 0.15 + $inko_k * 0.15 ); ?>s">
					<a href="<?php echo esc_url( inko_section_url( $inko_card['target'] ) ); ?>">
						<span class="style-tile__media" style="--pos:<?php echo esc_attr( $inko_card['pos'] ); ?>">
							<?php echo inko_img( $inko_card['img'], $inko_card['alt'], '(min-width: 1024px) 26vw, (min-width: 768px) 44vw, 46vw', array( 'class' => 'style-tile__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</span>
						<span class="style-tile__label">
							<span class="style-tile__num"><?php echo esc_html( sprintf( '%02d', $inko_k + 1 ) ); ?></span>
							<span class="style-tile__name"><?php echo esc_html( $inko_card['label'] ); ?></span>
							<?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
