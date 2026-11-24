<?php
/**
 * Front page — the 11-slide layered homepage.
 *
 * Each slide lives in template-parts/sections/. Slides are full-height
 * sticky panels: the next one rises over the previous one while the
 * previous stays pinned behind it (see assets/js/main.js → initStack).
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$inko_slides = inko_slides();
$inko_total  = count( $inko_slides );
?>

<main id="main" class="stack" tabindex="-1">
	<?php
	$inko_n = 0;
	foreach ( $inko_slides as $inko_id => $inko_slide ) {
		++$inko_n;
		get_template_part(
			'template-parts/sections/' . $inko_slide[0],
			null,
			array(
				'id'    => $inko_id,
				'num'   => $inko_n,
				'total' => $inko_total,
				'label' => $inko_slide[1],
			)
		);
	}
	?>
</main>

<nav class="slide-rail" aria-label="<?php esc_attr_e( 'Homepage sections', 'inko-custom' ); ?>">
	<ol>
		<?php
		$inko_n = 0;
		foreach ( $inko_slides as $inko_id => $inko_slide ) :
			++$inko_n;
			?>
			<li>
				<a href="#<?php echo esc_attr( $inko_id ); ?>" data-rail="<?php echo esc_attr( $inko_id ); ?>" title="<?php echo esc_attr( $inko_slide[1] ); ?>">
					<span class="slide-rail__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $inko_n ) ); ?></span>
					<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: 1: section number, 2: section name */ __( 'Section %1$d: %2$s', 'inko-custom' ), $inko_n, $inko_slide[1] ) ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>

<?php
get_template_part( 'template-parts/lightbox' );
get_footer();
