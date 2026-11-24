<?php
/**
 * Our Work category switcher (Fine-Line / Realism), shared by slides 06–07.
 *
 * @package INKO_Melbourne_Custom
 */

defined( 'ABSPATH' ) || exit;

$inko_t    = inko_content( 'styles' );
$inko_cur  = isset( $args['current'] ) ? $args['current'] : '';
$inko_tabs = array(
	'finelineshowcase' => $inko_t['fineline'],
	'realism-showcase' => $inko_t['realism'],
);
?>
<nav class="work-tabs" aria-label="<?php esc_attr_e( 'Our Work categories', 'inko-custom' ); ?>" data-reveal style="--d:.15s">
	<?php foreach ( $inko_tabs as $inko_id => $inko_label ) : ?>
		<a class="work-tabs__link" href="<?php echo esc_url( inko_section_url( $inko_id ) ); ?>"<?php echo $inko_id === $inko_cur ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $inko_label ); ?></a>
	<?php endforeach; ?>
</nav>
