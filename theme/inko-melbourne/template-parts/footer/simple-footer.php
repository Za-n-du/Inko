<?php
/**
 * Compact footer for ordinary pages/posts/404 (the homepage uses slide 12 as its footer, so this never renders there).
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$ik_phone = inko_contact( 'phone' );
?>
<footer class="ik-simple-footer" role="contentinfo">
	<strong><?php echo esc_html( inko_contact( 'name' ) ); ?></strong>
	<p><?php echo esc_html( inko_contact( 'address' ) ); ?></p>
	<p><a href="<?php echo esc_url( 'mailto:' . inko_contact( 'email' ) ); ?>"><?php echo esc_html( inko_contact( 'email' ) ); ?></a></p>
	<p><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $ik_phone ) ); ?>"><?php echo esc_html( $ik_phone ); ?></a></p>
</footer>
