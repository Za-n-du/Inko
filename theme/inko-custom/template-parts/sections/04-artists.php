<?php
/**
 * Slide 04 — Artists: "Meet Our Artists" (redesigned).
 *
 * Editorial 2 × 2 grid of horizontal artist cards: verified portrait on one
 * side; number, name, AKA, role and the original biography on the other.
 * Names, aliases, roles and bios are the original website's (content.php);
 * nothing is invented.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_a  = inko_content( 'artists' );
$inko_tt = inko_split_last( $inko_a['title'] );
?>
<section class="slide slide--artists" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="artists-title">
	<div class="slide__inner">
		<header class="slide-head">
			<?php inko_eyebrow( $args['num'], __( 'Artists', 'inko-custom' ) ); ?>
			<h2 class="display" id="artists-title" data-reveal="mask"><span><?php echo inko_heading( $inko_tt[0], $inko_tt[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
		</header>

		<ul class="artists" role="list">
			<?php foreach ( $inko_a['items'] as $inko_k => $inko_artist ) : ?>
				<li class="artist" data-reveal style="--d:<?php echo esc_attr( ( $inko_k % 2 ) * 0.12 ); ?>s">
					<article class="artist__card" aria-labelledby="artist-<?php echo esc_attr( sanitize_title( $inko_artist['name'] ) ); ?>">
						<figure class="artist__media">
							<?php
							/* translators: 1: artist name, 2: alias */
							$inko_alt = sprintf( __( '%1$s AKA %2$s, INKO Melbourne artist', 'inko-custom' ), $inko_artist['name'], $inko_artist['aka'] );
							echo inko_img( $inko_artist['img'], $inko_alt, '(min-width: 1024px) 18vw, (min-width: 560px) 34vw, 92vw', array( 'class' => 'artist__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							?>
						</figure>
						<div class="artist__body">
							<p class="artist__index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $inko_k + 1 ) ); ?></p>
							<h3 class="artist__name" id="artist-<?php echo esc_attr( sanitize_title( $inko_artist['name'] ) ); ?>"><?php echo esc_html( $inko_artist['name'] ); ?></h3>
							<p class="artist__aka">AKA <?php echo esc_html( $inko_artist['aka'] ); ?></p>
							<p class="artist__role"><?php echo esc_html( $inko_artist['role'] ); ?></p>
							<p class="artist__bio"><?php echo inko_text( $inko_artist['bio'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></p>
						</div>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
