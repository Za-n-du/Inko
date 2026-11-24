<?php
/**
 * Slide 07 — Our Work: Realism gallery (original anchor #realism-showcase).
 * Only verified INKO realism photographs; never mixed with Fine Line.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_t = inko_content( 'styles' );
$inko_g = inko_content( 'social' )['instagram'];
?>
<section class="slide slide--work slide--realism" id="<?php echo esc_attr( $args['id'] ); ?>" data-slide aria-labelledby="realism-title">
	<div class="slide__inner work-layout">
		<header class="work-head work-head--row">
			<div>
				<?php inko_eyebrow( $args['num'], __( 'Our Work', 'inko-custom' ) ); ?>
				<h2 class="display" id="realism-title" data-reveal="mask"><span><?php echo inko_heading( $inko_t['realism'], __( 'Gallery', 'inko-custom' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></span></h2>
			</div>
			<div class="work-head__nav">
				<?php get_template_part( 'template-parts/work-tabs', null, array( 'current' => 'realism-showcase' ) ); ?>
				<a class="work-more" href="<?php echo esc_url( $inko_g['url'] ); ?>" target="_blank" rel="noopener noreferrer" data-reveal><?php esc_html_e( 'More on Instagram', 'inko-custom' ); ?> <?php echo inko_icon( 'arrow-ur' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'inko-custom' ); ?></span></a>
			</div>
		</header>

		<?php
		get_template_part(
			'template-parts/gallery',
			null,
			array(
				'group' => 'realism',
				'items' => array(
					array( 'realism-01', __( 'Back piece', 'inko-custom' ), __( 'Black and grey realism back piece by INKO Melbourne', 'inko-custom' ), '50% 35%' ),
					array( 'realism-02', __( 'Winged chest piece', 'inko-custom' ), __( 'Realism winged chest piece with cross by INKO Melbourne', 'inko-custom' ), '50% 40%' ),
					array( 'realism-03', __( 'Crowned character, leg', 'inko-custom' ), __( 'Black and grey realism leg tattoo with crowned character by INKO Melbourne', 'inko-custom' ), '50% 45%' ),
					array( 'realism-04', __( 'Crowned ape chest piece', 'inko-custom' ), __( 'Realism crowned ape chest piece by INKO Melbourne', 'inko-custom' ), '50% 45%' ),
					array( 'realism-05', __( 'Statue portrait chest piece', 'inko-custom' ), __( 'Realism chest piece with statue portrait and lettering by INKO Melbourne', 'inko-custom' ), '50% 40%' ),
					array( 'realism-06', __( 'Full sleeve', 'inko-custom' ), __( 'Black and grey realism full sleeve by INKO Melbourne', 'inko-custom' ), '50% 40%' ),
				),
			)
		);
		?>
	</div>
</section>
