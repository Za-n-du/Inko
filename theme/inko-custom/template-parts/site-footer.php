<?php
/**
 * Footer: original logo + approved introduction, Visit / Contact /
 * Follow (Instagram and Facebook only) / Explore columns, copyright.
 * Used by slide 11 on the front page and by footer.php elsewhere.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_c = inko_content();
$inko_s = $inko_c['studio'];

$inko_links = array(
	'promotions'       => __( 'Promotions', 'inko-custom' ),
	'artists'          => __( 'Artists', 'inko-custom' ),
	'finelineshowcase' => __( 'Our Work', 'inko-custom' ),
	'studio'           => __( 'Studio', 'inko-custom' ),
	'reviews'          => __( 'Reviews', 'inko-custom' ),
	'quote'            => $inko_c['quote']['cta'],
);
?>
<footer class="site-footer">
	<div class="site-footer__top">
		<div class="site-footer__brand">
			<a href="<?php echo esc_url( inko_section_url( 'home' ) ); ?>" aria-label="<?php echo esc_attr( $inko_s['brand'] . ' — ' . __( 'back to top', 'inko-custom' ) ); ?>">
				<img src="<?php echo esc_url( inko_asset( 'images/logo-inko.png' ) ); ?>" width="892" height="630" alt="<?php echo esc_attr( $inko_s['brand'] ); ?>" loading="lazy" decoding="async">
			</a>
			<p class="site-footer__intro"><?php echo esc_html( $inko_c['hero']['intro'] ); ?></p>
		</div>

		<div class="site-footer__cols">
			<div class="site-footer__col">
				<h2 class="site-footer__label"><?php esc_html_e( 'Visit', 'inko-custom' ); ?></h2>
				<p><?php echo esc_html( $inko_s['address_1'] ); ?><br><?php echo esc_html( $inko_s['address_2'] ); ?></p>
				<a class="site-footer__more" href="<?php echo esc_url( $inko_s['directions'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $inko_c['location']['directions'] ); ?> <?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( '(opens Google Maps in a new tab)', 'inko-custom' ); ?></span></a>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__label"><?php echo esc_html( $inko_c['footer']['contact_label'] ); ?></h2>
				<ul class="site-footer__list" role="list">
					<li><a href="mailto:<?php echo esc_attr( antispambot( $inko_s['email'] ) ); ?>"><?php echo esc_html( antispambot( $inko_s['email'] ) ); ?></a></li>
					<li><a href="tel:<?php echo esc_attr( $inko_s['phone_tel'] ); ?>"><?php echo esc_html( $inko_s['phone_display'] ); ?></a></li>
				</ul>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__label"><?php echo esc_html( $inko_c['follow']['title'] ); ?></h2>
				<?php inko_social_links( 'social social--footer', true ); ?>
			</div>

			<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Footer', 'inko-custom' ); ?>">
				<h2 class="site-footer__label"><?php esc_html_e( 'Explore', 'inko-custom' ); ?></h2>
				<ul class="site-footer__list" role="list">
					<?php foreach ( $inko_links as $inko_id => $inko_label ) : ?>
						<li><a href="<?php echo esc_url( inko_section_url( $inko_id ) ); ?>"><?php echo esc_html( $inko_label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
	</div>

	<div class="site-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $inko_s['brand'] ); ?></p>
		<a class="to-top" href="<?php echo esc_url( inko_section_url( 'home' ) ); ?>"><?php esc_html_e( 'Back to top', 'inko-custom' ); ?> <?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></a>
	</div>
</footer>
