<?php
/**
 * The fixed V3 header: logo (orange box on the hero slide), navigation, Book button, burger.
 *
 * @package inko-melbourne
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$ik_stack = inko_is_stack_page();
$ik_home  = $ik_stack ? '#home' : home_url( '/' );
$ik_box   = inko_logo( 'box' );
$ik_plain = inko_logo( 'plain' );
$ik_name  = inko_contact( 'name' );
$ik_book  = (string) get_theme_mod( 'inko_book_url', '#quote' );
if ( 0 === strpos( $ik_book, '#' ) && ! $ik_stack ) {
	$ik_book = home_url( '/' ) . $ik_book;
}
?>
<header class="ik-header<?php echo $ik_stack ? ' is-hero' : ''; ?>" id="ik-header" role="banner">
	<a class="ik-logo ik-logo--box" href="<?php echo esc_url( $ik_home ); ?>" aria-label="<?php echo esc_attr( $ik_name ); ?>"<?php echo $ik_stack ? '' : ' hidden'; ?>>
		<img src="<?php echo esc_url( $ik_box['url'] ); ?>" width="<?php echo (int) $ik_box['w']; ?>" height="<?php echo (int) $ik_box['h']; ?>" alt="<?php echo esc_attr( $ik_name ); ?>">
	</a>
	<a class="ik-logo ik-logo--plain" href="<?php echo esc_url( $ik_home ); ?>" aria-label="<?php echo esc_attr( $ik_name ); ?>"<?php echo $ik_stack ? ' hidden' : ''; ?>>
		<img src="<?php echo esc_url( $ik_plain['url'] ); ?>" width="<?php echo (int) $ik_plain['w']; ?>" height="<?php echo (int) $ik_plain['h']; ?>" alt="<?php echo esc_attr( $ik_name ); ?>">
	</a>
	<nav class="ik-nav" aria-label="<?php esc_attr_e( 'Primary', 'inko-melbourne' ); ?>">
		<?php foreach ( inko_nav_items() as $ik_item ) : ?>
			<div class="ik-nav__a"><a href="<?php echo esc_url( $ik_item[1] ); ?>"><?php echo esc_html( $ik_item[0] ); ?></a></div>
		<?php endforeach; ?>
	</nav>
	<div class="ik-actions">
		<a class="ik-hbtn" href="<?php echo esc_url( $ik_book ); ?>"><?php echo esc_html( get_theme_mod( 'inko_book_label', 'Book Now' ) ); ?></a>
		<button class="ik-burger" type="button" aria-expanded="false" aria-controls="ik-menu" aria-label="<?php esc_attr_e( 'Open menu', 'inko-melbourne' ); ?>"><i></i><i></i><i></i></button>
	</div>
</header>
