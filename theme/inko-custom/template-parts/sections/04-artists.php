<?php
/**
 * Slide 04 — Artists: "Meet Our Artists" (full profile cards).
 *
 * Each card: verified portrait, name, alias, role, specialties, biography,
 * experience and Instagram (only when supplied in content.php), a portfolio
 * link and a "Book This Artist" button that opens the quotation form with
 * the artist preselected. Names, aliases, roles and bios are the original
 * website's (content.php); nothing is invented.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_a  = inko_content( 'artists' );
$inko_t  = inko_content( 'styles' );
$inko_tt = inko_split_last( $inko_a['title'] );
?>
<section class="slide slide--artists" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="artists-title">
	<div class="slide__inner">
		<header class="slide-head">
			<?php inko_eyebrow( $args['num'], __( 'Artists', 'inko-custom' ) ); ?>
			<h2 class="display" id="artists-title" data-reveal="mask"><span><?php echo inko_heading( $inko_tt[0], $inko_tt[1] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
		</header>

		<ul class="artists" role="list">
			<?php
			foreach ( $inko_a['items'] as $inko_k => $inko_artist ) :
				$inko_slug  = sanitize_title( $inko_artist['name'] );
				$inko_style = 'finelineshowcase' === $inko_artist['gallery'] ? $inko_t['fineline'] : $inko_t['realism'];
				?>
				<li class="artist" data-reveal style="--d:<?php echo esc_attr( ( $inko_k % 2 ) * 0.12 ); ?>s">
					<article class="artist__card" id="artist-<?php echo esc_attr( $inko_slug ); ?>" aria-labelledby="artist-<?php echo esc_attr( $inko_slug ); ?>-name">
						<figure class="artist__media">
							<?php
							/* translators: 1: artist name, 2: alias */
							$inko_alt = sprintf( __( '%1$s AKA %2$s, INKO Melbourne artist', 'inko-custom' ), $inko_artist['name'], $inko_artist['aka'] );
							echo inko_img( $inko_artist['img'], $inko_alt, '(min-width: 1024px) 18vw, (min-width: 560px) 34vw, 92vw', array( 'class' => 'artist__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							?>
						</figure>

						<div class="artist__body">
							<p class="artist__index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $inko_k + 1 ) ); ?></p>
							<h3 class="artist__name" id="artist-<?php echo esc_attr( $inko_slug ); ?>-name"><?php echo esc_html( $inko_artist['name'] ); ?></h3>
							<p class="artist__aka">AKA <?php echo esc_html( $inko_artist['aka'] ); ?></p>
							<p class="artist__role"><?php echo esc_html( $inko_artist['role'] ); ?></p>

							<dl class="artist__facts">
								<div class="artist__fact">
									<dt><?php esc_html_e( 'Specialties', 'inko-custom' ); ?></dt>
									<dd>
										<ul class="artist__tags" role="list">
											<?php foreach ( $inko_artist['specialties'] as $inko_sp ) : ?>
												<li><?php echo esc_html( $inko_sp ); ?></li>
											<?php endforeach; ?>
										</ul>
									</dd>
								</div>
								<?php if ( ! empty( $inko_artist['experience'] ) ) : ?>
									<div class="artist__fact">
										<dt><?php esc_html_e( 'Experience', 'inko-custom' ); ?></dt>
										<dd><?php echo esc_html( $inko_artist['experience'] ); ?></dd>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $inko_artist['instagram'] ) ) : ?>
									<div class="artist__fact">
										<dt><?php esc_html_e( 'Instagram', 'inko-custom' ); ?></dt>
										<dd><a href="<?php echo esc_url( $inko_artist['instagram'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( '@' . trim( wp_parse_url( $inko_artist['instagram'], PHP_URL_PATH ), '/' ) ); ?></a></dd>
									</div>
								<?php endif; ?>
							</dl>

							<p class="artist__bio"><?php echo inko_text( $inko_artist['bio'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></p>

							<div class="artist__actions">
								<?php if ( ! empty( $inko_artist['instagram'] ) ) : ?>
									<a class="artist__link" href="<?php echo esc_url( $inko_artist['instagram'] ); ?>" target="_blank" rel="noopener noreferrer">
										<?php esc_html_e( 'View Portfolio', 'inko-custom' ); ?> <?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %s: artist */ __( 'of %s on Instagram (opens in a new tab)', 'inko-custom' ), $inko_artist['name'] ) ); ?></span>
									</a>
								<?php else : ?>
									<a class="artist__link" href="<?php echo esc_url( inko_section_url( $inko_artist['gallery'] ) ); ?>">
										<?php
										/* translators: %s: tattoo style */
										echo esc_html( sprintf( __( 'View %s Work', 'inko-custom' ), $inko_style ) );
										?>
										<?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</a>
								<?php endif; ?>
								<a class="btn btn--primary btn--sm artist__book" href="<?php echo esc_url( inko_section_url( 'quote' ) ); ?>" data-book-artist="<?php echo esc_attr( $inko_artist['name'] ); ?>">
									<span><?php esc_html_e( 'Book This Artist', 'inko-custom' ); ?></span><?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<span class="screen-reader-text"><?php echo esc_html( $inko_artist['name'] ); ?></span>
								</a>
							</div>
						</div>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
