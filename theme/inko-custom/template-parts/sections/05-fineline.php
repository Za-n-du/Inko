<?php
/**
 * Slide 06 — Our Work: Fine Line gallery (original anchor #finelineshowcase).
 * Only verified INKO fine line photographs; never mixed with Realism.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_t = inko_content( 'styles' );
$inko_g = inko_content( 'social' )['instagram'];
?>
<section class="slide slide--work slide--fineline" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="fineline-title">
	<div class="slide__inner work-layout work-layout--side">
		<header class="work-head">
			<?php inko_eyebrow( $args['num'], __( 'Our Work', 'inko-custom' ) ); ?>
			<h2 class="display" id="fineline-title" data-reveal="mask"><span><?php echo inko_heading( $inko_t['fineline'], __( 'Gallery', 'inko-custom' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			<?php get_template_part( 'template-parts/work-tabs', null, array( 'current' => 'finelineshowcase' ) ); ?>
			<a class="work-more" href="<?php echo esc_url( $inko_g['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-reveal><?php esc_html_e( 'More on Instagram', 'inko-custom' ); ?> <?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'inko-custom' ); ?></span></a>
		</header>

		<?php
		get_template_part(
			'template-parts/gallery',
			null,
			array(
				'group' => 'fineline',
				'items' => array(
					array( 'fineline-01', __( 'Ornamental spine', 'inko-custom' ), __( 'Fine line ornamental spine tattoo by INKO Melbourne', 'inko-custom' ), '50% 40%' ),
					array( 'fineline-02', __( 'Ornamental shoulder', 'inko-custom' ), __( 'Fine line ornamental shoulder tattoo by INKO Melbourne', 'inko-custom' ), '50% 45%' ),
				),
			)
		);
		?>
	</div>
</section>
