<?php
/**
 * Full-screen mobile / burger menu (same links as the header, from Appearance > Menus).
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$ik_phone = inko_contact( 'phone' );
?>
<div class="ik-menu" id="ik-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'inko-melbourne' ); ?>">
	<nav aria-label="<?php esc_attr_e( 'Menu', 'inko-melbourne' ); ?>">
		<ol class="ik-menu__links">
			<?php foreach ( inko_nav_items() as $ik_item ) : ?>
				<li class="ik-menu__a"><a href="<?php echo esc_url( $ik_item[1] ); ?>"><?php echo esc_html( $ik_item[0] ); ?></a></li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<div class="ik-menu__foot">
		<a href="<?php echo esc_url( 'mailto:' . inko_contact( 'email' ) ); ?>"><?php echo esc_html( inko_contact( 'email' ) ); ?></a>
		<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $ik_phone ) ); ?>"><?php echo esc_html( $ik_phone ); ?></a>
		<div class="ik-socials-row">
			<?php foreach ( inko_socials() as $ik_s ) : ?>
				<a href="<?php echo esc_url( $ik_s['url'] ); ?>" aria-label="<?php echo esc_attr( $ik_s['label'] ); ?>"<?php echo '#' !== $ik_s['url'] ? ' target="_blank" rel="noopener"' : ''; ?>><img src="<?php echo esc_url( $ik_s['icon'] ); ?>" width="96" height="96" alt="" loading="lazy"></a>
			<?php endforeach; ?>
		</div>
	</div>
</div>
