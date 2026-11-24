<?php
/**
 * Slide 10 — Additional original content: "Ink Now, Pay Later."
 * (Afterpay, Klarna, Zip) and "Follow Us" (all five original social links).
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_p = inko_content( 'pay_later' );
$inko_f = inko_content( 'follow' );
?>
<section class="slide slide--extra" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-label="<?php echo esc_attr( $inko_p['title'] . ' / ' . $inko_f['title'] ); ?>">
	<div class="slide__inner extra__grid">
		<div class="extra__pay">
			<?php inko_eyebrow( $args['num'], __( 'Payment', 'inko-custom' ) ); ?>
			<?php $inko_tt = inko_split_last( $inko_p['title'] ); ?>
			<h2 class="display" data-reveal="mask"><span><?php echo inko_heading( $inko_tt[0], $inko_tt[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			<ul class="pay-logos" role="list">
				<?php
				foreach ( $inko_p['logos'] as $inko_k => $inko_logo ) :
					$inko_dim = wp_getimagesize( get_theme_file_path( 'assets/images/' . $inko_logo['file'] ) );
					?>
					<li class="pay-logo" data-reveal style="--d:<?php echo esc_attr( 0.1 + $inko_k * 0.08 ); ?>s">
						<img src="<?php echo esc_url( inko_asset( 'images/' . $inko_logo['file'] ) ); ?>" alt="<?php echo esc_attr( $inko_logo['label'] ); ?>" loading="lazy" decoding="async" width="<?php echo esc_attr( $inko_dim ? $inko_dim[0] : 500 ); ?>" height="<?php echo esc_attr( $inko_dim ? $inko_dim[1] : 200 ); ?>">
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="extra__follow" id="follow">
			<p class="eyebrow" data-reveal><span class="eyebrow-label">@inko_melbourne</span></p>
			<?php $inko_tt = inko_split_last( $inko_f['title'] ); ?>
			<h2 class="display" data-reveal="mask"><span><?php echo inko_heading( $inko_tt[0], $inko_tt[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			<?php inko_social_links( 'social social--list', true ); ?>
		</div>
	</div>
</section>
