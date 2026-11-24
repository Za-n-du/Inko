<?php
/**
 * Slide 11 — Contact call to action + footer.
 * "Tattoo Studio" removed from the title at the client's request; the
 * heading echoes Slide 01 (INKO in white, MELBOURNE in orange).
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_s = inko_content( 'studio' );
$inko_q = inko_content( 'quote' );
?>
<section class="slide slide--contact" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="contact-title">
	<div class="contact__glow" aria-hidden="true"></div>

	<div class="slide__inner contact__cta">
		<?php inko_eyebrow( $args['num'], __( 'Contact', 'inko-custom' ) ); ?>
		<h2 class="contact__title" id="contact-title" data-reveal="masks">
			<span class="mask"><span class="mask__in">INKO <span class="hero__accent">Melbourne</span></span></span>
		</h2>
		<p class="lede contact__text" data-reveal style="--d:.15s"><?php echo esc_html( $inko_q['text'] ); ?></p>
		<div class="contact__actions" data-reveal style="--d:.25s">
			<a class="btn btn--primary btn--lg" href="<?php echo esc_url( inko_section_url( 'quote' ) ); ?>"><span><?php echo esc_html( $inko_q['cta'] ); ?></span><?php echo inko_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<a class="btn btn--ghost btn--lg" href="mailto:<?php echo esc_attr( antispambot( $inko_s['email'] ) ); ?>"><span><?php echo esc_html( $inko_q['contact'] ); ?></span><?php echo inko_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
	</div>

	<?php get_template_part( 'template-parts/site-footer' ); ?>
</section>
